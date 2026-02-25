<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductPhoto;

class ProductPhotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get sample products
        $products = Product::all();
        
        // Create simple placeholder files for demonstration
        foreach ($products as $product) {
            $photoName = 'product_' . $product->item_code . '.txt';
            $photoPath = 'product_photos/' . $photoName;
            
            // Create a simple text file as placeholder
            $content = "Product: {$product->name}\nItem Code: {$product->item_code}\nCategory: {$product->category}\nPrice: $" . $product->unit_price;
            
            // Store the file
            \Storage::disk('public')->put($photoPath, $content);
            
            // Create product photo record
            ProductPhoto::create([
                'product_id' => $product->id,
                'photo_path' => $photoPath,
                'photo_name' => $product->name . ' Image',
                'order' => 0
            ]);
        }
    }
}
