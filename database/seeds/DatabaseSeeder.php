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
        factory(\App\User::class)->create([
            'first_name' => 'Андрес',
            'last_name' => 'Павлюк',
            'patronymic' => 'Игоревич',
            'role' => 'admin',
            'email' => 'admin@admin.com',
        ]);

        $users = factory(\App\User::class, 20)->create();
        $categories = factory(\App\UserCategory::class, 10)->create();

        foreach ($users as $user) {
            $user->user_category_id = $categories->random()->id;
            $user->save();

            factory(\App\Report::class)->create([
                'user_id' => $user->id,
                'type' => 'medical_board'
            ]);

            factory(\App\Report::class)->create([
                'user_id' => $user->id,
                'type' => 'fluorography'
            ]);
        }
    }
}
