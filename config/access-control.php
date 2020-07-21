<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Controle de Acesso
    |--------------------------------------------------------------------------
    |
    | Os recursos inseridos no vetor serão transformados em permissões 'listar',
    | 'criar', 'visualizar', 'editar' e 'deletar'. Ex: a partir do recurso
    | 'usuario', serão criadas as permissões 'listar-usuario', 'criar-usuario',
    | 'visualizar-usuario', 'editar-usuario' e 'deletar-usuario'.
    |
    | Caso o array seja associativo, serão criadas apenas as permissões contidas
    | no array. Ex: 'usuario' => ['listar', 'visualizar'] criará as permissões
    | 'listar-usuario' e 'visualizar-usuario'.
    |
    | As permissões são efetivamente criadas no seeder RolesAndPermissionsSeeder.
    |
    */

    'roles' => [
        'Super Administrador',
        'Administrador',
        'Usuário'
    ],

    'permissions' => [
        'Função',
        'Permissão' => [
            'Listar', 'Visualizar'
        ],
        'Usuário' => [
            'Listar', 'Visualizar', 'Editar', 'Deletar'
        ],
    ]
];
