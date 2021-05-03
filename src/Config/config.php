<?php

return [

    'route_prefix' => 'luna',


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
        \Luna\Menu\MenuItemAllResources::make('منابع', 'fa fa-database'),
    ]
];
