<?php

namespace Tests\Feature\Tickets;

use App\Models\Lane;
use App\Models\Ticket;
use App\Models\User;
use Faker\Factory;
use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Testing\WithoutMiddleware;
use Tests\TestCase;

/**
 * Class CreateTest
 *
 * @package Tests\Feature\Tickets
 */
class CreateTest extends TestCase
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
        $faker = Factory::create();

        $title = $faker->sentence(5);
        $description = $faker->sentences(3, true);
        $priority = $faker->numberBetween(0, 10);

        $user = factory(User::class)->create();
        $lane = factory(Lane::class)->create();

        $data = [
            'title'       => $title,
            'description' => $description,
            'priority'    => $priority,
            'user_id'     => $user->id,
            'lane_id'     => $lane->id,
        ];

        $this->json('POST', $this->url, $data);

        $this->seeInDatabase('tickets', $data);
        $response = json_decode($this->response->content());
        $this->assertEquals($data['title'], $response->title);
        $this->assertEquals($data['description'], $response->description);
        $this->assertEquals($data['priority'], $response->priority);
        $this->assertEquals($user->name, $response->user_assigned);
        $this->assertEquals($lane->name, $response->status);
        $this->assertResponseStatus(JsonResponse::HTTP_CREATED);
        $this->seeInDatabase('ticket_logs', [
            'ticket_id'    => $response->id,
            'action'       => Ticket::CREATE_ACTION,
            'requested_at' => $response->updated_at,
        ]);
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param $title
     * @param $description
     * @param $priority
     * @param $user_id
     * @param $lane_id
     * @param $responseStatus
     */
    public function invalidInputCases($title, $description, $priority, $user_id, $lane_id, $responseStatus)
    {
        $this->json('POST', $this->url, [
            'title'       => $title,
            'description' => $description,
            'priority'    => $priority,
            'user_id'     => $user_id,
            'lane_id'     => $lane_id,
        ]);

        $this->assertResponseStatus($responseStatus);
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        $faker = Factory::create();

        return [
            // all params are required
            [
                '',
                null,
                '',
                null,
                '',
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            ],

            // invalid title length - failed
            [
                $faker->realText(200),
                $faker->sentences(3, true),
                $faker->numberBetween(0, 10),
                $faker->numberBetween(0, 10),
                $faker->numberBetween(0, 10),
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            ],

            // wrong format of params
            [
                $faker->sentence(5),
                $faker->sentences(3, true),
                'a',
                'b',
                'c',
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            ],

            // not existing user & lane in database
            [
                $faker->sentence(5),
                $faker->sentences(3, true),
                $faker->numberBetween(0, 10),
                0,
                0,
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            ],
        ];
    }
}
