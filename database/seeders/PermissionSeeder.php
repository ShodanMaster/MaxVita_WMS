<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::insert([
            [
                'user_id' => 1,
                'submenu_id' => 1,
            ],
            [
                'user_id' => 1,
                'submenu_id' => 2,
            ],
            [
                'user_id' => 1,
                'submenu_id' => 3,
            ],
            [
                'user_id' => 1,
                'submenu_id' => 4,
            ],
            // [
            //     'user_id' => 1,
            //     'submenu_id' => 5,
            // ],
            [
                'user_id' => 1,
                'submenu_id' => 6,
            ],
            [
                'user_id' => 1,
                'submenu_id' => 7,
            ],
            [
                'user_id' => 1,
                'submenu_id' => 8,
            ],
            [
                'user_id' => 1,
                'submenu_id' => 9,
            ],
            [
                'user_id' => 1,
                'submenu_id' => 10,
            ],
            [
                'user_id' => 1,
                'submenu_id' => 11,
            ],
            [
                'user_id' => 1,
                'submenu_id' => 12,
            ],
            [
                'user_id' => 1,
                'submenu_id' => 13,
            ],
            [
                'user_id' => 1,
                'submenu_id' => 14,
            ],
            [
                'user_id' => 1,
                'submenu_id' => 15,
            ],
            [
                'user_id' => 1,
                'submenu_id' => 16,
            ],
            [
                'user_id' => 1,
                'submenu_id' => 17,
            ],
            [
                'user_id' => 1,
                'submenu_id' => 18,
            ],
            [
                'user_id' => 1,
                'submenu_id' => 19,
            ],
            [
                'user_id' => 1,
                'submenu_id' => 20,
            ],
            [
                'user_id' => 1,
                'submenu_id' => 21,
            ],
            [
                'user_id' => 1,
                'submenu_id' => 22,
            ],
        ]);
    }
}
