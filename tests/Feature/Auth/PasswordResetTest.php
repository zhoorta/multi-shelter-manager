<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::resetPasswords());
});

test('reset password link screen can be rendered', function () {
    $response = $this->get(route('password.request'));

    $response->assertOk();
});

test('reset password link screen shows the app name below the logo', function () {
    config(['app.name' => 'Abrigo Teste']);

    $this->get(route('password.request'))
        ->assertSee('Abrigo Teste')
        ->assertDontSeeHtml('<span class="sr-only">Abrigo Teste</span>');
});

test('reset password link can be requested', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.request'), ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class);
});

test('reset password screen can be rendered', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.request'), ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
        $response = $this->get(route('password.reset', $notification->token));

        $response->assertOk();

        return true;
    });
});

test('password can be reset with valid token', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.request'), ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $response = $this->post(route('password.update'), [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('login', absolute: false));

        return true;
    });
});

test('resetting the password verifies an unverified email, as with an invitation link', function () {
    Notification::fake();

    $user = User::factory()->unverified()->create();

    $this->post(route('password.request'), ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $this->post(route('password.update'), [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        expect($user->fresh()->email_verified_at)->not->toBeNull();

        return true;
    });
});

test('resetting the password does not change an already verified email timestamp', function () {
    Notification::fake();

    $user = User::factory()->create();
    $originalVerifiedAt = $user->email_verified_at;

    $this->post(route('password.request'), ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user, $originalVerifiedAt) {
        $this->post(route('password.update'), [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        expect($user->fresh()->email_verified_at)->toEqual($originalVerifiedAt);

        return true;
    });
});

test('forgot password screen is translated to portuguese', function () {
    app()->setLocale('pt');

    $this->get(route('password.request'))
        ->assertOk()
        ->assertSee('Recuperar palavra-passe')
        ->assertSee('Enviar link de redefinição')
        ->assertDontSee('Email password reset link');
});

test('reset link sent status is translated to portuguese', function () {
    Notification::fake();
    app()->setLocale('pt');

    $user = User::factory()->create();

    $this->post(route('password.request'), ['email' => $user->email])
        ->assertSessionHas('status', 'Enviámos por email o link de redefinição da palavra-passe.');
});

test('reset password screen is translated to portuguese', function () {
    app()->setLocale('pt');

    $this->get(route('password.reset', 'token'))
        ->assertOk()
        ->assertSee('Redefinir palavra-passe')
        ->assertSee('Introduza a sua nova palavra-passe abaixo')
        ->assertDontSee('Please enter your new password below');
});

test('reset password email subject is translated to portuguese', function () {
    Notification::fake();
    app()->setLocale('pt');

    $user = User::factory()->create();

    $this->post(route('password.request'), ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user) {
        return $notification->toMail($user)->subject === 'Redefina a sua palavra-passe';
    });
});
