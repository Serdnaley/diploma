<?php

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
        factory(\App\User::class)->create([
            'first_name' => 'Андрес',
            'last_name' => 'Павлюк',
            'patronymic' => 'Игоревич',
            'role' => 'admin',
            'email' => 'serdnaley@gmail.com',
        ]);

        factory(\App\User::class, 20)->create();
    }
}
