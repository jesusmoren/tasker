<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Task;
use Livewire\Component;
use App\Models\TaskGroup;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;

class Tasker extends Component
{
    // Select variables.
    public $tasksGroups;
    public $daysmonth;
    public $months;
    public $daysweek;
    public $message;
    public $newTaskSection;
    public $today;
    public $periodicity;

    // Tasks variables.
    public $tasksToday;
    public $tasksTomorrow;
    public $tasksThisWeek;
    public $tasksNextWeek;
    public $tasksNextMonth;
    public $tasksNextMonths;
    
    // Variables for selected values.
    public $groupId;

    #[Validate('required')] 
    public $name;

    public $cronDaysMonth;
    public $cronMonths;
    public $cronDaysWeek;
    public $cronYears;
    #[Validate('required_without_all:rangeAt,iterations')]
    public $rangeFrom;
    #[Validate('required_without_all:rangeFrom,iterations')]
    public $rangeAt;
    #[Validate('required_without_all:rangeAt,rangeFrom')]
    public $iterations;
    public $iterationsDate;

    // Variable for the new task.
    public $newTask;

    public function render()
    {
        return view('livewire.tasker');
    }

    public function mount()
    {
        $this->groupId = null;
        $this->today = Carbon::now()->toDateString();
        $this->newTaskSection = 0;
        $this->periodicity = false;
        $this->tasksGroups = TaskGroup::where('status', 1)
        ->get()
        ->pluck('name', 'id')
        ->prepend('', '')
        ->toArray();

        $this->daysweek = [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            0 => 'Sunday'
        ];
        $this->months = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];
        $this->daysmonth = range(1, 31);

        $this->cronDaysMonth = [];
        $this->cronMonths = [];
        $this->cronDaysWeek = [];

        $this->getAllTasks();
    }

    public function getAllTasks()
    {
        // Tasks Today
        $now = Carbon::now();
        
        $this->tasksToday = Task::where('status', 'pending')->where('event', $now->toDateString())->orderBy('event', 'asc')->get();
        /* $this->tasksToday = Task::where('status', 'pending')
        ->where(function($query, $now){
            $query->where('periodic_daymonth', $now->day)
            ->orWhere('periodic_dayweek', $now->dayofweek);
        })->get(); */

        // Tasks Tomorrow
        $tomorrow = $now->copy()->addDay();
        $this->tasksTomorrow = Task::where('status', 'pending')->where('event', $tomorrow->toDateString())->orderBy('event', 'asc')->get();
        
        // Tasks This Week
        $thisWeek = $now->copy()->addDays(2);

        // Check if the date is still in the same week.
        if($thisWeek->isCurrentWeek())
        {
            $this->tasksThisWeek = Task::where('status', 'pending')->whereBetween('event', [$thisWeek->toDateString(), $thisWeek->endOfWeek()->toDateString()])->orderBy('event', 'asc')->get();
        } else {
            $this->tasksThisWeek = [];
        }

        // Tasks Next Weeks
        $nextWeek = $now->copy()->addWeek();
        $this->tasksNextWeek = Task::where('status', 'pending')->whereMonth('event', $nextWeek->startOfWeek()->month)->whereBetween('event', [$nextWeek->startOfWeek()->toDateString(), $nextWeek->endOfMonth()->toDateString()])->orderBy('event', 'asc')->get();

        // Tasks in the near future.
        // Since "in the near future" is not an specific point in time, I use "Next Month" for this category.
        // Used addMonthNoOverflow to avoid bugs when jumping to a month with less days. For example, 31 of January to February which only has 28 days.
        $nextMonth = $now->copy()->addMonthNoOverflow();
        $this->tasksNextMonth = Task::where('status', 'pending')->whereYear('event', $nextMonth->year)->whereMonth('event', $nextMonth->month)->orderBy('event', 'asc')->get();

        // Tasks in the future
        // Since "in the future" has no end, im collecting here all the tasks that are not in the previous section, I mean, 
        // all the tasks from the next month in advance.
        $nextMonths = $nextMonth->copy()->addMonthNoOverflow()->startOfMonth();
        $this->tasksNextMonths = Task::where('status', 'pending')->where('event', '>=', $nextMonths->toDateString())->orderBy('event', 'asc')->get();
        

    }

    public function toggleNewTask()
    {
        $this->newTaskSection = 1 - $this->newTaskSection;
    }

    public function storeNewTask()
    {
        $this->validate();
        if(empty($this->cronMonths)) $this->cronMonths = range(1,12);
        if(empty($this->cronDaysWeek))
        {
            $this->cronDaysWeek = range(1,7);
            $this->cronDaysWeek[6] = 0;
        }
        if(empty($this->cronYears)) $this->cronYears = range(Carbon::now()->year, Carbon::now()->addYear(10)->year);
        
        $countIterations = 0;
        for($year = 0; $year < count($this->cronYears); $year++)
        {
            for($month = 0; $month < count($this->cronMonths); $month++)
            {
                if(empty($this->cronDaysMonth) || count($this->cronDaysMonth) >= 28)
                {
                    $daysOfMonth = cal_days_in_month(CAL_GREGORIAN, $this->cronMonths[$month], $this->cronYears[$year]);
                    $this->cronDaysMonth = range(1, $daysOfMonth);
                }

                for($day = 0; $day < count($this->cronDaysMonth); $day++)
                {
                    $date = Carbon::createFromFormat('Y-m-d', $this->cronYears[$year] .'-'. $this->cronMonths[$month] .'-'. $this->cronDaysMonth[$day]);
                    
                    for($dayweek = 0; $dayweek < count($this->cronDaysWeek); $dayweek++)
                    {
                        
                        if($date->isDayOfWeek((int)$this->cronDaysWeek[$dayweek]))
                        {
                            if(empty($this->iterations))
                            {

                                if($date->betweenIncluded(Carbon::createFromFormat('Y-m-d', $this->rangeFrom), Carbon::createFromFormat('Y-m-d', $this->rangeAt)))
                                {
                                    if(empty($this->groupId)) $this->groupId = null;
                                    $task = Task::create([
                                        'user_id' => Auth::user()->id,
                                        'group_id' => $this->groupId,
                                        'name' => $this->name,
                                        'event' => $date,
                                        'range_from' => $this->rangeFrom,
                                        'range_at' => $this->rangeAt,
                                        'iterations' => null,
                                        'status' => 'pending'
                                    ]);
                                    $task->generateCode();
                                }
    
                            } else {
    
                                if($countIterations < $this->iterations && $date->greaterThanOrEqualTo(Carbon::now()->startOfDay()))
                                {
                                    if(empty($this->groupId)) $this->groupId = null;
                                    $task = Task::create([
                                        'user_id' => Auth::user()->id,
                                        'group_id' => $this->groupId,
                                        'name' => $this->name,
                                        'event' => $date,
                                        'range_from' => null,
                                        'range_at' => null,
                                        'iterations' => $this->iterations,
                                        'status' => 'pending'
                                    ]);
                                    $task->generateCode();
                                    $countIterations++;
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->newTaskSection = 0;
        $this->cleanNewTask();
        $this->getAllTasks();

    }

    public function completeTask($taskId)
    {
        Task::where('id', $taskId)->update(['status' => 'completed']);
        $this->getAllTasks();
    }

    public function cleanNewTask()
    {
        $this->cronDaysMonth = [];
        $this->cronMonths = [];
        $this->cronDaysWeek = [];
        $this->cronYears = [];
        $this->groupId = null;
        $this->name = '';
        $this->rangeFrom = '';
        $this->rangeAt = '';
        $this->iterations = '';
    }
}
