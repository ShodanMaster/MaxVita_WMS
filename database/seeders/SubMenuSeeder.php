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
            // 1
            [
                'menu_id' => 2,
                'title' => 'Warehouses',
                'link' => 'branch',
                'listing_order' => 0,
                'created_at' => now(),
            ],
            // 2
            [
                'menu_id' => 2,
                'title' => 'Location',
                'link' => 'location',
                'listing_order' => 1,
                'created_at' => now(),
            ],
            // 3
            [
                'menu_id' => 2,
                'title' => 'Bin',
                'link' => 'bin',
                'listing_order' => 2,
                'created_at' => now(),
            ],
            // 4
            [
                'menu_id' => 2,
                'title' => 'Category',
                'link' => 'category',
                'listing_order' => 3,
                'created_at' => now(),
            ],
            // 5
            [
                'menu_id' => 2,
                'title' => 'Sub Category',
                'link' => 'sub-category',
                'listing_order' => 4,
                'created_at' => now(),
            ],
            // 6
            [
                'menu_id' => 2,
                'title' => 'UOM',
                'link' => 'uom',
                'listing_order' => 5,
                'created_at' => now(),
            ],
            // 7
            [
                'menu_id' => 2,
                'title' => 'Item',
                'link' => 'item',
                'listing_order' => 6,
                'created_at' => now(),
            ],
            // 8
            [
                'menu_id' => 2,
                'title' => 'Brand',
                'link' => 'brand',
                'listing_order' => 7,
                'created_at' => now(),
            ],
            // 9
            [
                'menu_id' => 2,
                'title' => 'Customer',
                'link' => 'customer',
                'listing_order' => 8,
                'created_at' => now(),
            ],
            // 10
            [
                'menu_id' => 2,
                'title' => 'Vendor',
                'link' => 'vendr',
                'listing_order' => 9,
                'created_at' => now(),
            ],
            // 11
            [
                'menu_id' => 2,
                'title' => 'User',
                'link' => 'user',
                'listing_order' => 10,
                'created_at' => now(),
            ],
            // 12
            [
                'menu_id' => 2,
                'title' => 'Reason',
                'link' => 'reason',
                'listing_order' => 11,
                'created_at' => now(),
            ],

            // Transaction
            // Purchase Entry
            // 13
            [
                'menu_id' => 3,
                'title' => 'Purchase Order',
                'link' => 'purchase-order',
                'listing_order' => 11,
                'created_at' => now(),
            ],
            // 14
            [
                'menu_id' => 3,
                'title' => 'Purchase Cancel',
                'link' => 'purchase-order-cancel',
                'listing_order' => 11,
                'created_at' => now(),
            ],
            // GRN Entry
            //15
            [
                'menu_id' => 4,
                'title' => 'GRN Entry',
                'link' => 'grn',
                'listing_order' => 11,
                'created_at' => now(),
            ],
            // Storage Scan
            //16
            [
                'menu_id' => 4,
                'title' => 'Storage Scan',
                'link' => 'storage-scan',
                'listing_order' => 11,
                'created_at' => now(),
            ],

            //Production
            //Production Plan
            // 17
            [
                'menu_id' => 5,
                'title' => 'Production Plan',
                'link' => 'production-plan',
                'listing_order' => 11,
                'created_at' => now(),
            ],
            //Production Scan
            // 18
            [
                'menu_id' => 5,
                'title' => 'Production Issue',
                'link' => 'production-issue',
                'listing_order' => 11,
                'created_at' => now(),
            ],
            //Fg Barcode Generation
            // 19
            [
                'menu_id' => 5,
                'title' => 'Fg Barcode Generation',
                'link' => 'fg-barcode-generation',
                'listing_order' => 11,
                'created_at' => now(),
            ],

            // Utility Submenus
            // Permission
            //20
            [
                'menu_id' => 7,
                'title' => 'Permission',
                'link' => 'permission',
                'listing_order' => 0,
                'created_at' => now(),
            ],

            // Change Password
            // 21
            [
                'menu_id' => 7,
                'title' => 'Change Password',
                'link' => 'change-password',
                'listing_order' => 0,
                'created_at' => now(),
            ],
        ]);
    }
}
