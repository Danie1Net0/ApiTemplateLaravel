<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        if (is_null(User::where('email', 'admin@admin.com')->first())) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('superadmin'),
                'is_active' => true
            ])->assignRole('Super Administrador')->avatar()->create();
        }
    }
}
