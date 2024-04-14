<div>
    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
    
        <h1 class="text-2xl font-medium text-gray-900">
            List of Tasks
        </h1>

        @if(Auth::user()->current_team_id === 100)

        <x-button type="button" class="mt-8 bg-red-600" wire:click="toggleNewTask">New Task</x-button>

        <div class="new-task px-6">
        @if($newTaskSection == 1)
        
            <p class="font-medium text-gray-500 mt-4">Select the Task Group and Name:</p>
            <div class="mt-2 inline-grid grid-cols-2">
                <div class="mr-4">
                    <x-label>Task Group</x-label>
                    <select name="" id="" wire:model="groupId" class="border-gray-300 focus:border-rose-500 focus:ring-rose-500 rounded-md shadow-sm w-full">
                        @foreach($tasksGroups as $id => $group)
                            <option value="{{ $id }}">{{ $group }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-label>Name</x-label>
                    <x-input class="w-96" type="text" wire:model="name"></x-input>
                    <x-input-error for="name"></x-input-error>
                </div>
            </div>
            <br>

            <p class="font-medium text-gray-500 mt-4">Select the recurrency of the task, empty selection is equal to all the values:</p>
            <div class="inline-flex mt-2">
                @foreach($months as $id => $month)
                <x-checkbox wire:model.live="cronMonths" value="{{ $id }}"></x-checkbox>
                <x-label class="px-1 mr-2">{{ $month }}</x-label>
                @endforeach
            </div>
    
            <div class="inline-flex mt-2">
                @foreach($daysweek as $id => $dayweek)
                <x-checkbox wire:model.live="cronDaysWeek" value="{{ $id }}"></x-checkbox>
                <x-label class="px-1 mr-2">{{ $dayweek }}</x-label>
                @endforeach
            </div>
    
            <div class="inline-flex mt-2 justify-center">
                @foreach($daysmonth as $id => $daymonth)
                <div class="px-1">
                    <x-checkbox class="px-1" wire:model.live="cronDaysMonth" value="{{ $daymonth }}"></x-checkbox>
                    <x-label class="mr-2 ">{{ str_pad($daymonth, 2, '0', STR_PAD_LEFT) }}</x-label>
                </div>
                @endforeach
            </div>

            <br>

            <label class="relative inline-flex cursor-pointer items-center mt-4">
                <input type="checkbox" wire:model.live="periodicity" class="peer sr-only" />
                <div class="peer flex h-8 items-center gap-5 rounded-full bg-gray-400 px-3 after:absolute after:left-1 after: after:h-6 after:w-16 after:rounded-full after:bg-white/40 after:transition-all after:content-[''] peer-checked:bg-stone-600 peer-checked:after:translate-x-full peer-focus:outline-none dark:border-slate-600 dark:bg-slate-700 text-sm text-white">
                    <span>Range</span>
                    <span>Iteration</span>
                </div>
            </label>
            <br>
            <div class="mt-2 inline-grid grid-cols-3 gap-3">
                @if($periodicity == false)
                <div class="range">
                    <x-label>Range from</x-label>
                    <x-input type="date" min="{{ $today }}" wire:model="rangeFrom"></x-input>
                </div>
                <div class="range">
                    <x-label>Range from</x-label>
                    <x-input type="date" wire:model="rangeAt"></x-input>
                </div>
                @else
                <div class="iteration">
                    <x-label>Iterations</x-label>
                    <x-input class="w-16" type="number" wire:model="iterations"></x-input>
                </div>
                @endif
            </div>
            <br>
            <x-input-error for="rangeFrom"></x-input-error>
            <x-input-error for="rangeAtange"></x-input-error>
            <x-input-error for="iterations"></x-input-error>
            <br>
            <x-button type="button" class="mt-4 bg-slate-600" wire:click="storeNewTask">Create Task</x-button>
        @endif
        </div>

        {{-- <div class="tasks-spinner w-full flex justify-center items-center mt-16" wire:loading.delay.long>
            <img class="w-16 h-16" src="https://cdn.pixabay.com/animation/2023/08/11/21/18/21-18-05-265_512.gif" alt="">
        </div> --}}
        <div class="tasks-list">
            <table class="w-full mt-4 text-sm text-left text-slate-900 table-fixed">
                <thead>
                    <tr>
                        <th class="py-4 px-6">ID</th>
                        <th class="py-4 px-6">Name</th>
                        <th class="py-4 px-6">Date</th>
                        <th class="py-4 px-6">Group</th>
                        <th class="py-4 px-6">Options</th>
                    </tr>
                </thead>
            </table>
    
            <p class="font-bold mt-2">Today</p>
            <table class="w-full text-sm text-left text-slate-900 table-fixed">
                <tbody>
                    @forelse($tasksToday as $taskToday)
                    <tr class="bg-white border-b">
                        <td class="py-4 px-6">{{ $taskToday->code }}</td>
                        <td class="py-4 px-6">{{ $taskToday->name }}</td>
                        <td class="py-4 px-6">{{ $taskToday->event }}</td>
                        <td class="py-4 px-6">{{ $taskToday->group->name ?? '' }}</td>
                        <td class="py-4 px-6">
                            <x-button class="button-sm bg-emerald-400" wire:click="completeTask({{ $taskToday->id }})">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                  </svg>
                            </x-button>
                        </td>
                    </tr>
                    @empty
                    <p class="mt-2 px-4 font-sm text-gray-600">No tasks today.</p>
                    @endforelse
                </tbody>
            </table>
    
            <p class="font-bold mt-6">Tomorrow</p>
            <table class="w-full text-sm text-left text-slate-900 table-fixed">
                <tbody>
                    @forelse($tasksTomorrow as $taskTomorrow)
                    <tr class="bg-white border-b">
                        <td class="py-4 px-6">{{ $taskTomorrow->code }}</td>
                        <td class="py-4 px-6">{{ $taskTomorrow->name }}</td>
                        <td class="py-4 px-6">{{ $taskTomorrow->event }}</td>
                        <td class="py-4 px-6">{{ $taskTomorrow->group->name ?? '' }}</td>
                        <td class="py-4 px-6">
                            <x-button class="button-sm bg-emerald-400" wire:click="completeTask({{ $taskTomorrow->id }})">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                  </svg>
                            </x-button>
                        </td>
                    </tr>
                    @empty
                    <p class="mt-2 px-4 font-sm text-gray-600">No tasks tomorrow.</p>
                    @endforelse
                </tbody>
            </table>
    
            <p class="font-bold mt-6">This Week</p>
            <table class="w-full text-sm text-left text-slate-900 table-fixed">
                <tbody>
                    @forelse($tasksThisWeek as $taskThisWeek)
                    <tr class="bg-white border-b">
                        <td class="py-4 px-6">{{ $taskThisWeek->code }}</td>
                        <td class="py-4 px-6">{{ $taskThisWeek->name }}</td>
                        <td class="py-4 px-6">{{ $taskThisWeek->event }}</td>
                        <td class="py-4 px-6">{{ $taskThisWeek->group->name ?? '' }}</td>
                        <td class="py-4 px-6">
                            <x-button class="button-sm bg-emerald-400" wire:click="completeTask({{ $taskThisWeek->id }})">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                  </svg>
                            </x-button>
                        </td>
                    </tr>
                    @empty
                    <p class="mt-2 px-4 font-sm text-gray-600">No tasks this week.</p>
                    @endforelse
                </tbody>
            </table>
    
            <p class="font-bold mt-6">Next Week</p>
            <table class="w-full text-sm text-left text-slate-900 table-fixed">
                <tbody>
                    @forelse($tasksNextWeek as $taskNextWeek)
                    <tr class="bg-white border-b">
                        <td class="py-4 px-6">{{ $taskNextWeek->code }}</td>
                        <td class="py-4 px-6">{{ $taskNextWeek->name }}</td>
                        <td class="py-4 px-6">{{ $taskNextWeek->event }}</td>
                        <td class="py-4 px-6">{{ $taskNextWeek->group->name ?? '' }}</td>
                        <td class="py-4 px-6">
                            <x-button class="button-sm bg-emerald-400" wire:click="completeTask({{ $taskNextWeek->id }})">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                  </svg>
                            </x-button>
                        </td>
                    </tr>
                    @empty
                    <p class="mt-2 px-4 font-sm text-gray-600">No tasks next week.</p>
                    @endforelse
                </tbody>
            </table>
    
            <p class="font-bold mt-6">Next month (near future)</p>
            <table class="w-full text-sm text-left text-slate-900 table-fixed">
                <tbody>
                    @forelse($tasksNextMonth as $taskNextMonth)
                    <tr class="bg-white border-b">
                        <td class="py-4 px-6">{{ $taskNextMonth->code }}</td>
                        <td class="py-4 px-6">{{ $taskNextMonth->name }}</td>
                        <td class="py-4 px-6">{{ $taskNextMonth->event }}</td>
                        <td class="py-4 px-6">{{ $taskNextMonth->group->name ?? '' }}</td>
                        <td class="py-4 px-6">
                            <x-button class="button-sm bg-emerald-400" wire:click="completeTask({{ $taskNextMonth->id }})">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                  </svg>
                            </x-button>
                        </td>
                    </tr>
                    @empty
                    <p class="mt-2 px-4 font-sm text-gray-600">No tasks the rest of the month.</p>
                    @endforelse
                </tbody>
            </table>
    
            <p class="font-bold mt-6">Next months (future)</p>
            <table class="w-full text-sm text-left text-slate-900 table-fixed">
                <tbody>
                    @forelse($tasksNextMonths as $taskNextMonth)
                    <tr class="bg-white border-b">
                        <td class="py-4 px-6">{{ $taskNextMonth->code }}</td>
                        <td class="py-4 px-6">{{ $taskNextMonth->name }}</td>
                        <td class="py-4 px-6">{{ $taskNextMonth->event }}</td>
                        <td class="py-4 px-6">{{ $taskNextMonth->group->name ?? '' }}</td>
                        <td class="py-4 px-6">
                            <x-button class="button-sm bg-emerald-400" wire:click="completeTask({{ $taskNextMonth->id }})">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                  </svg>
                            </x-button>
                        </td>
                    </tr>
                    @empty
                    <p class="mt-2 px-4 font-sm text-gray-600">No tasks for the coming months.</p>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @else
        <p class="text-sm mt-2 text-gray-600">You are not verified yet.</p>
        @endif

    </div>

</div>
