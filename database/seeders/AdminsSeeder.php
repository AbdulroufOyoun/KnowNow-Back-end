<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create(['name' => 'superAdmin', 'phone' => '0988292807', 'mobile_uuid' => '0', 'email' => 'superAdmin@gmail.com', 'password' => '123456789']);
        $user1 = User::create(['name' => 'Molham Abood', 'phone' => '0945734346', 'mobile_uuid' => '1', 'email' => 'molham@gmail.com', 'password' => '123456789']);
        $user2 = User::create(['name' => 'Waseem Zabadna', 'phone' => '0958586969', 'mobile_uuid' => '2', 'email' => 'waseem@gmail.com', 'password' => '123456789']);

        Role::create(['name' => 'superAdmin', 'guard_name' => 'api']);
        Role::create(['name' => 'admin', 'guard_name' => 'api']);
        Role::create(['name' => 'doctor', 'guard_name' => 'api']);


        $user->assignRole('superAdmin');
        $user1->assignRole('admin');
        $user2->assignRole('admin');


        // Artisan::call('passport:install');
        // Artisan::call('key:generate');
    }
}
