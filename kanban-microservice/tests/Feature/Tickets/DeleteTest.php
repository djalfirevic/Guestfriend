<?php

namespace Tests\Feature\Tickets;

use App\Models\Lane;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Testing\WithoutMiddleware;
use Tests\TestCase;

/**
 * Class DeleteTest
 *
 * @package Tests\Feature\Tickets
 */
class DeleteTest extends TestCase
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
    public function validInputCase()
    {
        $user = factory(User::class)->create();
        $lane = factory(Lane::class)->create();
        $ticket = factory(Ticket::class)->create(['user_id' => $user->id, 'lane_id' => $lane->id]);

        $this->json('DELETE', $this->url . '/' . $ticket->id);
        $this->assertEmpty($this->response->content());
        $this->assertResponseStatus(JsonResponse::HTTP_NO_CONTENT);

        $this->seeInDatabase('ticket_logs', [
            'ticket_id' => $ticket->id,
            'action'    => Ticket::DELETE_ACTION,
        ]);

        $this->seeInDatabase('tickets', ['id' => $ticket->id]);
        $this->notSeeInDatabase('tickets', ['id' => $ticket->id, 'deleted_at' => null]);
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param $id
     */
    public function invalidInputCases($id)
    {
        $this->json('DELETE', $this->url . '/' . $id);

        $this->assertResponseStatus(JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            // id not found
            [0],

            // id is not a number
            ['abc'],
        ];
    }
}
