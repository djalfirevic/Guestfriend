<?php

use App\Models\Ticket;
use Illuminate\Database\Seeder;

/**
 * Class TicketsTableSeeder
 */
class TicketsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Ticket::class, 50)->create();
    }
}
