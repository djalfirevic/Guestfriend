<?php

namespace Tests\Unit\Repositories\Common;

use App\Contracts\UserRepositoryInterface;
use App\Models\TicketLog;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use Tests\TestCase;

/**
 * Class FindWithTrashedMethodTest
 *
 * @package Tests\Unit\Repositories\Common
 */
class FindWithTrashedMethodTest extends TestCase
{
    /** @test */
    public function execute_method()
    {
        $id = 100;

        $model = Mockery::mock(TicketLog::class);
        $model->shouldReceive('newQuery')->once()->andReturn($model);
        $model->shouldReceive('withTrashed')->once()->andReturn($model);
        $model->shouldReceive('findOrFail')->once()->andReturn($model);

        (new UserRepository($model))->findWithTrashed($id);
    }

    /** @test */
    public function real_data()
    {
        $data = factory(User::class)->make()->toArray();
        $user = factory(User::class)->create($data);

        $this->seeInDatabase('users', [
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'api_token'  => $user->api_token,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'deleted_at' => $user->deleted_at,
        ]);

        app(UserRepositoryInterface::class)->delete($user->id);

        $result = app(UserRepositoryInterface::class)->findWithTrashed($user->id);
        $this->assertEquals($user->toArray(), $result->toArray());
    }

    /** @test */
    public function throw_an_exception_if_resource_not_found()
    {
        $this->expectException(ModelNotFoundException::class);

        app(UserRepositoryInterface::class)->findWithTrashed(0);
    }
}
