<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name'     => 'Admin',
                'email'    => 'admin@example.com',
                'password' => Hash::make('testadmin'),
                'is_admin' => true,
            ],
            [
                'name'     => 'User',
                'email'    => 'user@example.com',
                'password' => Hash::make('testuser'),
            ],
        ];

        foreach ($users as $user) {
            User::firstOrCreate($user);
        }
    }
}
