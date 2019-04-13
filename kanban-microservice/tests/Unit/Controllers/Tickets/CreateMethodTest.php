<?php

namespace Tests\Unit\Controllers\Tickets;

use App\Contracts\TicketRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Http\Controllers\TicketController;
use App\Models\Lane;
use App\Models\Ticket;
use App\Models\User;
use Faker\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

/**
 * Class CreateMethodTest
 *
 * @package Tests\Unit\Controllers\Tickets
 */
class CreateMethodTest extends TestCase
{
    /**
     * @test
     * @throws ValidationException
     */
    public function resource_successfully_created()
    {
        $user = factory(User::class)->create();
        $lane = factory(Lane::class)->create();
        $ticket = factory(Ticket::class)->make(['user_id' => $user->id, 'lane_id' => $lane->id]);

        $data = [
            'title'       => $ticket->title,
            'description' => $ticket->description,
            'priority'    => $ticket->priority,
            'user_id'     => $ticket->user_id,
            'lane_id'     => $ticket->lane_id,
        ];

        $ticketRepository = Mockery::mock(TicketRepositoryInterface::class);
        $ticketRepository->shouldReceive('create')->once()
            ->withArgs([$data])
            ->andReturn($ticket);

        $request = new Request();
        $request->merge($data);

        $controller = new TicketController($ticketRepository);
        $response = $controller->create($request);

        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals($ticket->toJson(), json_encode($response->getData()));
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param array $data
     * @throws ValidationException
     */
    public function throws_an_exception_because_of_invalid_input_params(array $data)
    {
        $this->expectException(ValidationException::class);

        $ticketRepository = Mockery::mock(TicketRepositoryInterface::class);
        $ticketRepository->shouldNotReceive('create');

        $request = new Request();
        $request->merge($data);

        $controller = new TicketController($ticketRepository);
        $controller->create($request);
    }

    /**
     * @return array
     */
    public function dataProvider(): array
    {
        $faker = Factory::create();

        return [
            // parameters are required
            [[]],

            // input name too long
            [
                [
                    'name'        => Str::random(200),
                    'description' => $faker->sentence,
                    'priority'    => $faker->randomNumber(1),
                    'lane_id'     => $faker->randomNumber(1),
                    'user_id'     => $faker->randomNumber(1),
                ],
            ],

            // wrong priority, lane_id & user_id format
            [
                [
                    'name'        => $faker->sentence,
                    'description' => $faker->sentence,
                    'priority'    => $faker->text,
                    'lane_id'     => $faker->text,
                    'user_id'     => $faker->text,
                ],
            ],
        ];
    }
}
