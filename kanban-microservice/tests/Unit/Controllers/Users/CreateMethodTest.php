<?php

namespace Tests\Unit\Controllers\Users;

use App\Contracts\UserRepositoryInterface;
use App\Http\Controllers\UserController;
use App\Models\User;
use Faker\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

/**
 * Class CreateMethodTest
 *
 * @package Tests\Unit\Controllers\Users
 */
class CreateMethodTest extends TestCase
{
    /**
     * @var string
     */
    protected $storedEmail;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();
        $user = factory(User::class)->create();
        $this->storedEmail = $user->email;
    }

    /**
     * @test
     * @throws ValidationException
     */
    public function resource_successfully_created()
    {
        $user = factory(User::class)->make();
        $data = ['name' => $user->name, 'email' => $user->email];

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldReceive('create')->once()
            ->withArgs([$data])
            ->andReturn($user);

        $request = new Request();
        $request->merge($data);

        $controller = new UserController($userRepository);
        $response = $controller->create($request);

        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals($user->toJson(), json_encode($response->getData()));
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param array $data
     * @throws ValidationException
     */
    public function throws_an_exception_because_of_invalid_input_params(array $data)
    {
        $this->expectException(ValidationException::class);

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldNotReceive('create');

        $request = new Request();
        $request->merge($data);

        $controller = new UserController($userRepository);
        $controller->create($request);
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
