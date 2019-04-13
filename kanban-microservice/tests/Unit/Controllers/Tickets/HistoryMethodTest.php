<?php

namespace Tests\Unit\Controllers\Tickets;

use App\Contracts\FindTicketHistoryRepositoryInterface;
use App\Http\Controllers\TicketLogController;
use App\Transformers\TicketLogTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

/**
 * Class HistoryMethodTest
 *
 * @package Tests\Unit\Controllers\Tickets
 */
class HistoryMethodTest extends TestCase
{
    /** @test */
    public function throws_an_exception_because_a_resource_not_found()
    {
        $this->expectException(ModelNotFoundException::class);

        $id = 100;

        $historyRepository = Mockery::mock(FindTicketHistoryRepositoryInterface::class);
        $historyRepository->shouldReceive('findHistory')->once()
            ->withArgs([$id])
            ->andThrow(new ModelNotFoundException('Resource not found.', JsonResponse::HTTP_NOT_FOUND));

        $historyTransformer = Mockery::mock(TicketLogTransformer::class);
        $historyTransformer->shouldNotReceive('transform');

        $controller = new TicketLogController($historyRepository, $historyTransformer);
        $controller->history($id);
    }
}
