<?php

return [
    'fieldset' => 'advanced',
    'name' => 'X-Advanced',
    'class_name' => 'basic-color-style',
    'icon' => 'fa fa-battery-quarter',
    'group' => [
        'header' => [
            'name'   => 'Header',
            'fields' => ['preloader','favicon']
        ],
        'logo'   => [ 
            'name'   => 'Logo',
            'fields' => ['logo','mobile-logo']
        ],
        'backgroud' => [
            'name'   => 'Backgroud Style',
            'fields' => ['bg-chn','bg-image','bg-color']
        ]
    ],
    'fields' => [
        'preloader' => [
            'name' => 'Preloader',
            'desc' => 'Select Preloader Style',
            'type' => 'select',
            'values' => [
                'circle' => 'Circle',
                'double-loop' => 'Double Loop',
                'wave-two' => 'Wave Two',
                'audio-wave' => 'Audio Wave'
            ],
            'std' => ''
        ],
        'favicon' => [
            'name' => 'Upload Favicon',
            'desc' => 'Upload Favicon Here',
            'type' => 'image',
            'std' => ''
        ],
        'logo' => [
            'name' => 'Upload Logo',
            'desc' => 'Upload Logo Here',
            'type' => 'image',
            'std' => ''
        ],
        'mobile-logo' => [
            'name' => 'Upload Mobile Logo',
            'desc' => 'Upload Mobile Logo Here',
            'type' => 'image',
            'std' => ''
        ],
        'bg-chn' => [
            'name' => 'Upload Mobile Logo',
            'desc' => 'Upload Mobile Logo Here',
            'type' => 'select',
            'values' => [
                'color' => 'Color',
                'image' => 'Image'
            ],
            'std' => ''
        ],
        'bg-image' => [
            'name' => 'Upload Mobile Logo',
            'desc' => 'Upload Mobile Logo Here',
            'type' => 'image',
            'std' => ''
        ],
        'bg-color' => [
            'name' => 'Upload Mobile Logo',
            'desc' => 'Upload Mobile Logo Here',
            'type' => 'color',
            'std' => ''
        ]
    ]
];