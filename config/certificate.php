<?php

return [
    'templates' => [
        'default' => [
            'name' => 'Template Default',
            'background' => '/storage/templates/default.jpg',
            'canvas_width' => 3508,
            'canvas_height' => 2480,

            'fields' => [
                'nama' => [
                    'x' => 50,
                    'y' => 45,
                    'font_size' => 80,
                    'align' => 'center',
                    'color' => '#000'
                ],
                'role' => [
                    'x' => 50,
                    'y' => 60,
                    'font_size' => 60,
                    'align' => 'center',
                    'color' => '#333'
                ],
                'certificate_number' => [
                    'x' => 50,
                    'y' => 80,
                    'font_size' => 40,
                    'align' => 'center',
                    'color' => '#000'
                ],
            ],
        ],
    ],
];

