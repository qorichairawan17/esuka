<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Optimization Settings
    |--------------------------------------------------------------------------
    |
    | This file contains optimization settings for the application
    |
    */

    'view_cache' => env('VIEW_CACHE_ENABLED', true),
    'route_cache' => env('ROUTE_CACHE_ENABLED', true),
    'config_cache' => env('CONFIG_CACHE_ENABLED', true),
    'event_cache' => env('EVENT_CACHE_ENABLED', true),
    
    // Image optimization
    'image_quality' => env('IMAGE_QUALITY', 80),
    'image_max_width' => env('IMAGE_MAX_WIDTH', 1920),
    'image_max_height' => env('IMAGE_MAX_HEIGHT', 1080),
    
];
