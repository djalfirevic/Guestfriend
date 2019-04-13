<?php

namespace Tests\Feature\Tickets;

use App\Models\Lane;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Testing\WithoutMiddleware;
use Tests\TestCase;

/**
 * Class GetTest
 *
 * @package Tests\Feature\Tickets
 */
class GetTest extends TestCase
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

        $this->json('GET', $this->url . '/' . $ticket->id);

        $response = json_decode($this->response->content());
        $this->assertEquals($ticket->id, $response->id);
        $this->assertEquals($ticket->title, $response->title);
        $this->assertEquals($ticket->description, $response->description);
        $this->assertEquals($ticket->priority, $response->priority);
        $this->assertEquals($ticket->user->name, $response->user_assigned);
        $this->assertEquals($ticket->lane->name, $response->status);
        $this->assertResponseStatus(JsonResponse::HTTP_OK);
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param $id
     */
    public function invalidInputCases($id)
    {
        $this->json('GET', $this->url . '/' . $id);

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
