<?php

namespace Tests\Unit\Repositories\Tickets;

use App\Models\TicketLog;
use App\Repositories\TicketLogRepository;
use Illuminate\Database\Eloquent\Builder;
use Mockery;
use Tests\TestCase;

/**
 * Class FindHistoryMethodTest
 *
 * @package Tests\Unit\Repositories\Tickets
 */
class FindHistoryMethodTest extends TestCase
{
    /** @test */
    public function run_all_input_data_search()
    {
        $id = 100;

        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('where')->once()->andReturn($builder);
        $builder->shouldReceive('get')->once()->andReturn($builder);

        $model = Mockery::mock(TicketLog::class);
        $model->shouldReceive('newQuery')->once()->andReturn($builder);

        (new TicketLogRepository($model))->findHistory($id);
    }
}
