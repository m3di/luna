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

        'auto' => app_path('Luna/Resources'),
        'manual' => [],
    ],

    'tools' => [
        'mode' => 'auto', // or manual

        'auto' => app_path('Luna/Tools'),
        'manual' => [],
    ],

    'views' => [
        'mode' => 'auto', // or manual

        'auto' => app_path('Luna/Views'),
        'manual' => [],
    ],

    'menu' => [
        \Luna\Menu\AllResources::make('منابع', 'fa fa-database'),
    ],
];
