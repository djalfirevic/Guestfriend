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
 * Class UpdateMethodTest
 *
 * @package Tests\Unit\Repositories\Common
 */
class UpdateMethodTest extends TestCase
{
    /** @test */
    public function execute_method()
    {
        $id = 100;

        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('findOrFail')->once()->andReturn($builder);
        $builder->shouldReceive('update')->once()->andReturn($builder);

        $model = Mockery::mock(TicketLog::class);
        $model->shouldReceive('newQuery')->once()->andReturn($builder);

        (new UserRepository($model))->update($id, []);
    }

    /** @test */
    public function real_data()
    {
        $user = factory(User::class)->create();
        $data = factory(User::class)->make()->toArray();

        $this->seeInDatabase('users', [
            'id'    => $user->id,
            'email' => $user->email,
            'name'  => $user->name,
        ]);

        $result = app(UserRepositoryInterface::class)->update($user->id, $data)->toArray();

        $this->seeInDatabase('users', [
            'id'    => $user->id,
            'email' => $data['email'],
            'name'  => $data['name'],
        ]);

        $this->assertEquals($data['name'], $result['name']);
        $this->assertEquals($data['email'], $result['email']);
    }

    /** @test */
    public function throw_an_exception_if_resource_not_found()
    {
        $this->expectException(ModelNotFoundException::class);

        app(UserRepositoryInterface::class)->update(0, []);
    }
}
