<?php

namespace Database\Seeders;

use App\Models\Sidebar\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Menu::insert([
            [
                'title' => 'Dashboard',
                'link' => 'dashboard',
                'icon' => 'box',
                'listing_order' => 0,
                'created_at' => now(),
            ],
            [
                'title' => 'Masters',
                'link' => 'masters',
                'icon' => 'hard-drive',
                'listing_order' => 1,
                'created_at' => now(),
            ],
            [
                'title' => 'Transactions',
                'link' => 'transactions',
                'icon' => 'inbox',
                'listing_order' => 2,
                'created_at' => now(),
            ],
            [
                'title' => 'Reports',
                'link' => 'reports',
                'icon' => 'layout',
                'listing_order' => 4,
                'created_at' => now(),
            ],
            [
                'title' => 'Utilities',
                'link' => 'utilities',
                'icon' => 'settings',
                'listing_order' => 5,
                'created_at' => now(),
            ],
        ]);
    }
}
