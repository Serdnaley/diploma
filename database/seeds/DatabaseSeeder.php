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
        $admin = factory(\App\User::class)->create([
            'first_name' => 'Андрес',
            'last_name' => 'Павлюк',
            'patronymic' => 'Ігорович',
            'role' => 'admin',
            'email' => 'admin@admin.com',
        ]);

        $manager = factory(\App\User::class)->create([
            'first_name' => 'Юлія',
            'last_name' => 'Арбузова',
            'patronymic' => 'Вікторовна',
            'role' => 'manager',
            'email' => 'manager@admin.com',
        ]);

        $user = factory(\App\User::class)->create([
            'first_name' => 'Богдан',
            'last_name' => 'Орлов',
            'patronymic' => 'Ігорович',
            'role' => 'user',
            'email' => 'user@admin.com',
        ]);

        $users = factory(\App\User::class, 20)->create();
        $categories = factory(\App\UserCategory::class, 10)->create();

//        $users->push($admin);
        $users->push($manager);
        $users->push($user);

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
