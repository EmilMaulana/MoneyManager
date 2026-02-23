<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {

            $categories = [
                // Income
                ['name' => 'Gaji', 'type' => 'income', 'color' => '#16a34a', 'icon' => 'wallet'],
                ['name' => 'Bonus', 'type' => 'income', 'color' => '#22c55e', 'icon' => 'gift'],

                // Expense
                ['name' => 'Makanan', 'type' => 'expense', 'color' => '#f97316', 'icon' => 'utensils'],
                ['name' => 'Transport', 'type' => 'expense', 'color' => '#3b82f6', 'icon' => 'car'],
                ['name' => 'Belanja', 'type' => 'expense', 'color' => '#ec4899', 'icon' => 'shopping-bag'],
                ['name' => 'Tagihan', 'type' => 'expense', 'color' => '#ef4444', 'icon' => 'receipt'],
                ['name' => 'Hiburan', 'type' => 'expense', 'color' => '#8b5cf6', 'icon' => 'film'],
            ];

            foreach ($categories as $category) {
                Category::create([
                    'user_id' => $user->id,
                    'name'     => $category['name'],
                    'type'     => $category['type'],
                    'color'    => $category['color'],
                    'icon'     => $category['icon'],
                ]);
            }
        }
    }
}
