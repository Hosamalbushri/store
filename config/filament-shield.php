<?php

return [
    'shield_resource' => [
        'should_register_navigation' => true,
        'slug' => 'roles',
        'navigation_sort' => -5,
        'navigation_badge' => true,
        'navigation_group' => true,
        'is_globally_searchable' => true,
        'show_model_path' => false,
        'is_scoped_to_tenant' => true,
    ],

    'auth_provider_model' => [
        'fqcn' => 'App\\Models\\User',
    ],

    'super_admin' => [
        'enabled' => true,
        'name' => 'المدير',
        'define_via_gate' => false,
        'intercept_gate' => 'after', // after
    ],

    'panel_user' => [
        'enabled' => true,
        'name' => 'مستخدم',
    ],

    'permission_prefixes' => [
        'resource' => [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
        ],

        'page' => 'page',
        'widget' => 'widget',
    ],

    'entities' => [
        'pages' => true,
        'widgets' => false,
        'resources' => true,
        'custom_permissions' => false,
    ],

    'generator' => [
        'option' => 'policies_and_permissions',
        'policy_directory' => 'Policies',
    ],

    'exclude' => [
        'enabled' => false,

        'pages' => [
            'Dashboard',
        ],

        'widgets' => [
            'AccountWidget',
        ],

        'resources' => [],
    ],

    'discovery' => [
        'discover_all_resources' => false,
        'discover_all_widgets' => false,
        'discover_all_pages' => false,
    ],

    'register_role_policy' => [
        'enabled' => false,
    ],

];
