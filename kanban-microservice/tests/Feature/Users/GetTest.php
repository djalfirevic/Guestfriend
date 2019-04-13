<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Testing\WithoutMiddleware;
use Tests\TestCase;

/**
 * Class GetTest
 *
 * @package Tests\Feature\Users
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

        $this->url = '/users';
    }

    /** @test */
    public function validInputCase()
    {
        $user = factory(User::class)->create();

        $this->json('GET', $this->url . '/' . $user->id);

        $response = json_decode($this->response->content());
        $this->assertEquals($user->id, $response->id);
        $this->assertEquals($user->email, $response->email);
        $this->assertEquals($user->name, $response->name);
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
