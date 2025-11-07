<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Image Processing Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how images are processed, compressed, and stored.
    |
    */

    'cover_image' => [
        'max_width' => env('IMAGE_COVER_MAX_WIDTH', 1200),
        'max_height' => env('IMAGE_COVER_MAX_HEIGHT', 800),
        'quality' => env('IMAGE_COVER_QUALITY', 85),
    ],

    'album_image' => [
        'max_width' => env('IMAGE_ALBUM_MAX_WIDTH', 1920),
        'max_height' => env('IMAGE_ALBUM_MAX_HEIGHT', 1080),
        'quality' => env('IMAGE_ALBUM_QUALITY', 80),
    ],

    'thumbnail' => [
        'width' => env('IMAGE_THUMBNAIL_WIDTH', 300),
        'height' => env('IMAGE_THUMBNAIL_HEIGHT', 300),
        'quality' => env('IMAGE_THUMBNAIL_QUALITY', 75),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default WebP Quality
    |--------------------------------------------------------------------------
    |
    | Default quality for WebP conversion (0-100)
    | Higher values mean better quality but larger file sizes.
    | Recommended: 80-85 for general use
    |
    */
    'default_quality' => env('IMAGE_DEFAULT_QUALITY', 80),

    /*
    |--------------------------------------------------------------------------
    | Image Format
    |--------------------------------------------------------------------------
    |
    | The output format for processed images.
    | Options: webp, jpg, png
    |
    */
    'format' => env('IMAGE_FORMAT', 'webp'),
];
