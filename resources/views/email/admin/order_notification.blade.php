<!DOCTYPE html>
<html>
<body>
    <h2>Thank you for your order!</h2>
    <p>Order Number: {{ $order->order_number }}</p>
    <p>Status: {{ $order->status }}</p>

    <h2>Items:</h2>
    <ul>
    @foreach ($order->orderItems as $item)
    <li>{{ $item->product_name }} quantity: {{ $item->quantity }}|| price: {{ $item->unit_price }}</li>
    <p>{{$item->line_total}}</p>
    @endforeach
    </ul>
    <h3>Subtotal: {{$order->subtotal}}
    <h2>Total Amount:{{$order->total_amount}}</h2>

</body>
</html>

