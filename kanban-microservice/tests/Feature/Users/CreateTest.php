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
 * Class CreateTest
 *
 * @package Tests\Feature\Users
 */
class CreateTest extends TestCase
{
    use WithoutMiddleware;

    /**
     * @var string
     */
    protected $url;

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
        $faker = Factory::create();
        $this->email = $faker->email;
        factory(User::class)->create(['email' => $this->email]);
        Mail::fake();
    }

    /** @test */
    public function validInputCase()
    {
        $faker = Factory::create();
        $name = $faker->name;
        $email = $faker->email;

        $this->json('POST', $this->url, [
            'name'  => $name,
            'email' => $email,
        ]);

        $this->seeInDatabase('users', ['name' => $name, 'email' => $email]);
        $response = json_decode($this->response->content());
        $this->assertEquals($email, $response->email);
        $this->assertEquals($name, $response->name);
        $this->assertResponseStatus(JsonResponse::HTTP_CREATED);

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
        $this->json('POST', $this->url, [
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
