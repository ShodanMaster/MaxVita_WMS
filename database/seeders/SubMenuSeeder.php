<?php

namespace Database\Seeders;

use App\Models\Sidebar\Submenu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Submenu::insert([
            // Masters Submenus
            [
                'menu_id' => 2,
                'title' => 'Warehouses',
                'link' => 'branch',
                'listing_order' => 0,
                'created_at' => now(),
            ],
            [
                'menu_id' => 2,
                'title' => 'Location',
                'link' => 'location',
                'listing_order' => 1,
                'created_at' => now(),
            ],
            [
                'menu_id' => 2,
                'title' => 'Bin',
                'link' => 'bin',
                'listing_order' => 2,
                'created_at' => now(),
            ],
            [
                'menu_id' => 2,
                'title' => 'Category',
                'link' => 'category',
                'listing_order' => 3,
                'created_at' => now(),
            ],
            [
                'menu_id' => 2,
                'title' => 'Sub Category',
                'link' => 'sub-category',
                'listing_order' => 4,
                'created_at' => now(),
            ],
            [
                'menu_id' => 2,
                'title' => 'UOM',
                'link' => 'uom',
                'listing_order' => 5,
                'created_at' => now(),
            ],
            [
                'menu_id' => 2,
                'title' => 'Item',
                'link' => 'item',
                'listing_order' => 6,
                'created_at' => now(),
            ],
            [
                'menu_id' => 2,
                'title' => 'Brand',
                'link' => 'brand',
                'listing_order' => 7,
                'created_at' => now(),
            ],
            [
                'menu_id' => 2,
                'title' => 'Customer',
                'link' => 'customer',
                'listing_order' => 8,
                'created_at' => now(),
            ],
            [
                'menu_id' => 2,
                'title' => 'Vendor',
                'link' => 'vendr',
                'listing_order' => 9,
                'created_at' => now(),
            ],
            [
                'menu_id' => 2,
                'title' => 'User',
                'link' => 'user',
                'listing_order' => 10,
                'created_at' => now(),
            ],
            [
                'menu_id' => 2,
                'title' => 'Reason',
                'link' => 'reason',
                'listing_order' => 11,
                'created_at' => now(),
            ],

            //Transaction Submenus
            [
                'menu_id' => 3,
                'title' => 'Purchase Order',
                'link' => 'purchase-order',
                'listing_order' => 11,
                'created_at' => now(),
            ],
            [
                'menu_id' => 3,
                'title' => 'Purchase Cancel',
                'link' => 'purchase-order-cancel',
                'listing_order' => 11,
                'created_at' => now(),
            ],

            //Utility Submenus
            [
                'menu_id' => 5,
                'title' => 'Permission',
                'link' => 'permission',
                'listing_order' => 0,
                'created_at' => now(),
            ],
            [
                'menu_id' => 5,
                'title' => 'Change Password',
                'link' => 'change-password',
                'listing_order' => 0,
                'created_at' => now(),
            ],
        ]);
    }
}
