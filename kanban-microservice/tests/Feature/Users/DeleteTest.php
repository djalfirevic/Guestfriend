<?php

namespace Tests\Feature\Users;

use App\Mails\UserMail;
use App\Models\Lane;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Laravel\Lumen\Testing\WithoutMiddleware;
use Tests\TestCase;

/**
 * Class DeleteTest
 *
 * @package Tests\Feature\Users
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

        $this->url = '/users';
        Mail::fake();
    }

    /** @test */
    public function validInputCase()
    {
        $this->assertTrue(true);

        $user = factory(User::class)->create();
        $lane = factory(Lane::class)->create();
        $ticket1 = factory(Ticket::class)->create(['user_id' => $user->id, 'lane_id' => $lane->id]);
        $ticket2 = factory(Ticket::class)->create(['user_id' => $user->id, 'lane_id' => $lane->id]);

        $this->json('DELETE', $this->url . '/' . $user->id);
        $this->assertEmpty($this->response->content());
        $this->assertResponseStatus(JsonResponse::HTTP_NO_CONTENT);

        Mail::assertSent(UserMail::class);

        $this->seeInDatabase('users', ['id' => $user->id]);
        $this->notSeeInDatabase('users', ['id' => $user->id, 'deleted_at' => null]);

        $this->seeInDatabase('tickets', ['id' => $ticket1->id]);
        $this->notSeeInDatabase('tickets', ['id' => $ticket1->id, 'deleted_at' => null]);

        $this->seeInDatabase('tickets', ['id' => $ticket2->id]);
        $this->notSeeInDatabase('tickets', ['id' => $ticket2->id, 'deleted_at' => null]);
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

        Mail::assertNotQueued(UserMail::class);
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
