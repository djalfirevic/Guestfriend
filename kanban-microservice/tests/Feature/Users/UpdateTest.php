<?php

namespace Tests\Feature\Users;

use App\Mails\UserMail;
use App\Models\User;
use Faker\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Laravel\Lumen\Testing\WithoutMiddleware;
use Tests\TestCase;

/**
 * Class UpdateTest
 *
 * @package Tests\Feature\Users
 */
class UpdateTest extends TestCase
{
    use WithoutMiddleware;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $email;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->url = '/users';
        $user = factory(User::class)->create();
        $this->id = $user->id;
        $faker = Factory::create();
        $this->email = $faker->email;
        factory(User::class)->create(['email' => $this->email]);
        Mail::fake();
    }

    /** @test */
    public function validInputCaseAllData()
    {
        $faker = Factory::create();
        $name = $faker->name;
        $email = $faker->email;

        $this->json('PUT', $this->url . '/' . $this->id, [
            'name'  => $name,
            'email' => $email,
        ]);

        $response = json_decode($this->response->content());
        $this->assertEquals($this->id, $response->id);
        $this->assertEquals($email, $response->email);
        $this->assertEquals($name, $response->name);
        $this->assertResponseStatus(JsonResponse::HTTP_OK);

        Mail::assertSent(UserMail::class);
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param $name
     * @param $email
     * @param $responseStatus
     */
    public function invalidInputCases($name, $email, $responseStatus)
    {
        $this->json('PUT', $this->url . '/' . $this->id, [
            'name'  => $name,
            'email' => $email,
        ]);

        $this->assertResponseStatus($responseStatus);

        Mail::assertNotQueued(UserMail::class);
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        $faker = Factory::create();

        return [
            // email already exists - failed
            [
                $faker->name,
                $this->email,
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            ],

            // name & email are required - failed
            [
                '',
                null,
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            ],

            // invalid name length - failed
            [
                $faker->realText(200),
                $faker->email,
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            ],
        ];
    }
}
