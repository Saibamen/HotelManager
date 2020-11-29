<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment() !== 'testing') {
            $this->call([
                UsersTableSeeder::class,
            ]);
        }
    }
}
