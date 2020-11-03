<?php

namespace Database\Seeders;

use App\Models\AccessControl\Role;
use Illuminate\Database\Seeder;

/**
 * Class RolesSeerder
 * @package Database\Seeders
 */
class RolesSeerder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (config('access-control.roles') as $role) {
            if (is_null(Role::where('name', $role)->first())) {
                Role::create(['name' => $role]);
            }
        }
    }
}
