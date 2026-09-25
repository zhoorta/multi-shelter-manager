<?php

use App\Livewire\Members\ManageMembers;
use App\Models\Member;
use App\Models\MemberPayment;
use App\Models\Shelter;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('members.index'))->assertRedirect(route('login'));
});

test('admins and viewers are forbidden from viewing the page', function (Closure $makeUser) {
    $this->actingAs($makeUser(Shelter::factory()->create()));

    $this->get(route('members.index'))->assertForbidden();
})->with([
    'admin' => fn (Shelter $shelter) => User::factory()->admin()->create(),
    'viewer' => fn (Shelter $shelter) => User::factory()->forShelter($shelter, 'viewer')->create(),
]);

test('managers and staff can view the page', function (string $role) {
    $this->actingAs(User::factory()->forShelter(Shelter::factory()->create(), $role)->create());

    $this->get(route('members.index'))->assertOk()->assertSee(__('No members registered'));
})->with(['manager', 'staff']);

test('lists only active members of the acting user\'s shelter by default', function () {
    $shelter = Shelter::factory()->create();
    Member::factory()->for($shelter)->create(['name' => 'Maria Silva']);
    Member::factory()->for($shelter)->create(['name' => 'Former Member', 'status' => 'left']);
    Member::factory()->create(['name' => 'Other Shelter Member']);

    $this->actingAs(User::factory()->forShelter($shelter)->create());

    Livewire::test(ManageMembers::class)
        ->assertSee('Maria Silva')
        ->assertDontSee('Former Member')
        ->assertDontSee('Other Shelter Member')
        ->set('statusFilter', '')
        ->assertSee('Former Member')
        ->assertDontSee('Other Shelter Member');
});

test('searches members by number or name', function () {
    $shelter = Shelter::factory()->create();
    $contacts = ['phone' => '912000000', 'email' => 'member@example.com', 'tin' => '100000000'];
    Member::factory()->for($shelter)->create(['name' => 'Maria Silva', 'member_number' => 7, ...$contacts]);
    Member::factory()->for($shelter)->create(['name' => 'João Costa', 'member_number' => 8, ...$contacts]);

    $this->actingAs(User::factory()->forShelter($shelter)->create());

    Livewire::test(ManageMembers::class)
        ->set('search', '7')
        ->assertSee('Maria Silva')
        ->assertDontSee('João Costa')
        ->set('search', 'Costa')
        ->assertSee('João Costa')
        ->assertDontSee('Maria Silva');
});

test('flags and filters the members with fees overdue', function () {
    $this->travelTo('2026-09-25');
    $shelter = Shelter::factory()->create();
    $upToDate = Member::factory()->for($shelter)->create(['name' => 'Up To Date Member']);
    MemberPayment::factory()->for($upToDate)->create(['start_date' => '2026-01-01', 'end_date' => '2026-12-31']);
    Member::factory()->for($shelter)->create(['name' => 'Overdue Member']);

    $this->actingAs(User::factory()->forShelter($shelter)->create());

    Livewire::test(ManageMembers::class)
        ->assertSee('Up To Date Member')
        ->assertSee('31/12/2026')
        ->assertSeeInOrder(['Overdue Member', __('Fees overdue')])
        ->set('inArrearsOnly', true)
        ->assertSee('Overdue Member')
        ->assertDontSee('Up To Date Member');
});

test('managers soft-delete members of their shelter only', function () {
    $shelter = Shelter::factory()->create();
    $member = Member::factory()->for($shelter)->create();
    $otherShelterMember = Member::factory()->create();

    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    Livewire::test(ManageMembers::class)->call('deleteMember', $member->id);

    expect($member->fresh()->trashed())->toBeTrue()
        ->and(fn () => Livewire::test(ManageMembers::class)->call('deleteMember', $otherShelterMember->id))
        ->toThrow(ModelNotFoundException::class);
});

test('staff cannot delete members or change the default fees', function (string $action) {
    $shelter = Shelter::factory()->create();
    $member = Member::factory()->for($shelter)->create();

    $this->actingAs(User::factory()->forShelter($shelter, 'staff')->create());

    Livewire::test(ManageMembers::class)
        ->call($action, ...($action === 'deleteMember' ? [$member->id] : []))
        ->assertForbidden();

    expect($member->fresh()->trashed())->toBeFalse();
})->with(['deleteMember', 'editFeeDefaults', 'saveFeeDefaults']);

test('managers set their shelter\'s default fees', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();

    $this->actingAs(User::factory()->forShelter($shelter, 'manager')->create());

    Livewire::test(ManageMembers::class)
        ->call('editFeeDefaults')
        ->assertSet('defaultJoiningFee', '0.00')
        ->set('defaultJoiningFee', '5')
        ->set('defaultMembershipFee', '2.5')
        ->set('defaultMembershipFeeFrequency', 'monthly')
        ->call('saveFeeDefaults')
        ->assertHasNoErrors();

    expect($shelter->fresh())
        ->joining_fee->toBe('5.00')
        ->membership_fee->toBe('2.50')
        ->membership_fee_frequency->toBe('monthly')
        ->and($otherShelter->fresh()->joining_fee)->toBe('0.00');
});

test('validates the default fees', function () {
    $this->actingAs(User::factory()->forShelter(Shelter::factory()->create(), 'manager')->create());

    Livewire::test(ManageMembers::class)
        ->set('defaultJoiningFee', '-1')
        ->set('defaultMembershipFee', '')
        ->set('defaultMembershipFeeFrequency', 'weekly')
        ->call('saveFeeDefaults')
        ->assertHasErrors([
            'defaultJoiningFee' => 'min',
            'defaultMembershipFee' => 'required',
            'defaultMembershipFeeFrequency' => 'in',
        ]);
});
