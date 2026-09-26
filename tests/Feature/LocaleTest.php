<?php

use App\Livewire\Settings\Appearance;
use App\Models\Shelter;
use App\Models\User;
use App\Notifications\ShelterMembershipAdded;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

test('a guest can switch language and later pages render in it', function () {
    $this->from(route('about'))
        ->post(route('locale.update'), ['locale' => 'fr'])
        ->assertRedirect(route('about'))
        ->assertSessionHas('locale', 'fr');

    $this->get(route('about'))->assertSee('<html lang="fr"', false);
});

test('each option in the language menu submits its own language code', function () {
    $response = $this->get(route('about'));

    foreach (array_keys(config('app.available_locales')) as $code) {
        $response->assertSee('<input type="hidden" name="locale" value="'.$code.'">', false);
    }
});

test('switching to an unsupported language is rejected', function () {
    $this->post(route('locale.update'), ['locale' => 'ja'])
        ->assertSessionHasErrors('locale')
        ->assertSessionMissing('locale');
});

test('a logged-in user switching language saves it to their account', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('locale.update'), ['locale' => 'de']);

    expect($user->fresh()->locale)->toBe('de');
});

test("a user's saved language wins over the session", function () {
    $user = User::factory()->create(['locale' => 'pt']);

    $response = $this->actingAs($user)->withSession(['locale' => 'es'])->get(route('about'));

    $response->assertSee('<html lang="pt"', false);
});

test("the browser's language does not change the installation default", function () {
    config(['app.locale' => 'pt']);
    app()->setLocale('pt');

    $this->withHeader('Accept-Language', 'de-DE,de;q=0.9')
        ->get(route('about'))
        ->assertSee('<html lang="pt"', false);
});

test('the appearance settings save the chosen language', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Appearance::class)
        ->set('locale', 'it')
        ->assertHasNoErrors()
        ->assertRedirect(route('appearance.edit'));

    expect($user->fresh()->locale)->toBe('it')
        ->and(session('locale'))->toBe('it');
});

test('the appearance settings reject an unsupported language', function () {
    $user = User::factory()->create(['locale' => 'pt']);

    $this->actingAs($user);

    Livewire::test(Appearance::class)
        ->set('locale', 'ja')
        ->assertHasErrors(['locale' => 'in']);

    expect($user->fresh()->locale)->toBe('pt');
});

test("notifications are sent in the recipient's language", function () {
    Notification::fake();
    $shelter = Shelter::factory()->create();
    $french = User::factory()->create(['locale' => 'fr']);
    $noPreference = User::factory()->create(['locale' => null]);

    $french->notify(new ShelterMembershipAdded($shelter, 'staff'));
    $noPreference->notify(new ShelterMembershipAdded($shelter, 'staff'));

    Notification::assertSentTo($french, ShelterMembershipAdded::class, fn ($notification, $channels, $notifiable, $locale) => $locale === 'fr');
    Notification::assertSentTo($noPreference, ShelterMembershipAdded::class, fn ($notification, $channels, $notifiable, $locale) => $locale === config('app.locale'));
});
