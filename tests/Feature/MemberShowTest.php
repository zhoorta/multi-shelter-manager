<?php

use App\Livewire\Members\MemberShow;
use App\Models\Member;
use App\Models\MemberPayment;
use App\Models\Shelter;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('viewers are forbidden from viewing a member', function () {
    $shelter = Shelter::factory()->create();
    $member = Member::factory()->for($shelter)->create();

    $this->actingAs(User::factory()->forShelter($shelter, 'viewer')->create());

    $this->get(route('members.show', $member))->assertForbidden();
});

test('cannot view a member of another shelter', function () {
    $this->actingAs(User::factory()->forShelter(Shelter::factory()->create())->create());

    $this->get(route('members.show', Member::factory()->create()))->assertNotFound();
});

test('shows the member details and payments', function () {
    $shelter = Shelter::factory()->create();
    $member = Member::factory()->for($shelter)->create(['name' => 'Maria Silva', 'member_number' => 42]);
    MemberPayment::factory()->for($member)->create([
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
        'payment_value' => 12,
        'payment_method' => 'mobile',
    ]);

    $this->actingAs(User::factory()->forShelter($shelter)->create());

    $this->get(route('members.show', $member))
        ->assertOk()
        ->assertSee('Maria Silva')
        ->assertSee('42')
        ->assertSee('01/01/2026 – 31/12/2026')
        ->assertSee('12,00 €')
        ->assertSee(__('payment_mobile'));
});

test('offers the joining fee payment only while it is owed', function () {
    $shelter = Shelter::factory()->create();
    $member = Member::factory()->for($shelter)->create(['joining_fee' => 10]);

    $this->actingAs(User::factory()->forShelter($shelter)->create());

    Livewire::test(MemberShow::class, ['member' => $member])
        ->assertSeeHtml("createPayment('joining_fee')");

    MemberPayment::factory()->for($member)->joiningFee()->create();

    Livewire::test(MemberShow::class, ['member' => $member])
        ->assertDontSeeHtml("createPayment('joining_fee')");
});

test('pre-fills a membership fee payment with the next period and the member\'s fee', function () {
    $this->travelTo('2026-09-25');
    $shelter = Shelter::factory()->create();
    $member = Member::factory()->for($shelter)->create(['membership_fee' => 3, 'membership_fee_frequency' => 'monthly']);
    MemberPayment::factory()->for($member)->create(['start_date' => '2026-08-01', 'end_date' => '2026-08-31']);

    $this->actingAs(User::factory()->forShelter($shelter)->create());

    Livewire::test(MemberShow::class, ['member' => $member])
        ->call('createPayment', 'membership_fee')
        ->assertSet('paymentType', 'membership_fee')
        ->assertSet('paymentStartDate', '2026-09-01')
        ->assertSet('paymentEndDate', '2026-09-30')
        ->assertSet('paymentDate', '2026-09-25')
        ->assertSet('paymentValue', '3.00')
        ->call('savePayment')
        ->assertHasNoErrors();

    expect($member->feesPaidUntil()?->toDateString())->toBe('2026-09-30');
});

test('records the joining fee without a period', function () {
    $shelter = Shelter::factory()->create();
    $member = Member::factory()->for($shelter)->create(['joining_fee' => 10]);

    $this->actingAs(User::factory()->forShelter($shelter)->create());

    Livewire::test(MemberShow::class, ['member' => $member])
        ->call('createPayment', 'joining_fee')
        ->assertSet('paymentValue', '10.00')
        ->set('paymentMethod', 'cash')
        ->call('savePayment')
        ->assertHasNoErrors();

    expect($member->payments()->sole())
        ->type->toBe('joining_fee')
        ->start_date->toBeNull()
        ->payment_method->toBe('cash')
        ->and($member->owesJoiningFee())->toBeFalse();
});

test('validates the membership fee payment', function () {
    $shelter = Shelter::factory()->create();
    $member = Member::factory()->for($shelter)->create();

    $this->actingAs(User::factory()->forShelter($shelter)->create());

    Livewire::test(MemberShow::class, ['member' => $member])
        ->call('createPayment', 'membership_fee')
        ->set('paymentStartDate', '2026-06-01')
        ->set('paymentEndDate', '2026-05-31')
        ->set('paymentDate', '')
        ->set('paymentValue', '-1')
        ->set('paymentMethod', 'cheque')
        ->call('savePayment')
        ->assertHasErrors([
            'paymentEndDate' => 'after_or_equal',
            'paymentDate' => 'required',
            'paymentValue' => 'min',
            'paymentMethod' => 'in',
        ]);
});

test('edits and deletes a payment of the member', function () {
    $shelter = Shelter::factory()->create();
    $member = Member::factory()->for($shelter)->create();
    $payment = MemberPayment::factory()->for($member)->create(['payment_value' => 12]);

    $this->actingAs(User::factory()->forShelter($shelter)->create());

    Livewire::test(MemberShow::class, ['member' => $member])
        ->call('editPayment', $payment->id)
        ->assertSet('paymentValue', '12.00')
        ->set('paymentValue', '15')
        ->call('savePayment')
        ->assertHasNoErrors()
        ->call('deletePayment', $payment->id);

    expect($payment->fresh())
        ->payment_value->toBe('15.00')
        ->trashed()->toBeTrue();
});

test('cannot edit a payment of another member', function () {
    $shelter = Shelter::factory()->create();
    $member = Member::factory()->for($shelter)->create();
    $otherPayment = MemberPayment::factory()->for(Member::factory()->for($shelter))->create();

    $this->actingAs(User::factory()->forShelter($shelter)->create());

    expect(fn () => Livewire::test(MemberShow::class, ['member' => $member])->call('editPayment', $otherPayment->id))
        ->toThrow(ModelNotFoundException::class);
});
