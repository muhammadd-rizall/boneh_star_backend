<?php

return [

    // User permissions
    'users' => [
        'view' => 'users.view',
        'create' => 'users.create',
        'update' => 'users.update',
        'delete' => 'users.delete',
        'restore' => 'users.restore',
        'forceDelete' => 'users.forceDelete',
    ],

    // Role permissions
    'roles' => [
        'view' => 'roles.view',
        'create' => 'roles.create',
        'update' => 'roles.update',
        'delete' => 'roles.delete',
        'restore' => 'roles.restore',
        'forceDelete' => 'roles.forceDelete',
    ],

    // Permission management
    'permissions' => [
        'manage' => 'permissions.manage',
    ],

    //customer permissions
    'customers' => [
        'view' => 'customers.view',
        'create' => 'customers.create',
        'update' => 'customers.update',
        'delete' => 'customers.delete',
        'restore' => 'customers.restore',
        'forceDelete' => 'customers.forceDelete',
    ],
];
