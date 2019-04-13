<?php

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Class UsersTableSeeder
 */
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class)->create([
            'name'      => 'Kanban Microservice',
            'email'     => 'test@gastfreund',
            'api_token' => '1234567890',
        ]);
        factory(User::class, 9)->create();
    }
}
