<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Infolists;
use Illuminate\Support\Facades\DB;


class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        $order = $this->record;
        
        $actions = [
            EditAction::make(),
        ];

        // Payment review actions (only show when payment is pending)
        if ($order->payment_status === 'pending') {
            $actions[] = Action::make('acceptPayment')
                ->label('Accept Payment')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Accept Payment Proof')
                ->modalDescription('Are you sure you want to accept this payment proof? This will mark the payment as paid and set fulfillment to fulfilled.')
                ->form([
                    Textarea::make('adminNotes')
                        ->label('Admin Notes (Optional)')
                        ->rows(3)
                        ->placeholder('Add any notes about this payment verification...'),
                ])
                ->action(function (array $data) use ($order) {
                    try {
                        DB::transaction(function () use ($order, $data) {
                            $order->acceptPayment(auth()->id());
                            
                            if (!empty($data['adminNotes'])) {
                                $order->admin_notes = $data['adminNotes'];
                                $order->save();
                            }
                        });

                        $order->refresh();

                        Notification::make()
                            ->title('Payment Accepted')
                            ->success()
                            ->body('Payment proof has been accepted. Order fulfillment status updated to fulfilled.')
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error')
                            ->danger()
                            ->body($e->getMessage())
                            ->send();
                    }
                });

            $actions[] = Action::make('rejectPayment')
                ->label('Reject Payment')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Reject Payment Proof')
                ->modalDescription('Are you sure you want to reject this payment proof? This will mark the payment as failed and cancel the order.')
                ->form([
                    Textarea::make('adminNotes')
                        ->label('Admin Notes (Required)')
                        ->rows(3)
                        ->required()
                        ->placeholder('Please provide a reason for rejecting this payment...'),
                ])
                ->action(function (array $data) use ($order) {
                    try {
                        DB::transaction(function () use ($order, $data) {
                            $order->rejectPayment(auth()->id());
                            
                            if (!empty($data['adminNotes'])) {
                                $order->admin_notes = $data['adminNotes'];
                                $order->save();
                            }
                        });

                        $order->refresh();

                        Notification::make()
                            ->title('Payment Rejected')
                            ->warning()
                            ->body('Payment proof has been rejected. Order has been cancelled.')
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error')
                            ->danger()
                            ->body($e->getMessage())
                            ->send();
                    }
                });
        }

        // Fulfillment status update action (only show when payment is paid)
        if ($order->canProgressFulfillment()) {
            $nextStatus = match($order->fulfillment_status) {
                'fulfilled' => 'shipped',
                'shipped' => 'delivered',
                default => 'fulfilled',
            };

            $actions[] = Action::make('updateFulfillment')
                ->label('Update Fulfillment')
                ->icon('heroicon-o-truck')
                ->color('info')
                ->form([
                    Select::make('fulfillmentStatus')
                        ->label('Fulfillment Status')
                        ->options([
                            'fulfilled' => 'Fulfilled',
                            'shipped' => 'Shipped',
                            'delivered' => 'Delivered',
                        ])
                        ->default($nextStatus)
                        ->required()
                        ->disabled(fn() => $order->payment_status !== 'paid'),
                ])
                ->action(function (array $data) use ($order) {
                    try {
                        $order->progressFulfillment($data['fulfillmentStatus']);

                        $order->refresh();

                        Notification::make()
                            ->title('Fulfillment Updated')
                            ->success()
                            ->body("Fulfillment status updated to: " . str($data['fulfillmentStatus'])->headline())
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error')
                            ->danger()
                            ->body($e->getMessage())
                            ->send();
                    }
                });
        }

        // Refund action (only show when payment is paid)
        if ($order->payment_status === 'paid' && $order->fulfillment_status !== 'delivered') {
            $actions[] = Action::make('processRefund')
                ->label('Process Refund')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Process Refund')
                ->modalDescription('This will mark the payment as refunded and cancel the order if not yet delivered.')
                ->form([
                    \Filament\Forms\Components\TextInput::make('refundedAmount')
                        ->label('Refunded Amount')
                        ->numeric()
                        ->default(fn() => $order->total_amount)
                        ->required()
                        ->prefix('PHP'),
                    Textarea::make('adminNotes')
                        ->label('Admin Notes (Optional)')
                        ->rows(3)
                        ->placeholder('Add any notes about this refund...'),
                ])
                ->action(function (array $data) use ($order) {
                    try {
                        DB::transaction(function () use ($order, $data) {
                            $order->processRefund($data['refundedAmount']);
                            
                            if (!empty($data['adminNotes'])) {
                                $order->admin_notes = ($order->admin_notes ? $order->admin_notes . "\n\n" : '') . $data['adminNotes'];
                                $order->save();
                            }
                        });

                        $order->refresh();

                        Notification::make()
                            ->title('Refund Processed')
                            ->success()
                            ->body('Payment has been marked as refunded.')
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error')
                            ->danger()
                            ->body($e->getMessage())
                            ->send();
                    }
                });
        }

        return $actions;
    }
}
