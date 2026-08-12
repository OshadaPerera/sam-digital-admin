<?php

namespace App\Http\Controllers\API;

class GalleryController extends BaseAlbumController
{
    /**
     * Get the album type for this controller
     */
    protected function getAlbumType(): string
    {
        return 'gallery';
    }
}
