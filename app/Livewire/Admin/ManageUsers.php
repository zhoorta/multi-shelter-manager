<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Shelter;
use App\Models\User;
use App\Notifications\UserInvitation;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Manage Users')]
class ManageUsers extends Component
{
    public string $filterShelterId = '';

    public ?int $editingUserId = null;

    public string $userName = '';

    public string $userEmail = '';

    public string $userRole = 'staff';

    public ?int $userShelterId = null;

    public function mount(): void
    {
        abort_unless(Auth::user()->role === 'admin', 403);
    }

    /**
     * @return Collection<int, User>
     */
    #[Computed]
    public function users(): Collection
    {
        return User::query()
            ->with('shelter')
            ->when(
                $this->filterShelterId !== '',
                fn ($query) => $query->where('shelter_id', (int) $this->filterShelterId),
            )
            ->orderBy('name')
            ->get();
    }

    /**
     * @return Collection<int, Shelter>
     */
    #[Computed]
    public function shelters(): Collection
    {
        return Shelter::query()->orderBy('name')->get();
    }

    public function updatedFilterShelterId(): void
    {
        unset($this->users);
    }

    public function createUser(): void
    {
        $this->resetUserForm();
    }

    public function editUser(int $userId): void
    {
        $user = User::query()->findOrFail($userId);

        $this->editingUserId = $user->id;
        $this->userName = $user->name;
        $this->userEmail = $user->email;
        $this->userRole = $user->role;
        $this->userShelterId = $user->shelter_id;
    }

    public function saveUser(): void
    {
        if ($this->userRole === 'admin') {
            $this->userShelterId = null;
        }

        $validated = $this->validate([
            'userName' => ['required', 'string', 'max:255'],
            'userEmail' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->editingUserId),
            ],
            'userRole' => ['required', Rule::in(['staff', 'manager', 'admin'])],
            'userShelterId' => [
                Rule::requiredIf(fn () => $this->userRole !== 'admin'),
                Rule::prohibitedIf(fn () => $this->userRole === 'admin'),
                'nullable',
                'integer',
                'exists:shelters,id',
            ],
        ], [], [
            'userName' => __('Name'),
            'userEmail' => __('Email'),
            'userRole' => __('Role'),
            'userShelterId' => __('Shelter'),
        ]);

        $shelterId = $validated['userRole'] === 'admin' ? null : (int) $validated['userShelterId'];

        if ($this->editingUserId !== null) {
            User::query()->findOrFail($this->editingUserId)->update([
                'name' => $validated['userName'],
                'role' => $validated['userRole'],
                'shelter_id' => $shelterId,
            ]);

            Flux::toast(variant: 'success', text: __('Record updated successfully'));
        } else {
            $user = User::query()->create([
                'name' => $validated['userName'],
                'email' => $validated['userEmail'],
                'role' => $validated['userRole'],
                'shelter_id' => $shelterId,
                'password' => Hash::make(Str::random(40)),
            ]);

            $token = Password::broker()->createToken($user);
            $user->notify(new UserInvitation($token));

            Flux::toast(variant: 'success', text: __('User invited successfully'));
        }

        $this->resetUserForm();
        unset($this->users);

        Flux::modal('user-form')->close();
    }

    public function deleteUser(int $userId): void
    {
        if ($userId === Auth::id()) {
            Flux::toast(variant: 'danger', text: __('You cannot delete your own account'));

            return;
        }

        User::query()->findOrFail($userId)->delete();

        if ($this->editingUserId === $userId) {
            $this->resetUserForm();
        }

        unset($this->users);

        Flux::toast(variant: 'success', text: __('Record deleted successfully'));
    }

    protected function resetUserForm(): void
    {
        $this->reset(['editingUserId', 'userName', 'userEmail', 'userShelterId']);
        $this->userRole = 'staff';
        $this->resetErrorBag();
    }

    public function render(): View
    {
        return view('livewire.admin.manage-users');
    }
}
