<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\AlbumImage;
use Illuminate\Database\Seeder;

class AlbumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create gallery albums
        $galleryAlbum1 = Album::create([
            'title' => 'Summer Collection 2025',
            'description' => 'Beautiful summer memories captured in stunning photos',
            'type' => 'gallery',
            'status' => 'active',
            'cover_image' => null,
        ]);

        $galleryAlbum2 = Album::create([
            'title' => 'Nature Photography',
            'description' => 'Exploring the beauty of nature through our lens',
            'type' => 'gallery',
            'status' => 'active',
            'cover_image' => null,
        ]);

        // Create whatwedo albums
        $whatwedoAlbum1 = Album::create([
            'title' => 'Web Development Projects',
            'description' => 'Showcasing our latest web development work',
            'type' => 'whatwedo',
            'status' => 'active',
            'cover_image' => null,
        ]);

        $whatwedoAlbum2 = Album::create([
            'title' => 'Mobile App Development',
            'description' => 'Our portfolio of mobile applications',
            'type' => 'whatwedo',
            'status' => 'active',
            'cover_image' => null,
        ]);

        // Add some sample images to the first gallery album
        for ($i = 1; $i <= 5; $i++) {
            AlbumImage::create([
                'album_id' => $galleryAlbum1->id,
                'image_path' => "sample/image-{$i}.jpg",
                'order' => $i,
            ]);
        }

        $this->command->info('Albums and images seeded successfully!');
        $this->command->info("Gallery Album 1 ID: {$galleryAlbum1->id}");
        $this->command->info("Gallery Album 2 ID: {$galleryAlbum2->id}");
        $this->command->info("WhatWeDo Album 1 ID: {$whatwedoAlbum1->id}");
        $this->command->info("WhatWeDo Album 2 ID: {$whatwedoAlbum2->id}");
    }
}
