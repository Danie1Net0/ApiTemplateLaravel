<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createRoles(config('access-control.roles'));
        $this->createPermissions();
    }

    /**
     * Cria "roles" a partir dos dados do arquivo config/access-control.php
     *
     * @param array $roles
     */
    private function createRoles(array $roles): void
    {
        foreach ($roles as $role) {
            if (is_null(Role::where('name', $role)->first())) {
                Role::create(['name' => $role]);
            }
        }
    }

    /**
     * Cria "permissions" a partir das rotas com prefixo "api".
     */
    private function createPermissions(): void
    {
        foreach (Route::getRoutes() as $route) {
            if ($route->getPrefix() === 'api') {
                $permission = $route->getName();

                if (is_null(Permission::where('name', $permission)->first())) {
                    Permission::create(['name' => $permission]);
                }
            }
        }
    }
}
