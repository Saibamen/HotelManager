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
                'is_admin' => true,
            ],
            [
                'name'     => 'User',
                'email'    => 'user@example.com',
            ],
        ];

        $passwords = [
            ['password' => Hash::make('testadmin')],
            ['password' => Hash::make('testuser')],
        ];

        foreach ($users as $key => $value) {
            User::firstOrCreate($value, $passwords[$key]);
        }
    }
}
