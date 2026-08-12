<?php

namespace App\Http\Controllers\API;

class WhatWeDoController extends BaseAlbumController
{
    /**
     * Get the album type for this controller
     */
    protected function getAlbumType(): string
    {
        return 'whatwedo';
    }
}
