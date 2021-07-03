<?php

return [

    'route_prefix' => 'luna',
    'index_page' => [
        'type' => 'resource',
        'resource' => '',
    ],

    'middleware' => [
        'web',
        'auth',
    ],

    'ui' => [
        'external_help_url_generator' => function ($tree) {
            return false;
        },
    ],

    'resources' => [
        'mode' => 'auto', // or manual

        'auto' => app_path('luna/resources'),
        'manual' => [],
    ],

    'tools' => [
        'mode' => 'auto', // or manual

        'auto' => app_path('luna/tools'),
        'manual' => [],
    ],

    'views' => [
        'mode' => 'auto', // or manual

        'auto' => app_path('luna/views'),
        'manual' => [],
    ],

    'menu' => [
        \Luna\Menu\AllResources::make('منابع', 'fa fa-database'),
    ],
];
