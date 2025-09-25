<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Location;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        //ExampleData

        Branch::create([
            'name' => 'Ernakulam',
            'branch_code' => 'ekm',
            'address' => 'Ernakulam, Kerala, India',
        ]);

        Location::create([
            'branch_id' => 1,
            'name' => 'Kadavanthara',
            'prefix' => 'kdv',
            'nav_loc_code' => '670502',
            'location_type' => '1',
        ]);

        User::create([
            'branch_id' => 1,
            'location_id' => 1,
            'name' => 'Admin',
            'email' => 'admin@examle.com',
            'password' => Hash::make('admin@123'),
            'username' => 'admin',
            'user_type' => 1,
            'permission_level' => 1,
        ]);

        $this->call([
            MenuSeeder::class,
            SubMenuSeeder::class,
            PermissionSeeder::class,
        ]);

    }
}
