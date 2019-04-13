<?php

namespace Tests\Unit\Controllers\Tickets;

use App\Contracts\TicketRepositoryInterface;
use App\Http\Controllers\TicketController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

/**
 * Class DeleteMethodTest
 *
 * @package Tests\Unit\Controllers\Tickets
 */
class DeleteMethodTest extends TestCase
{
    /** @test */
    public function resource_successfully_deleted()
    {
        $id = 100;

        $ticketRepository = Mockery::mock(TicketRepositoryInterface::class);
        $ticketRepository->shouldReceive('delete')->once()
            ->withArgs([$id])
            ->andReturn(true);

        $controller = new TicketController($ticketRepository);
        $response = $controller->delete($id);

        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $this->assertEquals(JsonResponse::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->assertEquals([], $response->getData());
    }

    /** @test */
    public function throws_an_exception_because_a_resource_not_found()
    {
        $this->expectException(ModelNotFoundException::class);

        $id = 10000;

        $ticketRepository = Mockery::mock(TicketRepositoryInterface::class);
        $ticketRepository->shouldReceive('delete')->once()
            ->withArgs([$id])
            ->andThrow(new ModelNotFoundException('Resource not found.', JsonResponse::HTTP_NOT_FOUND));

        $controller = new TicketController($ticketRepository);
        $controller->delete($id);
    }
}
