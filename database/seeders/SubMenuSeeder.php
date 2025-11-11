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
            //Production Barcode Generation
            // 19
            [
                'menu_id' => 5,
                'title' => 'FG Barcode Generation',
                'link' => 'production-barcode-generation',
                'listing_order' => 11,
                'created_at' => now(),
            ],
            //Production Storage Scan
            // 20
            [
                'menu_id' => 5,
                'title' => 'FG Storage Scan',
                'link' => 'production-storage-scan',
                'listing_order' => 11,
                'created_at' => now(),
            ],

            //Dispatch
            //Dispatch Entry
            // 21
            [
                'menu_id' => 6,
                'title' => 'Dispatch Entry',
                'link' => 'dispatch',
                'listing_order' => 11,
                'created_at' => now(),
            ],
            //Dispatch Edit
            // 22
            [
                'menu_id' => 6,
                'title' => 'Dispatch Edit',
                'link' => 'dispatch-edit',
                'listing_order' => 11,
                'created_at' => now(),
            ],
            //Dispatch Scan
            // 23
            [
                'menu_id' => 6,
                'title' => 'Dispatch Scan',
                'link' => 'dispatch-scan',
                'listing_order' => 11,
                'created_at' => now(),
            ],

            //Sales Return
            // 24
            [
                'menu_id' => 6,
                'title' => 'Sales Return',
                'link' => 'sales-return',
                'listing_order' => 11,
                'created_at' => now(),
            ],

            //Receipt
            // Receipt Scan
            // 25
            [
                'menu_id' => 7,
                'title' => 'Receipt Scan',
                'link' => 'receipt-scan',
                'listing_order' => 11,
                'created_at' => now(),
            ],

            //Stock Management
            //Opening Stock
            // 26
            [
                'menu_id' => 8,
                'title' => 'Opening Stock',
                'link' => 'opening-stock',
                'listing_order' => 11,
                'created_at' => now(),
            ],

            //Stock Out
            // 27
            [
                'menu_id' => 8,
                'title' => 'Stock Out',
                'link' => 'stock-out',
                'listing_order' => 11,
                'created_at' => now(),
            ],

            //Reports
            //Purchase Order Reports
            // 28
            [
                'menu_id' => 9,
                'title' => 'Purchase Order Report',
                'link' => 'purchase-order-report',
                'listing_order' => 0,
                'created_at' => now(),
            ],

            // 29
            [
                'menu_id' => 9,
                'title' => 'GRN Report',
                'link' => 'grn-report',
                'listing_order' => 0,
                'created_at' => now(),
            ],

            //Stock Report
            //30
            [
                'menu_id' => 9,
                'title' => 'Stock Report',
                'link' => 'stock-report',
                'listing_order' => 7,
                'created_at' => now(),
            ],

            //Storage Scan Report
            //31
            [
                'menu_id' => 9,
                'title' => 'Storage Scan Report',
                'link' => 'storage-scan-report',
                'listing_order' => 7,
                'created_at' => now(),
            ],

            //Production Plan Report
            //32
            [
                'menu_id' => 9,
                'title' => 'Production Plan Report',
                'link' => 'production-plan-report',
                'listing_order' => 7,
                'created_at' => now(),
            ],

            // Utility Submenus
            // Barcode Reprint
            // 33
            [
                'menu_id' => 10,
                'title' => 'Barcode Reprint',
                'link' => 'barcode-reprint',
                'listing_order' => 0,
                'created_at' => now(),
            ],

            // Permission
            // 34
            [
                'menu_id' => 10,
                'title' => 'Permission',
                'link' => 'permission',
                'listing_order' => 0,
                'created_at' => now(),
            ],

            // Change Password
            // 35
            [
                'menu_id' => 10,
                'title' => 'Change Password',
                'link' => 'change-password',
                'listing_order' => 0,
                'created_at' => now(),
            ],
        ]);
    }
}
