<?php

namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;
use App\Models\Product;

class TestProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'product_name' => 'Water Filter',
            'product_description' => 'Test water filter product',
            'purchase_cost' => 100,
            'selling_price' => 150,
            'quantity' => 50,
            'category' => 'Filter',
            'approx_volume' => 1,
            'product_image' => 'testpic',
        ]);

        Product::create([
            'product_name' => 'Water Hose',
            'product_description' => 'Test water hose product',
            'purchase_cost' => 200,
            'selling_price' => 300,
            'quantity' => 30,
            'category' => 'Hose',
            'approx_volume' => 2,
            'product_image' => 'testpic',
        ]);

        Product::create([
            'product_name' => 'Water Gallon',
            'product_description' => 'Test water gallon product',
            'purchase_cost' => 50,
            'selling_price' => 80,
            'quantity' => 40,
            'category' => 'Gallon',
            'approx_volume' => 5,
            'product_image' => 'testpic',
        ]);
    }
}
