<?php

use App\Models\Member;
use App\Models\MemberPayment;
use App\Models\Shelter;
use App\Models\User;
use App\Models\Volunteer;

test('numbers members sequentially within each shelter', function () {
    $shelter = Shelter::factory()->create();
    $otherShelter = Shelter::factory()->create();

    $first = Member::factory()->for($shelter)->create();
    $otherShelterMember = Member::factory()->for($otherShelter)->create();
    $second = Member::factory()->for($shelter)->create();

    expect($first->member_number)->toBe(1)
        ->and($second->member_number)->toBe(2)
        ->and($otherShelterMember->member_number)->toBe(1);
});

test('never reuses the number of a deleted member', function () {
    $shelter = Shelter::factory()->create();
    Member::factory()->for($shelter)->create();
    Member::factory()->for($shelter)->create()->delete();

    expect(Member::factory()->for($shelter)->create()->member_number)->toBe(3);
});

test('keeps an explicitly given member number', function () {
    $member = Member::factory()->create(['member_number' => 150]);

    expect($member->member_number)->toBe(150);
});

test('numbers a member created by a shelter user within that user\'s shelter', function () {
    $shelter = Shelter::factory()->create();
    Member::factory()->for($shelter)->create();
    $this->actingAs(User::factory()->forShelter($shelter)->create());

    $member = Member::factory()->create(['shelter_id' => null]);

    expect($member->shelter_id)->toBe($shelter->id)
        ->and($member->member_number)->toBe(2);
});

test('only shows members of the user\'s current shelter', function () {
    $shelter = Shelter::factory()->create();
    $ownMember = Member::factory()->for($shelter)->create();
    Member::factory()->create();

    $this->actingAs(User::factory()->forShelter($shelter)->create());

    expect(Member::pluck('id')->all())->toBe([$ownMember->id]);
});

test('relates the member to its shelter, volunteer record and payments', function () {
    $volunteer = Volunteer::factory()->create();
    $member = Member::factory()->for($volunteer->shelter)->for($volunteer)->create();
    $payment = MemberPayment::factory()->for($member)->create();

    expect($member->shelter->is($volunteer->shelter))->toBeTrue()
        ->and($volunteer->member->is($member))->toBeTrue()
        ->and($member->payments->sole()->is($payment))->toBeTrue()
        ->and($volunteer->shelter->members->sole()->is($member))->toBeTrue();
});

test('owes the joining fee only when it is above zero and not paid', function (string $joiningFee, bool $paid, bool $owes) {
    $member = Member::factory()->create(['joining_fee' => $joiningFee]);

    if ($paid) {
        MemberPayment::factory()->for($member)->joiningFee()->create();
    }

    expect($member->owesJoiningFee())->toBe($owes);
})->with([
    'free joining fee' => ['0', false, false],
    'unpaid joining fee' => ['10', false, true],
    'paid joining fee' => ['10', true, false],
]);

test('fees are paid until the latest membership fee period end', function () {
    $member = Member::factory()->create();
    MemberPayment::factory()->for($member)->create(['start_date' => '2025-01-01', 'end_date' => '2025-12-31']);
    MemberPayment::factory()->for($member)->create(['start_date' => '2026-01-01', 'end_date' => '2026-12-31']);
    MemberPayment::factory()->for($member)->joiningFee()->create();

    expect($member->feesPaidUntil()?->toDateString())->toBe('2026-12-31');
});

test('an active member is in arrears when owing the joining fee or with no fee covering today', function (array $attributes, ?string $paidUntil, bool $inArrears) {
    $this->travelTo('2026-09-25');
    $member = Member::factory()->create([
        'status' => 'active',
        'joining_fee' => 0,
        'membership_fee' => 12,
        ...$attributes,
    ]);

    if ($paidUntil) {
        MemberPayment::factory()->for($member)->create(['start_date' => '2026-01-01', 'end_date' => $paidUntil]);
    }

    expect($member->isInArrears())->toBe($inArrears);
})->with([
    'fee covers today' => [[], '2026-12-31', false],
    'fee covers until today' => [[], '2026-09-25', false],
    'fee ended yesterday' => [[], '2026-09-24', true],
    'no fee paid yet' => [[], null, true],
    'no membership fee to pay' => [['membership_fee' => 0], null, false],
    'unpaid joining fee' => [['joining_fee' => 10], '2026-12-31', true],
    'suspended member' => [['status' => 'suspended'], null, false],
    'former member' => [['status' => 'left'], null, false],
]);

test('the in arrears scope matches the members in arrears', function () {
    $this->travelTo('2026-09-25');
    $upToDate = Member::factory()->create(['membership_fee' => 12]);
    MemberPayment::factory()->for($upToDate)->create(['start_date' => '2026-01-01', 'end_date' => '2026-12-31']);
    $expired = Member::factory()->create(['membership_fee' => 12]);
    MemberPayment::factory()->for($expired)->create(['start_date' => '2025-01-01', 'end_date' => '2025-12-31']);
    $owesJoiningFee = Member::factory()->create(['membership_fee' => 0, 'joining_fee' => 10]);
    $paidJoiningFee = Member::factory()->create(['membership_fee' => 0, 'joining_fee' => 10]);
    MemberPayment::factory()->for($paidJoiningFee)->joiningFee()->create();
    Member::factory()->create(['membership_fee' => 12, 'status' => 'left']);

    expect(Member::inArrears()->pluck('id')->sort()->values()->all())
        ->toBe([$expired->id, $owesJoiningFee->id])
        ->and(Member::all()->filter->isInArrears()->pluck('id')->values()->all())
        ->toBe([$expired->id, $owesJoiningFee->id]);
});

test('the next fee period starts after the last paid period and lasts the fee frequency', function (string $frequency, ?string $paidUntil, string $start, string $end) {
    $member = Member::factory()->create(['join_date' => '2026-01-15', 'membership_fee_frequency' => $frequency]);

    if ($paidUntil) {
        MemberPayment::factory()->for($member)->create(['start_date' => '2026-01-01', 'end_date' => $paidUntil]);
    }

    [$periodStart, $periodEnd] = $member->nextFeePeriod();

    expect($periodStart->toDateString())->toBe($start)
        ->and($periodEnd->toDateString())->toBe($end);
})->with([
    'first yearly fee starts on the join date' => ['yearly', null, '2026-01-15', '2027-01-14'],
    'monthly fee after a paid month' => ['monthly', '2026-01-31', '2026-02-01', '2026-02-28'],
    'quarterly fee after a paid quarter' => ['quarterly', '2026-03-31', '2026-04-01', '2026-06-30'],
]);
