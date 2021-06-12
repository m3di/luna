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

    'ui' => [
        'action_buttons_text' => true,
    ]
];
