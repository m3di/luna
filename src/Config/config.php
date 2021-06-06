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

        'auto' => app_path('Luna/Resources/'),
        'manual' => [

        ],
    ],

    'tools' => [
        'mode' => 'auto', // or manual

        'auto' => app_path('Luna/Tools/'),
        'manual' => [

        ],
    ],

    'menu' => [
        \Luna\Menu\AllResources::make('منابع', 'fa fa-database'),
    ],

    'ui' => [
        'action_buttons_text' => true,
    ]
];
