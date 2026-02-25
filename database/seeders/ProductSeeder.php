<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'item_code' => 'P001',
                'name' => 'Office Chair Ergonomic',
                'category' => 'Product',
                'unit' => 'piece',
                'unit_price' => 299.99,
                'description' => 'Comfortable ergonomic office chair with lumbar support',
                'brand' => 'OfficePro',
                'type' => 'Furniture',
                'active' => true,
            ],
            [
                'item_code' => 'P002',
                'name' => 'Standing Desk',
                'category' => 'Product',
                'unit' => 'piece',
                'unit_price' => 599.99,
                'description' => 'Adjustable height standing desk with memory presets',
                'brand' => 'DeskMax',
                'type' => 'Furniture',
                'active' => true,
            ],
            [
                'item_code' => 'S001',
                'name' => 'Office Cleaning Service',
                'category' => 'Service',
                'unit' => 'hour',
                'unit_price' => 45.00,
                'description' => 'Professional office cleaning service',
                'brand' => 'CleanPro',
                'type' => 'Service',
                'active' => true,
            ],
            [
                'item_code' => 'P003',
                'name' => 'Wireless Mouse',
                'category' => 'Product',
                'unit' => 'piece',
                'unit_price' => 29.99,
                'description' => 'Ergonomic wireless mouse with long battery life',
                'brand' => 'TechGear',
                'type' => 'Electronics',
                'active' => true,
            ],
            [
                'item_code' => 'P004',
                'name' => 'LED Desk Lamp',
                'category' => 'Product',
                'unit' => 'piece',
                'unit_price' => 49.99,
                'description' => 'Adjustable LED desk lamp with USB charging port',
                'brand' => 'LightBright',
                'type' => 'Lighting',
                'active' => true,
            ],
            [
                'item_code' => 'S002',
                'name' => 'IT Support Service',
                'category' => 'Service',
                'unit' => 'hour',
                'unit_price' => 75.00,
                'description' => 'Professional IT support and troubleshooting',
                'brand' => 'TechSupport',
                'type' => 'Service',
                'active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
