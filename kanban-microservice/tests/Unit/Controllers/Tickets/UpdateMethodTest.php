<?php

namespace Tests\Unit\Controllers\Tickets;

use App\Contracts\TicketRepositoryInterface;
use App\Http\Controllers\TicketController;
use App\Models\Lane;
use App\Models\Ticket;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

/**
 * Class UpdateMethodTest
 *
 * @package Tests\Unit\Controllers\Tickets
 */
class UpdateMethodTest extends TestCase
{
    /**
     * @var int
     */
    protected $storedId;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();
        $user = factory(User::class)->create();
        $lane = factory(Lane::class)->create();
        $ticket = factory(Ticket::class)->create(['user_id' => $user->id, 'lane_id' => $lane->id]);
        $this->storedId = $ticket->id;
    }

    /**
     * @test
     * @throws ValidationException
     */
    public function resource_successfully_updated()
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
        $ticket->id = $this->storedId;

        $ticketRepository = Mockery::mock(TicketRepositoryInterface::class);
        $ticketRepository->shouldReceive('update')->once()
            ->withArgs([$this->storedId, $data])
            ->andReturn($ticket);

        $request = new Request();
        $request->merge($data);

        $controller = new TicketController($ticketRepository);
        $response = $controller->update($request, $this->storedId);

        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($ticket->toJson(), json_encode($response->getData()));
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param array $data
     * @throws ValidationException|ModelNotFoundException
     */
    public function throws_an_exception_because_of_invalid_input_params(array $data)
    {
        $this->expectException(ValidationException::class);

        $ticketRepository = Mockery::mock(TicketRepositoryInterface::class);
        $ticketRepository->shouldNotReceive('update');

        $request = new Request();
        $request->merge($data);

        $controller = new TicketController($ticketRepository);
        $controller->update($request, 100);
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
