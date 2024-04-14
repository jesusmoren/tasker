<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;

class UsersManagement extends Component
{
    public $users;

    public function render()
    {
        return view('livewire.users-management');
    }

    public function mount()
    {
        $this->getUsers();
    }

    public function getUsers()
    {
        $this->users = User::get();
    }

    public function verify($userId)
    {
        User::where('id', $userId)->update(['email_verified_at' => Carbon::now()->toDateTimeString(), 'current_team_id' => 1]);
        $this->getUsers();
    }
}
