<?php

namespace Database\Seeders;

use App\Models\Video;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Video::create([
            'title' => 'Sample Video 1',
            'url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'status' => 'active',
        ]);

        Video::create([
            'title' => 'Sample Video 2',
            'url' => 'https://www.youtube.com/embed/9bZkp7q19f0',
            'status' => 'active',
        ]);

        Video::create([
            'title' => 'Sample Video 3',
            'url' => 'https://www.youtube.com/embed/jNQXAC9IVRw',
            'status' => 'inactive',
        ]);
    }
}
