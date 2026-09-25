<?php

declare(strict_types=1);

namespace App\Livewire\Members;

use App\Models\Member;
use App\Models\Shelter;
use App\Models\Volunteer;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

class MemberForm extends Component
{
    public ?Member $member = null;

    public string $memberNumber = '';

    public string $memberName = '';

    public string $memberTin = '';

    public string $memberEmail = '';

    public string $memberPhone = '';

    public string $memberAddress = '';

    public string $memberPostalCode = '';

    public string $memberCity = '';

    public string $memberJoinDate = '';

    public string $memberStatus = 'active';

    public string $memberJoiningFee = '0.00';

    public string $memberMembershipFee = '0.00';

    public string $memberMembershipFeeFrequency = 'yearly';

    public string $memberVolunteerId = '';

    public string $memberNotes = '';

    public function mount(?Member $member = null): void
    {
        abort_unless(Auth::user()->canEditCurrentShelter(), 403);

        if ($member === null) {
            // New members start with the shelter's default fees.
            $shelter = Shelter::query()->findOrFail(Auth::user()->current_shelter_id);
            $this->memberJoiningFee = (string) $shelter->joining_fee;
            $this->memberMembershipFee = (string) $shelter->membership_fee;
            $this->memberMembershipFeeFrequency = $shelter->membership_fee_frequency;
            $this->memberJoinDate = now()->format('Y-m-d');

            return;
        }

        $this->member = $member;
        $this->memberNumber = (string) $member->member_number;
        $this->memberName = $member->name;
        $this->memberTin = (string) $member->tin;
        $this->memberEmail = (string) $member->email;
        $this->memberPhone = (string) $member->phone;
        $this->memberAddress = (string) $member->address;
        $this->memberPostalCode = (string) $member->postal_code;
        $this->memberCity = (string) $member->city;
        $this->memberJoinDate = $member->join_date->format('Y-m-d');
        $this->memberStatus = $member->status;
        $this->memberJoiningFee = (string) $member->joining_fee;
        $this->memberMembershipFee = (string) $member->membership_fee;
        $this->memberMembershipFeeFrequency = $member->membership_fee_frequency;
        $this->memberVolunteerId = (string) $member->volunteer_id;
        $this->memberNotes = (string) $member->notes;
    }

    /**
     * @return Collection<int, Volunteer>
     */
    #[Computed]
    public function volunteers(): Collection
    {
        return Volunteer::query()->orderBy('name')->get(['id', 'name']);
    }

    public function saveMember(): void
    {
        $shelterId = Auth::user()->current_shelter_id;

        $validated = $this->validate([
            'memberNumber' => [
                'nullable', 'integer', 'min:1',
                Rule::unique('members', 'member_number')->where('shelter_id', $shelterId)->ignore($this->member?->id),
            ],
            'memberName' => ['required', 'string', 'max:255'],
            'memberTin' => ['nullable', 'string', 'max:255'],
            'memberEmail' => ['nullable', 'string', 'email', 'max:150'],
            'memberPhone' => ['nullable', 'string', 'max:30'],
            'memberAddress' => ['nullable', 'string', 'max:255'],
            'memberPostalCode' => ['nullable', 'string', 'max:20'],
            'memberCity' => ['nullable', 'string', 'max:100'],
            'memberJoinDate' => ['required', 'date'],
            'memberStatus' => ['required', 'in:active,suspended,left'],
            'memberJoiningFee' => ['required', 'numeric', 'min:0', 'max:999999'],
            'memberMembershipFee' => ['required', 'numeric', 'min:0', 'max:999999'],
            'memberMembershipFeeFrequency' => ['required', 'in:monthly,quarterly,semiannual,yearly'],
            'memberVolunteerId' => [
                'nullable', 'integer',
                Rule::exists('volunteers', 'id')->where('shelter_id', $shelterId)->withoutTrashed(),
            ],
            'memberNotes' => ['nullable', 'string'],
        ], [], [
            'memberNumber' => __('Member Number'),
            'memberName' => __('Name'),
            'memberTin' => __('TIN'),
            'memberEmail' => __('Email'),
            'memberPhone' => __('Phone'),
            'memberAddress' => __('Address'),
            'memberPostalCode' => __('Postal Code'),
            'memberCity' => __('City'),
            'memberJoinDate' => __('Join Date'),
            'memberStatus' => __('Status'),
            'memberJoiningFee' => __('Joining Fee'),
            'memberMembershipFee' => __('Membership Fee'),
            'memberMembershipFeeFrequency' => __('Frequency'),
            'memberVolunteerId' => __('Volunteer'),
            'memberNotes' => __('Notes'),
        ]);

        $attributes = [
            'name' => $validated['memberName'],
            'tin' => $validated['memberTin'] !== '' ? $validated['memberTin'] : null,
            'email' => $validated['memberEmail'] !== '' ? $validated['memberEmail'] : null,
            'phone' => $validated['memberPhone'] !== '' ? $validated['memberPhone'] : null,
            'address' => $validated['memberAddress'] !== '' ? $validated['memberAddress'] : null,
            'postal_code' => $validated['memberPostalCode'] !== '' ? $validated['memberPostalCode'] : null,
            'city' => $validated['memberCity'] !== '' ? $validated['memberCity'] : null,
            'join_date' => $validated['memberJoinDate'],
            'status' => $validated['memberStatus'],
            'joining_fee' => $validated['memberJoiningFee'],
            'membership_fee' => $validated['memberMembershipFee'],
            'membership_fee_frequency' => $validated['memberMembershipFeeFrequency'],
            'volunteer_id' => $validated['memberVolunteerId'] !== '' ? (int) $validated['memberVolunteerId'] : null,
            'notes' => $validated['memberNotes'] !== '' ? $validated['memberNotes'] : null,
        ];

        // Left empty, a new member gets the next number (see Member::booted()).
        if ($validated['memberNumber'] !== '') {
            $attributes['member_number'] = (int) $validated['memberNumber'];
        }

        $isEditing = $this->member !== null;

        if ($isEditing) {
            $this->member->update($attributes);
        } else {
            $this->member = Member::query()->create($attributes);
        }

        Flux::toast(
            variant: 'success',
            text: $isEditing ? __('Record updated successfully') : __('Record created successfully'),
        );

        $this->redirect(route('members.show', $this->member), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.members.member-form')->title(
            $this->member !== null ? __('Edit').' — '.$this->member->name : __('Create').' — '.__('Members'),
        );
    }
}
