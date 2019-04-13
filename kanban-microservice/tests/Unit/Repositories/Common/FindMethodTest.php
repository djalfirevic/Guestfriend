<?php

namespace Tests\Unit\Repositories\Common;

use App\Models\TicketLog;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;
use Tests\TestCase;

/**
 * Class FindMethodTest
 *
 * @package Tests\Unit\Repositories\Common
 */
class FindMethodTest extends TestCase
{
    /** @test */
    public function execute_method()
    {
        $id = 100;

        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('findOrFail')->once()->andReturn($builder);

        $model = Mockery::mock(TicketLog::class);
        $model->shouldReceive('newQuery')->once()->andReturn($builder);

        (new UserRepository($model))->find($id);
    }

    /** @test */
    public function real_data()
    {
        $user = factory(User::class)->create();

        $this->seeInDatabase('users', [
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'api_token'  => $user->api_token,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'deleted_at' => $user->deleted_at,
        ]);

        $result = (new UserRepository(app(User::class)))->find($user->id);
        $this->assertEquals($user->toArray(), $result->toArray());
    }

    /** @test */
    public function throw_an_exception_if_resource_not_found()
    {
        $this->expectException(ModelNotFoundException::class);

        (new UserRepository(app(User::class)))->find(0);
    }
}
