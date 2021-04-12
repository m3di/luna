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

];