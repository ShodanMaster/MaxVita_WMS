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
            // 1
            [
                'title' => 'Dashboard',
                'link' => 'dashboard',
                'icon' => 'box',
                'listing_order' => 0,
                'created_at' => now(),
            ],
            // 2
            [
                'title' => 'Masters',
                'link' => 'masters',
                'icon' => 'hard-drive',
                'listing_order' => 1,
                'created_at' => now(),
            ],
            // 3
            [
                'title' => 'Purchase Entry',
                'link' => 'purchase-entry',
                'icon' => 'inbox',
                'listing_order' => 2,
                'created_at' => now(),
            ],
            // 4
            [
                'title' => 'GRN',
                'link' => 'grn',
                'icon' => 'log-in',
                'listing_order' => 3,
                'created_at' => now(),
            ],
            // 5
            [
                'title' => 'Production',
                'link' => 'production',
                'icon' => 'layers',
                'listing_order' => 4,
                'created_at' => now(),
            ],
            // 6
            [
                'title' => 'Dispatch',
                'link' => 'dispatch',
                'icon' => 'truck',
                'listing_order' => 5,
                'created_at' => now(),
            ],
            // 7
            [
                'title' => 'Receipt',
                'link' => 'receipt',
                'icon' => 'truck',
                'listing_order' => 6,
                'created_at' => now(),
            ],
            // 8
            [
                'title' => 'Reports',
                'link' => 'reports',
                'icon' => 'layout',
                'listing_order' => 7,
                'created_at' => now(),
            ],
            // 9
            [
                'title' => 'Utilities',
                'link' => 'utilities',
                'icon' => 'settings',
                'listing_order' => 8,
                'created_at' => now(),
            ],
        ]);
    }
}
