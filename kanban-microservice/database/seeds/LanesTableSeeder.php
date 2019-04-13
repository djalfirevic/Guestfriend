<?php

use App\Models\Lane;
use Illuminate\Database\Seeder;

/**
 * Class LanesTableSeeder
 */
class LanesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Lane::class)->create(['name' => 'To Do']);
        factory(Lane::class)->create(['name' => 'In Progress']);
        factory(Lane::class)->create(['name' => 'Done']);
    }
}
