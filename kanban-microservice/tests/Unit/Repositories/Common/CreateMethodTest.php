<?php

namespace Tests\Unit\Repositories\Common;

use App\Models\TicketLog;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Builder;
use Mockery;
use Tests\TestCase;

/**
 * Class CreateMethodTest
 *
 * @package Tests\Unit\Repositories\Common
 */
class CreateMethodTest extends TestCase
{
    /** @test */
    public function execute_method()
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('create')->once()->andReturn($builder);

        $model = Mockery::mock(TicketLog::class);
        $model->shouldReceive('newQuery')->once()->andReturn($builder);

        (new UserRepository($model))->create([]);
    }

    /** @test */
    public function real_data()
    {
        $data = factory(User::class)->make()->toArray();

        $this->NotSeeInDatabase('users', [
            'email' => $data['email'],
        ]);

        $result = (new UserRepository(app(User::class)))->create($data)->toArray();
        $this->assertEquals($data['name'], $result['name']);
        $this->assertEquals($data['email'], $result['email']);
    }
}
