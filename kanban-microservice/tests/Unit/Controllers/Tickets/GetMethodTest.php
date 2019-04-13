<?php

namespace Tests\Unit\Controllers\Tickets;

use App\Contracts\TicketRepositoryInterface;
use App\Http\Controllers\TicketController;
use App\Models\Lane;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

/**
 * Class GetMethodTest
 *
 * @package Tests\Unit\Controllers\Tickets
 */
class GetMethodTest extends TestCase
{
    /** @test */
    public function resource_successfully_found()
    {
        $id = 100;
        $user = factory(User::class)->create();
        $lane = factory(Lane::class)->create();
        $ticket = factory(Ticket::class)->create(['user_id' => $user->id, 'lane_id' => $lane->id]);

        $ticketRepository = Mockery::mock(TicketRepositoryInterface::class);
        $ticketRepository->shouldReceive('find')->once()
            ->withArgs([$id])
            ->andReturn($ticket);

        $controller = new TicketController($ticketRepository);
        $response = $controller->get($id);

        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($ticket->toJson(), json_encode($response->getData()));
    }

    /** @test */
    public function throws_an_exception_because_a_resource_not_found()
    {
        $this->expectException(ModelNotFoundException::class);

        $id = 100;

        $ticketRepository = Mockery::mock(TicketRepositoryInterface::class);
        $ticketRepository->shouldReceive('find')->once()
            ->withArgs([$id])
            ->andThrow(new ModelNotFoundException('Resource not found.', JsonResponse::HTTP_NOT_FOUND));

        $controller = new TicketController($ticketRepository);
        $controller->get($id);
    }
}
