<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\InventoryMovement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'id' => (string) Str::uuid(),
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $users = User::factory(10)->create();

        // Create clothing categories
        $shirtsCategory = Category::create([
            'name' => 'Shirts & Tops',
            'slug' => 'shirts-tops',
            'description' => 'Branded shirts, blouses, and tops from thrift finds',
            'is_active' => true,
        ]);

        $categories = collect([$shirtsCategory]);

        // Create specific products
        $shirtProducts = $this->createShirtProducts($shirtsCategory);

        $products = $shirtProducts;

        // Create customer orders with richer timelines for dashboard charts
        $orders = $users->flatMap(function ($user) use ($products) {
            return Order::factory(fake()->numberBetween(2, 4))->create([
                'user_id' => $user->id,
            ]);
        });

        $guestOrders = Order::factory(10)->create([
            'user_id' => null,
        ]);

        $orders->merge($guestOrders)->each(function (Order $order) use ($products) {
            $this->attachItemsToOrder($order, $products);
        });

        // Create user addresses
        $users->each(function ($user) {
            Address::factory(fake()->numberBetween(1, 2))->create([
                'user_id' => $user->id
            ]);
        });

        // Create inventory movements
            $products->each(function ($product) {
            InventoryMovement::factory(1)->create([
                'product_id' => $product->id,
            ]);
        });

        $this->command->info('✅ Clothing store data seeded successfully!');
    }

    private function createShirtProducts(Category $category): \Illuminate\Support\Collection
    {
        $shirtData = [
            [
                'name' => 'Chicago Bulls',
                'description' => 'Bershka x NBA Chicago Bulls collaboration shirt in gray. Size L with oversized fit, featuring soft and lightweight cloth. Perfect for casual streetwear. Measurements: 28x22 inches.',
                'short_description' => 'Bershka x NBA Chicago Bulls gray shirt, oversized fit',
                'base_price' => 700.00,
                'cost_price' => 200.00,
                'brand' => 'bershka',
                'condition' => 'Excellent',
                'source' => 'Thrift Store',
                'type' => 'shirt',
                'original_price' => 1500.00,
                'color' => 'Gray',
                'image_name' => 'bulls.jpg',
                'tags' => ['NBA', 'basketball', 'sports', 'oversized', 'streetwear', 'collaboration'],
            ],
            [
                'name' => 'Wilderness',
                'description' => 'GAP Wilderness shirt in white with a boxy fit. Size M featuring soft and lightweight fabric. Classic casual style perfect for everyday wear. Measurements: 26x19 inches.',
                'short_description' => 'GAP Wilderness white shirt, boxy fit',
                'base_price' => 700.00,
                'cost_price' => 200.00,
                'brand' => 'gap',
                'condition' => 'Excellent',
                'source' => 'Thrift Store',
                'type' => 'shirt',
                'original_price' => 1800.00,
                'color' => 'White',
                'image_name' => 'gap.jpg',
                'tags' => ['casual', 'everyday', 'classic', 'boxy fit', 'white'],
            ],
            [
                'name' => 'Netflix x One Piece',
                'description' => 'Netflix x One Piece collaboration shirt in black. Size XL with oversized fit and soft, lightweight fabric. Perfect for anime fans. Measurements: 30x24 inches.',
                'short_description' => 'Netflix x One Piece black shirt, oversized fit',
                'base_price' => 600.00,
                'cost_price' => 180.00,
                'brand' => 'generic',
                'condition' => 'Excellent',
                'source' => 'Thrift Store',
                'type' => 'shirt',
                'original_price' => 1200.00,
                'color' => 'Black',
                'image_name' => 'onepiece.jpg',
                'tags' => ['anime', 'collaboration', 'oversized', 'black', 'pop culture'],
            ],
            [
                'name' => 'LA Lakers',
                'description' => 'NBA x Cotton On LA Lakers shirt in black. Size XL with oversized fit, featuring soft and lightweight cloth. Perfect for Lakers fans. Measurements: 30x24 inches.',
                'short_description' => 'NBA x Cotton On LA Lakers black shirt',
                'base_price' => 700.00,
                'cost_price' => 200.00,
                'brand' => 'cotton on',
                'condition' => 'Excellent',
                'source' => 'Thrift Store',
                'type' => 'shirt',
                'original_price' => 1500.00,
                'color' => 'Black',
                'image_name' => 'LA.jpg',
                'tags' => ['NBA', 'basketball', 'sports', 'oversized', 'black', 'collaboration'],
            ],
            [
                'name' => 'Milwaukee Bucks',
                'description' => 'NBA x Cotton On Milwaukee Bucks shirt in green. Size L with oversized fit and soft, heavy cloth. Great quality for basketball fans. Measurements: 28x21 inches.',
                'short_description' => 'NBA x Cotton On Milwaukee Bucks green shirt',
                'base_price' => 650.00,
                'cost_price' => 190.00,
                'brand' => 'cotton on',
                'condition' => 'Excellent',
                'source' => 'Thrift Store',
                'type' => 'shirt',
                'original_price' => 1500.00,
                'color' => 'Green',
                'image_name' => 'bucks.jpg',
                'tags' => ['NBA', 'basketball', 'sports', 'oversized', 'green', 'collaboration'],
            ],
            [
                'name' => 'Brooklyn Nets',
                'description' => 'NBA x Cotton On Brooklyn Nets shirt in black. Size XL with oversized fit, featuring soft and heavy cloth. Premium quality for Nets supporters. Measurements: 29x22 inches.',
                'short_description' => 'NBA x Cotton On Brooklyn Nets black shirt',
                'base_price' => 650.00,
                'cost_price' => 190.00,
                'brand' => 'cotton on',
                'condition' => 'Excellent',
                'source' => 'Thrift Store',
                'type' => 'shirt',
                'original_price' => 1500.00,
                'color' => 'Black',
                'image_name' => 'nets.jpg',
                'tags' => ['NBA', 'basketball', 'sports', 'oversized', 'black', 'premium'],
            ],
            [
                'name' => 'Nirvana',
                'description' => 'Vintage Nirvana band shirt in brown. Size XL with oversized fit and soft, lightweight fabric. Perfect for grunge and rock music fans. Measurements: 29x24 inches.',
                'short_description' => 'Nirvana brown vintage band shirt, oversized fit',
                'base_price' => 600.00,
                'cost_price' => 180.00,
                'brand' => 'generic',
                'condition' => 'Very Good',
                'source' => 'Thrift Store',
                'type' => 'shirt',
                'original_price' => 1000.00,
                'color' => 'Brown',
                'image_name' => 'nirvana.jpg',
                'tags' => ['vintage', 'band', 'music', 'grunge', 'rock', 'oversized'],
            ],
            [
                'name' => 'APUNVS',
                'description' => 'Bape APUNVS shirt in black. Size L with boxy fit and soft, lightweight cloth. Authentic streetwear style from the iconic Japanese brand. Measurements: 26x20 inches.',
                'short_description' => 'Bape APUNVS black shirt, boxy fit',
                'base_price' => 600.00,
                'cost_price' => 180.00,
                'brand' => 'bape',
                'condition' => 'Excellent',
                'source' => 'Thrift Store',
                'type' => 'shirt',
                'original_price' => 2500.00,
                'color' => 'Black',
                'image_name' => 'bape.jpg',
                'tags' => ['streetwear', 'premium', 'japanese', 'boxy fit', 'black', 'authentic'],
            ],
            [
                'name' => 'Jeans Shirt',
                'description' => 'DKNY Jeans shirt in violet, from the women\'s selection. Size L with oversized fit and soft, lightweight fabric. Stylish and comfortable casual wear. Measurements: 24x17 inches.',
                'short_description' => 'DKNY Jeans violet shirt, women\'s oversized fit',
                'base_price' => 600.00,
                'cost_price' => 180.00,
                'brand' => 'dkny',
                'condition' => 'Excellent',
                'source' => 'Thrift Store',
                'type' => 'shirt',
                'original_price' => 1800.00,
                'color' => 'Violet',
                'image_name' => 'dkny.jpg',
                'tags' => ['women', 'premium', 'casual', 'oversized', 'violet', 'designer'],
            ],
            [
                'name' => 'Soul Searching',
                'description' => 'NEXT Soul Searching shirt in black, from the women\'s selection. Size L with loose fit and soft, lightweight cloth. Comfortable everyday style. Measurements: 24x19 inches.',
                'short_description' => 'NEXT Soul Searching black shirt, women\'s loose fit',
                'base_price' => 350.00,
                'cost_price' => 100.00,
                'brand' => 'next',
                'condition' => 'Good',
                'source' => 'Thrift Store',
                'type' => 'shirt',
                'original_price' => 800.00,
                'color' => 'Black',
                'image_name' => 'soulSearching.jpg',
                'tags' => ['women', 'casual', 'everyday', 'loose fit', 'black', 'comfortable'],
            ],
        ];

        return collect($shirtData)->map(function ($productData) use ($category) {
            return $this->createProduct($productData, $category);
        });
    }

    private function getProductSizeData(string $productName): ?array
    {
        $sizeMap = [
            'Bershka x NBA Chicago Bulls Shirt' => ['size' => 'L'],
            'GAP Wilderness Shirt' => ['size' => 'M'],
            'Netflix x One Piece Shirt' => ['size' => 'XL'],
            'NBA x Cotton On LA Lakers Shirt' => ['size' => 'XL'],
            'NBA x Cotton On Milwaukee Bucks Shirt' => ['size' => 'L'],
            'NBA x Cotton On Brooklyn Nets Shirt' => ['size' => 'XL'],
            'Nirvana Shirt' => ['size' => 'XL'],
            'Bape APUNVS Shirt' => ['size' => 'L'],
            'DKNY Jeans Shirt' => ['size' => 'L'],
            'NEXT Soul Searching Shirt' => ['size' => 'L'],
        ];

        return $sizeMap[$productName] ?? null;
    }

    private function createProduct(array $productData, Category $category): Product
    {
        $slug = Str::slug($productData['name']);

        $product = Product::create([
            'id' => (string) Str::uuid(),
            'category_id' => $category->id,
            'name' => $productData['name'],
            'slug' => $slug,
            'description' => $productData['description'],
            'short_description' => $productData['short_description'],
            'sku' => strtoupper(substr($productData['brand'], 0, 3) . Str::random(5)),
            'color' => $productData['color'],
            'brand' => $productData['brand'],
            'condition' => $productData['condition'],
            'source' => $productData['source'],
            'base_price' => $productData['base_price'],
            'sale_price' => null,
            'cost_price' => $productData['cost_price'],
            'original_price' => $productData['original_price'],
            'stock_status' => 'available',
            'type' => $productData['type'],
            'image_path' => null,
            'is_active' => true,
            'is_featured' => rand(0, 1) == 1,
            'tags' => json_encode($productData['tags']),
        ]);

        // Handle image copying
        $this->copyProductImage($product, $productData['image_name'], $category);

        return $product;
    }

    private function copyProductImage(Product $product, string $imageName): void
    {
        // Path to seeder images
        $sourceImagePath = database_path("seeders/images/{$imageName}");

        if (!File::exists($sourceImagePath)) {
            $this->command->warn("⚠️ Image not found: {$imageName}");
            return;
        }

        // New simplified storage folder
        $storageDir = "products";

        // Ensure directory exists
        Storage::disk('public')->makeDirectory($storageDir);

        // Keep original filename OR rename to main.ext (your choice)
        $extension = pathinfo($imageName, PATHINFO_EXTENSION);
        $filename = $imageName;
        // If you want to keep the original filename instead:
        // $filename = $imageName;

        $destinationPath = "{$storageDir}/{$filename}";

        // Copy file
        $imageContent = File::get($sourceImagePath);
        Storage::disk('public')->put($destinationPath, $imageContent);

        // Store simplified path
        $product->update(['image_path' => $destinationPath]);

        $this->command->info("📸 Copied image for: {$product->name}");
    }
    private function attachItemsToOrder(Order $order, Collection $products): void
    {
        $lineItemsTotal = 0;

        foreach (range(1, fake()->numberBetween(1, 3)) as $_) {
            $product = $products->random();
            $quantity = fake()->numberBetween(1, 2);
            $unitPrice = $product->sale_price ?? $product->base_price;
            $lineTotal = $unitPrice * $quantity;

            OrderItem::factory()->create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_sku' => $product->sku,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal,
            ]);

            $lineItemsTotal += $lineTotal;
        }

        $this->recalculateOrderTotals($order, $lineItemsTotal);
    }

    private function recalculateOrderTotals(Order $order, float $lineItemsTotal): void
    {
        $shippingAmount = $order->shipping_amount ?? 0;
        $discountAmount = min($order->discount_amount ?? 0, $lineItemsTotal * 0.4);
        $taxAmount = round($lineItemsTotal * 0.12, 2);
        $total = max($lineItemsTotal + $taxAmount + $shippingAmount - $discountAmount, 0);

        $order->update([
            'subtotal' => $lineItemsTotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $total,
            'refunded_amount' => $order->payment_status === 'refunded' ? $total : ($order->refunded_amount ?? 0),
        ]);
    }
}
