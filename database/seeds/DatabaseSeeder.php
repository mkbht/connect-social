<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $faker = Faker\Factory::create();
        foreach (range(1,30) as $index) {
            DB::table('posts')->insert([
                'user_id' => rand(1,2),
                'content' => $faker->realText(rand(100, 1500)),
                //'image' => rand(0,1) == 0 ? $faker->image($dir = 'public/tmp', $width = 640, $height = 480, '', false) : NULL,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }
    }
}
