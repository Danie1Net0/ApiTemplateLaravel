<?php

namespace Database\Seeders;

use App\Models\AccessControl\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Route;

/**
 * Class PermissionsSeerder
 * @package Database\Seeders
 */
class PermissionsSeerder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Route::getRoutes() as $route) {
            if ($route->getPrefix() === 'api/v1') {
                $permission = $route->getName();

                if (is_null(Permission::where('name', $permission)->first())) {
                    Permission::create(['name' => $permission]);
                }
            }
        }
    }
}
