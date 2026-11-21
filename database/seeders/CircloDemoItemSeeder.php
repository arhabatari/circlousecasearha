<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CircloDemoItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['title' => 'Macbook Air M1', 'category' => 'Electronics', 'owner' => 'Budi S.', 'daily_price' => 50000, 'status' => 'available'],
            ['title' => 'Textbook Calculus', 'category' => 'Books', 'owner' => 'Dewi L.', 'daily_price' => 15000, 'status' => 'available'],
            ['title' => 'Canon Camera', 'category' => 'Electronics', 'owner' => 'Rizky A.', 'daily_price' => 75000, 'status' => 'booked'],
            ['title' => 'Camping Tent', 'category' => 'Equipment', 'owner' => 'Sari M.', 'daily_price' => 40000, 'status' => 'available'],
            ['title' => 'Jaket Hoodie', 'category' => 'Fashion', 'owner' => 'Andi P.', 'daily_price' => 20000, 'status' => 'available'],
            ['title' => 'Road Bike', 'category' => 'Sports', 'owner' => 'Tono W.', 'daily_price' => 60000, 'status' => 'booked'],
        ];

        foreach ($items as $item) {
            DB::table('circlo_demo_items')->updateOrInsert(
                ['title' => $item['title']],
                array_merge($item, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
