<?php

use Illuminate\Database\Seeder;
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
        $this->createPermissions(config('access-control.permissions'));
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
     * Cria "permissions" a partir dos dados do arquivo config/access-control.php
     *
     * @param array $permissions
     */
    private function createPermissions(array $permissions): void
    {
        $defaultPermissions = ['Listar', 'Criar', 'Visualizar', 'Editar', 'Deletar'];

        foreach ($permissions as $key => $value) {
            // Se for um array associativo, cria apenas as permissões específicas.
            if (is_array($value)) {
                foreach ($value as $permission) {
                    if (is_null(Permission::where('name', "{$permission} {$key}")->first())) {
                        Permission::create(['name' => "{$permission} {$key}"]);
                    }
                }
                break;
            }

            // Senão, cria todas as permissões (conforme o array $permissions).
            foreach ($defaultPermissions as $permission) {
                if (is_null(Permission::where('name', "{$permission} {$value}")->first())) {
                    Permission::create(['name' => "{$permission} {$value}"]);
                }
            }
        }
    }
}
