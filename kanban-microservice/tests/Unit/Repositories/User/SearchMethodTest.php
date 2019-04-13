<?php

namespace Tests\Unit\Repositories\Users;

use App\Exceptions\AppException;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Builder;
use Mockery;
use Tests\TestCase;

/**
 * Class SearchMethodTest
 *
 * @package Tests\Unit\Repositories\Users
 */
class SearchMethodTest extends TestCase
{
    /**
     * @test
     * @throws AppException
     */
    public function run_all_input_data_search()
    {
        $searchable = ['id', 'name', 'email', 'created_at', 'updated_at'];
        $data = [
            'query'    => 'a',
            'filter'   => ['name' => 'user', 'email' => 'com'],
            'order_by' => 'email',
            'sorting'  => 'asc',
            'page'     => 3,
            'limit'    => 3,
        ];

        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('where')->times(3)->andReturn($builder);
        $builder->shouldReceive('orderBy')->once()->andReturn($builder);
        $builder->shouldReceive('offset')->once()->andReturn($builder);
        $builder->shouldReceive('limit')->once()->andReturn($builder);
        $builder->shouldReceive('get')->once()->andReturn($builder);
        $builder->shouldReceive('toArray')->once()->andReturn([]);

        $model = Mockery::mock(User::class);
        $model->shouldReceive('newQuery')->once()->andReturn($builder);
        $model->shouldReceive('getSearchableAttributes')->once()->andReturn($searchable);

        (new UserRepository($model))->search($data);
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param array $searchable
     * @param array $data
     * @throws AppException
     */
    public function throw_an_exception_if_input_param_not_searchable(array $searchable, array $data)
    {
        $builder = Mockery::mock(Builder::class);

        $model = Mockery::mock(User::class);
        $model->shouldReceive('newQuery')->once()->andReturn($builder);
        $model->shouldReceive('getSearchableAttributes')->once()->andReturn($searchable);

        $this->expectException(AppException::class);

        (new UserRepository($model))->search($data);
    }


    public function dataProvider()
    {
        return [
            // undefined filter param
            [
                'searchable' => ['id', 'name', 'email', 'created_at', 'updated_at'],
                'data'       => ['filter' => ['something' => 100]],
            ],

            // undefined sorting param
            [
                'searchable' => ['id', 'name', 'email', 'created_at', 'updated_at'],
                'data'       => ['sorting' => 'aaa'],
            ],

            // undefined order_by param
            [
                'searchable' => ['id', 'name', 'email', 'created_at', 'updated_at'],
                'data'       => ['order_by' => 'something'],
            ],
        ];
    }
}
