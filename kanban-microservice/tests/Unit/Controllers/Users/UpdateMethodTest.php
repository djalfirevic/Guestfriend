<?php

namespace Tests\Unit\Controllers\Users;

use App\Contracts\UserRepositoryInterface;
use App\Http\Controllers\UserController;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

/**
 * Class UpdateMethodTest
 *
 * @package Tests\Unit\Controllers\Users
 */
class UpdateMethodTest extends TestCase
{
    /**
     * @var string
     */
    protected $storedEmail;

    /**
     * @var int
     */
    protected $storedId;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();
        $user = factory(User::class)->create();
        $this->storedEmail = $user->email;
        $this->storedId = $user->id;
    }

    /**
     * @test
     * @throws ValidationException
     */
    public function resource_successfully_updated()
    {
        $user = factory(User::class)->make();
        $data = ['name' => $user->name, 'email' => $user->email];
        $user->id = $this->storedId;

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldReceive('update')->once()
            ->withArgs([$this->storedId, $data])
            ->andReturn($user);

        $request = new Request();
        $request->merge($data);

        $controller = new UserController($userRepository);
        $response = $controller->update($request, $this->storedId);

        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($user->toJson(), json_encode($response->getData()));
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param array $data
     * @throws ValidationException|ModelNotFoundException
     */
    public function throws_an_exception_because_of_invalid_input_params(array $data)
    {
        $this->expectException(ValidationException::class);

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldNotReceive('update');

        $request = new Request();
        $request->merge($data);

        $controller = new UserController($userRepository);
        $controller->update($request, 100);
    }

    /**
     * @return array
     */
    public function dataProvider(): array
    {
        $faker = Factory::create();

        return [
            // parameters are required
            [[]],

            // invalid email param
            [['name' => 'aaa', 'email' => 'bbb']],

            // input name too long
            [['name' => Str::random(200), 'email' => $faker->email]],

            // email already exists
            [['name' => $faker->name, 'email' => $this->storedEmail]],
        ];
    }
}
