<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('reviews')->insert([
            [
                'name' => 'John Doe',
                'message' => 'This is a great product!',
                'status' => 'active',
                'rating' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jane Smith',
                'message' => 'I found this service very useful.',
                'status' => 'active',
                'rating' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sam Wilson',
                'message' => 'Not satisfied with the quality.',
                'status' => 'active',
                'rating' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Alice Johnson',
                'message' => 'The customer service was excellent!',
                'status' => 'active',
                'rating' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Robert Brown',
                'message' => 'Delivery was late, but the product is good.',
                'status' => 'active',
                'rating' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Emily Davis',
                'message' => 'This exceeded my expectations. Highly recommend!',
                'status' => 'active',
                'rating' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Michael Green',
                'message' => 'Average experience, nothing special.',
                'status' => 'inactive',
                'rating' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sophia Taylor',
                'message' => 'The product was damaged upon arrival.',
                'status' => 'active',
                'rating' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Liam Martinez',
                'message' => 'Fantastic! Will definitely buy again.',
                'status' => 'active',
                'rating' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Olivia Hernandez',
                'message' => 'Too expensive for the quality offered.',
                'status' => 'inactive',
                'rating' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
