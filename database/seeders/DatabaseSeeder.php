<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\InventoryMovement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::factory(10)->create();

        // Create clothing categories
        $shirtsCategory = Category::create([
            'name' => 'Shirts & Tops',
            'slug' => 'shirts-tops',
            'description' => 'Branded shirts, blouses, and tops from mall pullouts and thrift finds',
            'is_active' => true,
        ]);

        $pantsCategory = Category::create([
            'name' => 'Pants & Bottoms',
            'slug' => 'pants-bottoms',
            'description' => 'Quality pants, jeans, and bottoms from premium brands',
            'is_active' => true,
        ]);

        $dressesCategory = Category::create([
            'name' => 'Dresses',
            'slug' => 'dresses',
            'description' => 'Elegant dresses and formal wear from designer brands',
            'is_active' => true,
        ]);

        $outerwearCategory = Category::create([
            'name' => 'Outerwear',
            'slug' => 'outerwear',
            'description' => 'Jackets, coats, and outerwear from top fashion brands',
            'is_active' => true,
        ]);

        $categories = collect([$shirtsCategory, $pantsCategory, $dressesCategory, $outerwearCategory]);

        // Create specific products
        $shirtProducts = $this->createShirtProducts($shirtsCategory);
        $pantsProducts = $this->createPantsProducts($pantsCategory);
        $dressProducts = $this->createDressProducts($dressesCategory);
        $outerwearProducts = $this->createOuterwearProducts($outerwearCategory);

        $products = $shirtProducts->concat($pantsProducts)->concat($dressProducts)->concat($outerwearProducts);

        // Create variants for sizes and colors
        $variants = $products->flatMap(function ($product) {
            if (fake()->boolean(80)) { // 80% chance of having variants
                $variants = collect();
                $sizes = ['XS', 'S', 'M', 'L', 'XL'];
                $colors = ['Black', 'White', 'Navy', 'Gray', 'Beige'];

                // Create size variants
                foreach (fake()->randomElements($sizes, fake()->numberBetween(2, 4)) as $size) {
                    $variants->push(ProductVariant::factory()->create([
                        'product_id' => $product->id,
                        'name' => "Size {$size}",
                        'type' => 'size',
                        'value' => $size,
                        'stock_quantity' => fake()->numberBetween(0, 3), // Lower stock for thrift items
                    ]));
                }

                $totalStock = $variants->sum('stock_quantity');
                $product->update(['stock_quantity' => $totalStock]);
                return $variants;
            }

            $product->update(['stock_quantity' => fake()->numberBetween(0, 2)]); // Limited quantities
            return collect();
        });

        // Create orders
        $orders = $users->flatMap(function ($user) use ($products, $variants) {
            return Order::factory(fake()->numberBetween(1, 3))->create([
                'user_id' => $user->id
            ])->each(function ($order) use ($products, $variants) {
                foreach (range(1, fake()->numberBetween(1, 4)) as $_) {
                    $product = $products->random();
                    $productVariants = $variants->where('product_id', $product->id);
                    $variant = $productVariants->isNotEmpty() ? $productVariants->random() : null;

                    OrderItem::factory()->create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_variant_id' => $variant?->id,
                        'product_name' => $product->name,
                        'product_sku' => $product->sku,
                        'variant_name' => $variant?->name,
                        'variant_sku' => $variant?->sku,
                    ]);
                }
            });
        });

        // Create guest orders
        $guestOrders = Order::factory(5)->create([
            'user_id' => null
        ])->each(function ($order) use ($products, $variants) {
            foreach (range(1, fake()->numberBetween(1, 3)) as $_) {
                $product = $products->random();
                $productVariants = $variants->where('product_id', $product->id);
                $variant = $productVariants->isNotEmpty() ? $productVariants->random() : null;

                OrderItem::factory()->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'variant_name' => $variant?->name,
                    'variant_sku' => $variant?->sku,
                ]);
            }
        });

        // Create user addresses
        $users->each(function ($user) {
            Address::factory(fake()->numberBetween(1, 2))->create([
                'user_id' => $user->id
            ]);
        });

        // Create inventory movements
        $products->each(function ($product) use ($variants) {
            $productVariants = $variants->where('product_id', $product->id);
            if ($productVariants->isNotEmpty()) {
                InventoryMovement::factory(fake()->numberBetween(1, 3))->create([
                    'product_id' => $product->id,
                    'product_variant_id' => $productVariants->random()->id
                ]);
            } else {
                InventoryMovement::factory(fake()->numberBetween(1, 2))->create([
                    'product_id' => $product->id,
                    'product_variant_id' => null
                ]);
            }
        });

        $this->command->info('✅ Clothing store data seeded successfully!');
    }

    private function createShirtProducts(Category $category): \Illuminate\Support\Collection
    {
        $shirtData = [
            [
                'name' => 'Nike Dri-FIT Training Tee',
                'description' => 'Authentic Nike performance t-shirt from mall pullout. Features moisture-wicking technology and classic Nike swoosh. Perfect condition, never worn.',
                'short_description' => 'Nike performance tee with Dri-FIT technology',
                'base_price' => 1250.00,
                'cost_price' => 300.00,
                'brand' => 'Nike',
                'condition' => 'Brand New',
                'source' => 'Mall Pullout',
                'type' => 'premium',
                'original_price' => 2500.00
            ],
            [
                'name' => 'Zara Oversized Button Shirt',
                'description' => 'Stylish oversized button-up shirt from Zara. Thrifted find in excellent condition. Classic white with subtle texture.',
                'short_description' => 'Zara oversized button shirt in white',
                'base_price' => 890.00,
                'cost_price' => 200.00,
                'brand' => 'Zara',
                'condition' => 'Excellent',
                'source' => 'Thrift Store',
                'type' => 'classic',
                'original_price' => 1950.00
            ],
            [
                'name' => 'Uniqlo Heattech Crew Neck',
                'description' => 'Uniqlo\'s innovative Heattech long sleeve shirt. Mall pullout item with tags still attached. Thermal technology for warmth.',
                'short_description' => 'Uniqlo Heattech thermal shirt',
                'base_price' => 750.00,
                'cost_price' => 180.00,
                'brand' => 'Uniqlo',
                'condition' => 'Brand New',
                'source' => 'Mall Pullout',
                'type' => 'classic',
                'original_price' => 1290.00
            ],
            [
                'name' => 'H&M Vintage Band Tee',
                'description' => 'Retro band t-shirt from H&M with vintage graphics. Thrifted piece with character and style. Soft cotton blend.',
                'short_description' => 'H&M vintage band t-shirt',
                'base_price' => 650.00,
                'cost_price' => 150.00,
                'brand' => 'H&M',
                'condition' => 'Good',
                'source' => 'Thrift Store',
                'type' => 'classic',
                'original_price' => 990.00
            ],
            [
                'name' => 'Forever 21 Crop Top',
                'description' => 'Trendy crop top from Forever 21 mall pullout. Features unique cut-out details and stretch fabric. Perfect for layering.',
                'short_description' => 'Forever 21 crop top with cut-out details',
                'base_price' => 450.00,
                'cost_price' => 120.00,
                'brand' => 'Forever 21',
                'condition' => 'Brand New',
                'source' => 'Mall Pullout',
                'type' => 'classic',
                'original_price' => 790.00
            ]
        ];

        return collect($shirtData)->map(function ($productData) use ($category) {
            return $this->createProduct($productData, $category);
        });
    }

    private function createPantsProducts(Category $category): \Illuminate\Support\Collection
    {
        $pantsData = [
            [
                'name' => 'Levi\'s 511 Slim Jeans',
                'description' => 'Classic Levi\'s 511 slim fit jeans in dark wash. Thrifted premium denim in great condition. Authentic vintage styling.',
                'short_description' => 'Levi\'s 511 slim fit jeans, dark wash',
                'base_price' => 1890.00,
                'cost_price' => 450.00,
                'brand' => 'Levi\'s',
                'condition' => 'Very Good',
                'source' => 'Thrift Store',
                'type' => 'premium',
                'original_price' => 4500.00
            ],
            [
                'name' => 'Mango Wide Leg Trousers',
                'description' => 'Elegant wide leg trousers from Mango mall pullout. Professional look with comfortable fit. Perfect for office wear.',
                'short_description' => 'Mango wide leg professional trousers',
                'base_price' => 1350.00,
                'cost_price' => 320.00,
                'brand' => 'Mango',
                'condition' => 'Brand New',
                'source' => 'Mall Pullout',
                'type' => 'premium',
                'original_price' => 2890.00
            ],
            [
                'name' => 'American Eagle Mom Jeans',
                'description' => 'Trendy high-waisted mom jeans from American Eagle. Thrifted find with perfect vintage fit and light distressing.',
                'short_description' => 'American Eagle high-waisted mom jeans',
                'base_price' => 1650.00,
                'cost_price' => 380.00,
                'brand' => 'American Eagle',
                'condition' => 'Excellent',
                'source' => 'Thrift Store',
                'type' => 'classic',
                'original_price' => 3200.00
            ]
        ];

        return collect($pantsData)->map(function ($productData) use ($category) {
            return $this->createProduct($productData, $category);
        });
    }

    private function createDressProducts(Category $category): \Illuminate\Support\Collection
    {
        $dressData = [
            [
                'name' => 'Massimo Dutti Midi Dress',
                'description' => 'Sophisticated midi dress from Massimo Dutti. Mall pullout piece with elegant silhouette and premium fabric quality.',
                'short_description' => 'Massimo Dutti elegant midi dress',
                'base_price' => 2250.00,
                'cost_price' => 550.00,
                'brand' => 'Massimo Dutti',
                'condition' => 'Brand New',
                'source' => 'Mall Pullout',
                'type' => 'premium',
                'original_price' => 5800.00
            ],
            [
                'name' => 'Bershka Floral Sundress',
                'description' => 'Cute floral sundress from Bershka mall pullout. Perfect for casual summer days with adjustable straps and flowing fit.',
                'short_description' => 'Bershka floral summer dress',
                'base_price' => 950.00,
                'cost_price' => 220.00,
                'brand' => 'Bershka',
                'condition' => 'Brand New',
                'source' => 'Mall Pullout',
                'type' => 'classic',
                'original_price' => 1890.00
            ]
        ];

        return collect($dressData)->map(function ($productData) use ($category) {
            return $this->createProduct($productData, $category);
        });
    }

    private function createOuterwearProducts(Category $category): \Illuminate\Support\Collection
    {
        $outerwearData = [
            [
                'name' => 'North Face Windbreaker',
                'description' => 'Authentic North Face windbreaker jacket. Thrifted outdoor gear in excellent condition. Water-resistant and packable.',
                'short_description' => 'North Face windbreaker jacket',
                'base_price' => 3250.00,
                'cost_price' => 750.00,
                'brand' => 'The North Face',
                'condition' => 'Excellent',
                'source' => 'Thrift Store',
                'type' => 'premium',
                'original_price' => 7500.00
            ],
            [
                'name' => 'Pull & Bear Denim Jacket',
                'description' => 'Classic denim jacket from Pull & Bear mall pullout. Versatile layering piece with vintage wash and comfortable fit.',
                'short_description' => 'Pull & Bear classic denim jacket',
                'base_price' => 1450.00,
                'cost_price' => 350.00,
                'brand' => 'Pull & Bear',
                'condition' => 'Brand New',
                'source' => 'Mall Pullout',
                'type' => 'classic',
                'original_price' => 2990.00
            ]
        ];

        return collect($outerwearData)->map(function ($productData) use ($category) {
            return $this->createProduct($productData, $category);
        });
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
            'base_price' => $productData['base_price'],
            'cost_price' => $productData['cost_price'],
            'stock_quantity' => rand(0, 3), // Limited stock for thrift/pullout items
            'low_stock_threshold' => 1,
            'track_inventory' => true,
            'allow_backorder' => false,
            'type' => $productData['type'], // Added the missing type field
            'brand' => $productData['brand'],
            'condition' => $productData['condition'],
            'source' => $productData['source'],
            'original_price' => $productData['original_price'],
            'is_active' => true,
            'is_featured' => rand(0, 1),
            'available_from' => now(),
            'purchase_count' => rand(0, 5),
        ]);

        // Handle image copying (on hold for now)
        // $this->copyProductImage($product, $productData['image_name'], $category);

        return $product;
    }

    private function copyProductImage(Product $product, string $imageName, Category $category): void
    {
        // Path to seeder images (tracked in git)
        $sourceImagePath = database_path("seeders/images/{$imageName}");

        if (!File::exists($sourceImagePath)) {
            $this->command->warn("⚠️  Image not found: {$imageName}");
            return;
        }

        // Create storage directory structure
        $categorySlug = Str::slug($category->name);
        $productSlug = $product->slug;
        $storageDir = "products/{$categorySlug}/{$productSlug}";

        // Ensure directory exists
        Storage::disk('public')->makeDirectory($storageDir);

        // Copy image to storage
        $extension = pathinfo($imageName, PATHINFO_EXTENSION);
        $filename = "main.{$extension}";
        $destinationPath = "{$storageDir}/{$filename}";

        // Copy file
        $imageContent = File::get($sourceImagePath);
        Storage::disk('public')->put($destinationPath, $imageContent);

        // Update product with image path
        $product->update(['image_path' => $destinationPath]);

        $this->command->info("📸 Copied image for: {$product->name}");
    }
}
