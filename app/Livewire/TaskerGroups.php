<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TaskGroup;

class TaskerGroups extends Component
{

    public $tasksGroups;
    public $showNewTaskGroup;

    public $name;
    public $description;

    public function render()
    {
        return view('livewire.tasker-groups');
    }

    public function mount()
    {
        $this->showNewTaskGroup = 0;
        $this->getAllTasksGroups();
    }

    public function getAllTasksGroups()
    {
        $this->tasksGroups = TaskGroup::where('status', 1)->get();
    }

    public function toggleNewTaskGroup()
    {
        $this->showNewTaskGroup = 1 - $this->showNewTaskGroup;
    }

    public function storeTaskGroup()
    {
        TaskGroup::create([
            'name' => $this->name,
            'description' => $this->description,
            'status' => 1
        ]);

        $this->showNewTaskGroup = 0;
        $this->reset();
        $this->getAllTasksGroups();
    }

    public function removeTaskGroup($groupId)
    {
        TaskGroup::where('id', $groupId)->update(['status' => 0]);
        $this->getAllTasksGroups();
    }
}
