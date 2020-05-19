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
            'first_name' => 'Admin',
            'last_name' => '',
            'patronymic' => '',
            'role' => 'admin',
            'email' => 'admin@admin.com',
        ]);

        $users = collect();
        $categories = collect();

        $fake_users_json = File::get(database_path('seeds/users.json'));
        $fake_categories_json = File::get(database_path('seeds/categories.json'));
        $fake_users = json_decode($fake_users_json) ?? [];
        $fake_categories = json_decode($fake_categories_json) ?? [];

        foreach ($fake_categories as $fake_category) {
            $categories->push(
                factory(\App\UserCategory::class)->create([
                    'name' => $fake_category
                ])
            );
        }

        foreach ($fake_users as $fake_user) {
            list($last_name, $first_name, $patronymic) = explode(' ', $fake_user->name);
            $preset = [];

            $preset['last_name'] = $last_name;
            $preset['first_name'] = $first_name;
            $preset['patronymic'] = $patronymic;
            $preset['user_category_id'] = $categories->random()->id;

            if ($fake_user->email)
                $preset['email'] = $fake_user->email;

            $users->push(factory(\App\User::class)->create($preset));
        }

        foreach ($users as $user) {
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
