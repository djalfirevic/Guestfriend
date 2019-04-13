<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

use App\Models\Lane;
use App\Models\Ticket;
use App\Models\TicketLog;
use App\Models\User;
use Faker\Generator;

/** @var $factory Illuminate\Database\Eloquent\Factory */

$factory->define(User::class, function (Generator $faker) {
    return [
        'name'      => $faker->name,
        'email'     => $faker->email,
        'api_token' => User::generateApiToken(),
    ];
});

$factory->define(Lane::class, function (Generator $faker) {
    return [
        'name' => $faker->randomElement(['To Do', 'In Progress', 'Done']),
    ];
});

$factory->define(Ticket::class, function (Generator $faker) {
    return [
        'title'       => $faker->sentence(5),
        'description' => $faker->sentences(3, true),
        'priority'    => $faker->numberBetween(0, 10),
        'user_id'     => $faker->numberBetween(1, 10),
        'lane_id'     => $faker->numberBetween(1, 3),
    ];
});

$factory->define(TicketLog::class, function (Generator $faker) {
    return [
        'ticket_id' => $faker->numberBetween(1, 10),
        'action'    => $faker->randomElement([Ticket::CREATE_ACTION, Ticket::UPDATE_ACTION, Ticket::DELETE_ACTION]),
        'details'   => json_encode([
            'title'       => $faker->sentence(5),
            'description' => $faker->sentences(3, true),
            'priority'    => $faker->numberBetween(0, 10),
            'user_id'     => $faker->numberBetween(1, 10),
            'lane_id'     => $faker->numberBetween(1, 3),
        ]),
    ];
});
