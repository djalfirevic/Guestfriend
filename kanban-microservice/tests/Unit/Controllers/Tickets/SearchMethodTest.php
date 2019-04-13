<?php

namespace Tests\Unit\Controllers\Tickets;

use App\Contracts\TicketRepositoryInterface;
use App\Exceptions\AppException;
use App\Http\Controllers\TicketController;
use App\Models\Lane;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

/**
 * Class SearchMethodTest
 *
 * @package Tests\Unit\Controllers\Tickets
 */
class SearchMethodTest extends TestCase
{
    /**
     * @test
     * @throws AppException
     */
    public function results_successfully_found()
    {
        $tickets = [];

        $user = factory(User::class)->create();
        $lane = factory(Lane::class)->create();
        $tickets[] = factory(Ticket::class)->make(['user_id' => $user->id, 'lane_id' => $lane->id]);

        $user = factory(User::class)->create();
        $lane = factory(Lane::class)->create();
        $tickets[] = factory(Ticket::class)->make(['user_id' => $user->id, 'lane_id' => $lane->id]);

        $data = [
            'query'    => 'a',
            'filter'   => ['title' => 'some title', 'description' => 'some description'],
            'order_by' => 'priority',
            'sorting'  => 'asc',
            'page'     => 2,
            'limit'    => 4,
        ];

        $ticketRepository = Mockery::mock(TicketRepositoryInterface::class);
        $ticketRepository->shouldReceive('search')->once()
            ->withArgs([$data])
            ->andReturn($tickets);

        $request = new Request();
        $request->merge($data);

        $controller = new TicketController($ticketRepository);
        $response = $controller->search($request);

        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(json_encode($tickets), json_encode($response->getData()));
    }

    /**
     * @test
     * @throws AppException
     */
    public function throws_an_exception_because_of_invalid_input_params()
    {
        $this->expectException(AppException::class);

        $data = [
            'query'    => 'a',
            'filter'   => ['title' => 'some title', 'description' => 'some description'],
            'order_by' => 'priority',
            'sorting'  => 'asc',
            'page'     => 2,
            'limit'    => 4,
        ];

        $ticketRepository = Mockery::mock(TicketRepositoryInterface::class);
        $ticketRepository->shouldReceive('search')->once()
            ->withArgs([$data])
            ->andThrow(new AppException('Invalid input.', JsonResponse::HTTP_NOT_FOUND));

        $request = new Request();
        $request->merge($data);

        $controller = new TicketController($ticketRepository);
        $controller->search($request);
    }
}
