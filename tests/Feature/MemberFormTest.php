<?php

use App\Livewire\Members\MemberForm;
use App\Models\Member;
use App\Models\Shelter;
use App\Models\User;
use App\Models\Volunteer;
use Livewire\Livewire;

test('viewers are forbidden from viewing the form', function () {
    $this->actingAs(User::factory()->forShelter(Shelter::factory()->create(), 'viewer')->create());

    $this->get(route('members.create'))->assertForbidden();
});

test('staff can view the create and edit pages', function () {
    $shelter = Shelter::factory()->create();
    $member = Member::factory()->for($shelter)->create();

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    $this->get(route('members.create'))->assertOk();
    $this->get(route('members.edit', $member))->assertOk();
});

test('cannot edit a member of another shelter', function () {
    $this->actingAs(User::factory()->forShelter(Shelter::factory()->create())->create());

    $this->get(route('members.edit', Member::factory()->create()))->assertNotFound();
});

test('a new member starts with the shelter\'s default fees and today as join date', function () {
    $this->travelTo('2026-09-25');
    $shelter = Shelter::factory()->create([
        'joining_fee' => 5,
        'membership_fee' => 2.5,
        'membership_fee_frequency' => 'monthly',
    ]);

    $this->actingAs(User::factory()->forShelter($shelter)->create());

    Livewire::test(MemberForm::class)
        ->assertSet('memberJoiningFee', '5.00')
        ->assertSet('memberMembershipFee', '2.50')
        ->assertSet('memberMembershipFeeFrequency', 'monthly')
        ->assertSet('memberJoinDate', '2026-09-25');
});

test('creates a member with the next number in the acting user\'s shelter', function () {
    $shelter = Shelter::factory()->create();
    Member::factory()->for($shelter)->create();

    $this->actingAs(User::factory()->forShelter($shelter)->create());

    Livewire::test(MemberForm::class)
        ->set('memberName', 'Maria Silva')
        ->set('memberEmail', 'maria@example.com')
        ->set('memberJoiningFee', '10')
        ->call('saveMember')
        ->assertHasNoErrors()
        ->assertRedirect(route('members.show', Member::query()->latest('id')->first()));

    expect(Member::query()->latest('id')->first())
        ->shelter_id->toBe($shelter->id)
        ->member_number->toBe(2)
        ->name->toBe('Maria Silva')
        ->joining_fee->toBe('10.00')
        ->tin->toBeNull();
});

test('a member number must be unique within the shelter only', function () {
    $shelter = Shelter::factory()->create();
    Member::factory()->for($shelter)->create(['member_number' => 12]);
    Member::factory()->create(['member_number' => 30]);

    $this->actingAs(User::factory()->forShelter($shelter)->create());

    Livewire::test(MemberForm::class)
        ->set('memberName', 'Maria Silva')
        ->set('memberNumber', '12')
        ->call('saveMember')
        ->assertHasErrors(['memberNumber' => 'unique'])
        ->set('memberNumber', '30')
        ->call('saveMember')
        ->assertHasNoErrors();

    expect(Member::query()->where('name', 'Maria Silva')->value('member_number'))->toBe(30);
});

test('only links a volunteer of the acting user\'s shelter', function () {
    $shelter = Shelter::factory()->create();
    $volunteer = Volunteer::factory()->for($shelter)->create();

    $this->actingAs(User::factory()->forShelter($shelter)->create());

    Livewire::test(MemberForm::class)
        ->set('memberName', 'Maria Silva')
        ->set('memberVolunteerId', (string) Volunteer::factory()->create()->id)
        ->call('saveMember')
        ->assertHasErrors(['memberVolunteerId' => 'exists'])
        ->set('memberVolunteerId', (string) $volunteer->id)
        ->call('saveMember')
        ->assertHasNoErrors();

    expect($volunteer->member->name)->toBe('Maria Silva');
});

test('validates the member fields', function () {
    $this->actingAs(User::factory()->forShelter(Shelter::factory()->create())->create());

    Livewire::test(MemberForm::class)
        ->set('memberName', '')
        ->set('memberEmail', 'not-an-email')
        ->set('memberJoinDate', '')
        ->set('memberStatus', 'expelled')
        ->set('memberMembershipFee', '-5')
        ->call('saveMember')
        ->assertHasErrors([
            'memberName' => 'required',
            'memberEmail' => 'email',
            'memberJoinDate' => 'required',
            'memberStatus' => 'in',
            'memberMembershipFee' => 'min',
        ]);
});

test('populates and updates an existing member', function () {
    $shelter = Shelter::factory()->create();
    $member = Member::factory()->for($shelter)->create(['name' => 'Maria Silva', 'member_number' => 4]);

    $this->actingAs(User::factory()->forShelter($shelter)->create());

    Livewire::test(MemberForm::class, ['member' => $member])
        ->assertSet('memberName', 'Maria Silva')
        ->assertSet('memberNumber', '4')
        ->set('memberStatus', 'suspended')
        ->call('saveMember')
        ->assertHasNoErrors()
        ->assertRedirect(route('members.show', $member));

    expect($member->fresh())
        ->status->toBe('suspended')
        ->member_number->toBe(4);
});
