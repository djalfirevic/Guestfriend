<?php

namespace Tests\Feature\Tickets;

use App\Contracts\TicketRepositoryInterface;
use App\Models\Lane;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Testing\WithoutMiddleware;
use Mockery;
use Tests\TestCase;

/**
 * Class SearchTest
 *
 * @package Tests\Feature\Tickets
 */
class SearchTest extends TestCase
{
    use WithoutMiddleware;

    /**
     * @var string
     */
    protected $url;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->url = '/tickets';
    }

    /** @test */
    public function validCase()
    {
        $user = factory(User::class)->create();
        $lane = factory(Lane::class)->create();
        $tickets = factory(Ticket::class, 10)->create(['user_id' => $user->id, 'lane_id' => $lane->id]);

        $ticketRepository = Mockery::mock(TicketRepositoryInterface::class);
        $ticketRepository->shouldReceive('search')->times(1)->andReturn($tickets);
        app()->bind(TicketRepositoryInterface::class, function () use ($ticketRepository) {
            return $ticketRepository;
        });

        $this->json('GET', $this->url);

        $this->assertResponseStatus(JsonResponse::HTTP_OK);
    }
}
