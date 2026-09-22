<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Shelter;
use App\Models\User;
use App\Notifications\ShelterMembershipAdded;
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
use Livewire\Component;

class UserForm extends Component
{
    public ?User $user = null;

    public ?int $existingUserId = null;

    public string $userName = '';

    public string $userEmail = '';

    public bool $userIsAdmin = false;

    /**
     * One row per shelter membership being created/edited.
     *
     * @var array<int, array{shelter_id: int|null, role: string, vaccination_notifications: bool}>
     */
    public array $userMemberships = [];

    public function mount(?User $user = null): void
    {
        $viewer = Auth::user();

        abort_unless($viewer->is_admin || $viewer->isManagerOfCurrentShelter(), 403);

        if ($user === null) {
            $this->addMembership();

            return;
        }

        $user = User::query()
            ->when(
                ! $viewer->is_admin,
                fn ($query) => $query->whereHas('shelters', fn ($q) => $q->whereIn('shelters.id', $viewer->managedShelterIds())),
            )
            ->findOrFail($user->id);

        $this->user = $user;
        $this->userName = $user->name;
        $this->userEmail = $user->email;
        $this->userIsAdmin = $user->is_admin;

        $memberships = $user->shelters()
            ->when(
                ! $viewer->is_admin,
                fn ($query) => $query->whereIn('shelters.id', $viewer->managedShelterIds()),
            )
            ->get();

        $this->userMemberships = $memberships->map(fn ($shelter) => [
            'shelter_id' => $shelter->id,
            'role' => $shelter->pivot->role,
            'vaccination_notifications' => (bool) $shelter->pivot->vaccination_notifications,
        ])->all();
    }

    /**
     * @return Collection<int, Shelter>
     */
    #[Computed]
    public function shelters(): Collection
    {
        return Shelter::query()->orderBy('name')->get();
    }

    /**
     * A manager editing their own account may only toggle vaccination
     * notifications — never their name or shelter memberships.
     */
    #[Computed]
    public function isManagerEditingOwnAccount(): bool
    {
        return $this->user !== null && $this->user->id === Auth::id() && ! Auth::user()->is_admin;
    }

    /**
     * The shelters the acting manager manages, offered as membership options.
     *
     * @return Collection<int, Shelter>
     */
    #[Computed]
    public function managedShelters(): Collection
    {
        return Auth::user()->shelters()->wherePivot('role', 'manager')->orderBy('shelters.name')->get();
    }

    public function addMembership(): void
    {
        if ($this->isManagerEditingOwnAccount) {
            return;
        }

        $viewer = Auth::user();

        $this->userMemberships[] = [
            'shelter_id' => $viewer->is_admin ? null : $viewer->current_shelter_id,
            'role' => 'staff',
            'vaccination_notifications' => false,
        ];
    }

    public function removeMembership(int $index): void
    {
        if ($this->isManagerEditingOwnAccount) {
            return;
        }

        unset($this->userMemberships[$index]);
        $this->userMemberships = array_values($this->userMemberships);
    }

    public function updatedUserEmail(string $value): void
    {
        if ($this->user !== null) {
            return;
        }

        $this->existingUserId = User::query()->where('email', $value)->value('id');
    }

    public function saveUser(): void
    {
        $viewer = Auth::user();

        if (! $viewer->is_admin) {
            $this->userIsAdmin = false;
        }

        if ($this->userIsAdmin) {
            $this->userMemberships = [];
        }

        $validated = $this->validate([
            'userName' => [$this->existingUserId ? 'nullable' : 'required', 'string', 'max:255'],
            'userEmail' => ['required', 'string', 'email', 'max:255'],
            'userIsAdmin' => ['boolean'],
            'userMemberships' => $viewer->is_admin && $this->userIsAdmin
                ? ['prohibited']
                : ['required', 'array', 'min:1'],
            'userMemberships.*.shelter_id' => [
                'required',
                'integer',
                'distinct',
                $viewer->is_admin ? 'exists:shelters,id' : Rule::in($viewer->managedShelterIds()),
            ],
            'userMemberships.*.role' => ['required', Rule::in(['staff', 'manager'])],
            'userMemberships.*.vaccination_notifications' => ['boolean'],
        ], [
            'userMemberships.*.shelter_id.distinct' => __('This shelter has already been selected.'),
        ], [
            'userName' => __('Name'),
            'userEmail' => __('Email'),
            'userMemberships' => __('Shelters'),
            'userMemberships.*.shelter_id' => __('Shelter'),
            'userMemberships.*.role' => __('Role'),
        ]);

        $success = match (true) {
            $this->user === null && $this->existingUserId !== null => $this->addExistingUserToShelters($validated),
            $this->user !== null => $this->updateExistingUser($validated),
            default => $this->createNewUser($validated),
        };

        if (! $success) {
            return;
        }

        $this->redirect(route('admin.users.index'), navigate: true);
    }

    /**
     * @param  array{userMemberships?: array<int, array{shelter_id: int, role: string, vaccination_notifications: bool}>}  $validated
     */
    private function addExistingUserToShelters(array $validated): bool
    {
        $user = User::query()->findOrFail($this->existingUserId);

        foreach ($validated['userMemberships'] as $membership) {
            if ($user->belongsToShelter($membership['shelter_id'])) {
                $this->addError('userMemberships', __('This user already belongs to that shelter.'));

                return false;
            }
        }

        foreach ($validated['userMemberships'] as $membership) {
            $user->shelters()->attach($membership['shelter_id'], [
                'role' => $membership['role'],
                'vaccination_notifications' => $membership['vaccination_notifications'],
            ]);

            $user->notify(new ShelterMembershipAdded(Shelter::query()->findOrFail($membership['shelter_id']), $membership['role']));
        }

        if ($user->current_shelter_id === null) {
            $user->update(['current_shelter_id' => $validated['userMemberships'][0]['shelter_id']]);
        }

        Flux::toast(variant: 'success', text: __('Existing user added to the shelter successfully'));

        return true;
    }

    /**
     * @param  array{userName: string, userIsAdmin: bool, userMemberships?: array<int, array{shelter_id: int, role: string, vaccination_notifications: bool}>}  $validated
     */
    private function updateExistingUser(array $validated): bool
    {
        $viewer = Auth::user();
        $user = $this->user;

        if ($this->isManagerEditingOwnAccount) {
            return $this->updateOwnNotificationPreferences($validated);
        }

        $user->update(['name' => $validated['userName']]);

        if ($viewer->is_admin) {
            $user->update(['is_admin' => $validated['userIsAdmin']]);
        }

        $authorizedShelterIds = $viewer->is_admin ? null : $viewer->managedShelterIds();

        $shelterIdsToDetach = $user->shelters()
            ->when($authorizedShelterIds !== null, fn ($query) => $query->whereIn('shelters.id', $authorizedShelterIds))
            ->pluck('shelters.id');

        $user->shelters()->detach($shelterIdsToDetach);

        foreach ($validated['userMemberships'] ?? [] as $membership) {
            $user->shelters()->attach($membership['shelter_id'], [
                'role' => $membership['role'],
                'vaccination_notifications' => $membership['vaccination_notifications'],
            ]);
        }

        if ($user->current_shelter_id !== null && ! $user->belongsToShelter($user->current_shelter_id)) {
            $user->update(['current_shelter_id' => $user->shelters()->value('shelters.id')]);
        }

        Flux::toast(variant: 'success', text: __('Record updated successfully'));

        return true;
    }

    /**
     * Only the vaccination_notifications flag of memberships the manager
     * already has is applied; name, shelters and roles are left untouched.
     *
     * @param  array{userMemberships?: array<int, array{shelter_id: int, role: string, vaccination_notifications: bool}>}  $validated
     */
    private function updateOwnNotificationPreferences(array $validated): bool
    {
        foreach ($validated['userMemberships'] ?? [] as $membership) {
            if ($this->user->belongsToShelter($membership['shelter_id'])) {
                $this->user->shelters()->updateExistingPivot($membership['shelter_id'], [
                    'vaccination_notifications' => $membership['vaccination_notifications'],
                ]);
            }
        }

        Flux::toast(variant: 'success', text: __('Record updated successfully'));

        return true;
    }

    /**
     * @param  array{userName: string, userEmail: string, userIsAdmin: bool, userMemberships?: array<int, array{shelter_id: int, role: string, vaccination_notifications: bool}>}  $validated
     */
    private function createNewUser(array $validated): bool
    {
        $viewer = Auth::user();
        $isAdmin = $viewer->is_admin && $validated['userIsAdmin'];

        $user = User::query()->create([
            'name' => $validated['userName'],
            'email' => $validated['userEmail'],
            'is_admin' => $isAdmin,
            'current_shelter_id' => $isAdmin ? null : $validated['userMemberships'][0]['shelter_id'],
            'password' => Hash::make(Str::random(40)),
        ]);

        if (! $isAdmin) {
            foreach ($validated['userMemberships'] as $membership) {
                $user->shelters()->attach($membership['shelter_id'], [
                    'role' => $membership['role'],
                    'vaccination_notifications' => $membership['vaccination_notifications'],
                ]);
            }
        }

        $user->notify(new UserInvitation(Password::broker()->createToken($user)));

        Flux::toast(variant: 'success', text: __('User invited successfully'));

        return true;
    }

    public function render(): View
    {
        return view('livewire.admin.user-form')->title(
            $this->user !== null ? __('Edit User').' — '.$this->user->name : __('Invite User'),
        );
    }
}
