<?php


namespace Tests\Unit;

use App\Models\TaskGroup;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class TaskGroupModelTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_taskgroup_creation()
    {
        $taskGroup = new TaskGroup([
            'name' => '',
            'description' => '',
            'status' => 1
        ]);
    
        expect($taskGroup->name)->toBeString();
        expect($taskGroup->description)->toBeString();
        expect($taskGroup->status)->toBe(1);
    }
}



