<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Task;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TaskModelTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_task_creation_specific_date()
    {
        $task = new Task([
            'user_id' => 1,
            'group_id' => null,
            'name' => 'TaskModelTest',
            'event' => Carbon::createFromFormat('Y-m-d', '2024-05-15')->toDateString(),
            'range_from' => '2024-01-01',
            'range_at' => '2024-12-01',
            'iterations' => null,
            'status' => 'pending'
        ]);
    
        expect($task->event)->toBe('2024-05-15');

    }
}

