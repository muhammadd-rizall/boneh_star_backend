<?php

return [

    // User permissions
    'users' => [
        'view' => 'users.view',
        'create' => 'users.create',
        'update' => 'users.update',
        'delete' => 'users.delete',
    ],

    // Role permissions
    'roles' => [
        'view' => 'roles.view',
        'create' => 'roles.create',
        'update' => 'roles.update',
        'delete' => 'roles.delete',
    ],

    // Permission management
    'permissions' => [
        'manage' => 'permissions.manage',
    ],
];
