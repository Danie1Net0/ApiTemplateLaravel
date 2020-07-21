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
        $roles = config('access-control.roles');

        foreach ($roles as $role)
            if (is_null(Role::where('name', $role)->first()))
                Role::create(['name' => $role, 'guard_name' => 'api']);

        $permissions = config('access-control.permissions');
        $defaultPermissions = ['Listar', 'Criar', 'Visualizar', 'Editar', 'Deletar'];

        foreach ($permissions as $key => $value) {
            if (is_array($value)) {
                // Se for um array associativo, cria apenas as permissões específicas.
                foreach ($value as $permission)
                    if (is_null(Permission::where('name', "{$permission} {$key}")->first()))
                        Permission::create(['name' => "{$permission} {$key}", 'guard_name' => 'api']);
            } else {
                // Senão, cria todas as permissões (conforme o array $permissions).
                foreach ($defaultPermissions as $permission)
                    if (is_null(Permission::where('name', "{$permission} {$value}")->first()))
                        Permission::create(['name' => "{$permission} {$value}", 'guard_name' => 'api']);
            }
        }
    }
}
