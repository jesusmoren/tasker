<div>
    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
    
        <h1 class="text-2xl font-medium text-gray-900">
            Users
        </h1>
        
        @if(Auth::user()->current_team_id === 100)

        <table class="table-fixed w-full text-sm text-left text-slate-900 content-center">
            <thead>
                <tr>
                    <th class="py-4 px-6">Name</th>
                    <th class="py-4 px-6">Email</th>
                    <th class="py-4 px-6">Verified</th>
                    <th class="py-4 px-6">Verified Date</th>
                    <th class="py-4 px-6">Options</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="bg-white border-b">
                    <td class="py-4 px-6">{{ $user->name }}</td>
                    <td class="py-4 px-6">{{ $user->email }}</td>
                    <td class="py-4 px-6">{{ ($user->current_team_id != null) ? 'Yes':'No' }}</td>
                    <td class="py-4 px-6">{{ $user->email_verified_at }}</td>
                    <td class="py-4 px-6">
                        @if($user->current_team_id !== 1 && $user->email_verified_at == null)
                        <x-button class="button-sm bg-emerald-400" wire:click="verify({{ $user->id }})">
                            Verify
                        </x-button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @else
        <p class="text-sm mt-2 text-gray-600">You are not verified yet.</p>
        @endif

    </div>

</div>
