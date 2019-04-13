<?php

namespace Tests\Feature\Tickets;

use App\Contracts\TicketRepositoryInterface;
use App\Models\Lane;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Testing\WithoutMiddleware;
use Tests\TestCase;

/**
 * Class HistoryTest
 *
 * @package Tests\Feature\Tickets
 */
class HistoryTest extends TestCase
{
    use WithoutMiddleware;

    public function test()
    {
        $users = factory(User::class, 5)->create();
        $lanes = factory(Lane::class, 3)->create();

        $res = [];

        $ticketRepository = app(TicketRepositoryInterface::class);

        // CREATE
        $ticket = $ticketRepository->create([
            'user_id'     => $users[0]->id,
            'lane_id'     => $lanes[0]->id,
            'title'       => 'Some title',
            'description' => 'Some description',
            'priority'    => 5,
        ]);
        $res[0] = [
            'action'       => Ticket::CREATE_ACTION,
            'requested_at' => $ticket->updated_at,
            'details'      => [
                'user_assigned' => $users[0]->name,
                'status'        => $lanes[0]->name,
                'title'         => 'Some title',
                'description'   => 'Some description',
                'priority'      => 5,
            ],
        ];

        // UPDATE 1
        $ticket = $ticketRepository->update($ticket->id, [
            'user_id'  => $users[1]->id,
            'priority' => 3,
        ]);
        $res[1] = [
            'action'       => Ticket::UPDATE_ACTION,
            'requested_at' => $ticket->updated_at,
            'details'      => ['user_assigned' => $users[1]->name, 'priority' => 3],
        ];

        // UPDATE 2
        $ticket = $ticketRepository->update($ticket->id, [
            'user_id' => $users[2]->id,
            'lane_id' => $lanes[1]->id,
        ]);
        $res[2] = [
            'action'       => Ticket::UPDATE_ACTION,
            'requested_at' => $ticket->updated_at,
            'details'      => ['user_assigned' => $users[2]->name, 'status' => $lanes[1]->name],
        ];

        // UPDATE 3
        $ticket = $ticketRepository->update($ticket->id, [
            'user_id' => $users[3]->id,
            'title'   => 'Changed title',
        ]);
        $res[3] = [
            'action'       => Ticket::UPDATE_ACTION,
            'requested_at' => $ticket->updated_at,
            'details'      => ['user_assigned' => $users[3]->name, 'title' => 'Changed title'],
        ];

        // UPDATE 4
        $ticket = $ticketRepository->update($ticket->id, [
            'lane_id'  => $lanes[2]->id,
            'priority' => 1,
        ]);
        $res[4] = [
            'action'       => Ticket::UPDATE_ACTION,
            'requested_at' => $ticket->updated_at,
            'details'      => ['status' => $lanes[2]->name, 'priority' => 1],
        ];

        // UPDATE 5
        $ticket = $ticketRepository->update($ticket->id, [
            'user_id'     => $users[4]->id,
            'description' => 'Changed description',
        ]);
        $res[5] = [
            'action'       => Ticket::UPDATE_ACTION,
            'requested_at' => $ticket->updated_at,
            'details'      => ['user_assigned' => $users[4]->name, 'description' => 'Changed description'],
        ];

        // DELETE
        $ticketRepository->delete($ticket->id);
        $ticket = $ticketRepository->findWithTrashed($ticket->id);
        $res[6] = [
            'action'       => Ticket::DELETE_ACTION,
            'requested_at' => $ticket->updated_at,
        ];

        // TEST
        $this->json('GET', '/tickets/' . $ticket->id . '/history');

        $this->assertResponseStatus(JsonResponse::HTTP_OK);
        $response = $this->response->content();
        $this->assertEquals(
            ['results' => $res],
            json_decode($response, true)
        );
    }
}
