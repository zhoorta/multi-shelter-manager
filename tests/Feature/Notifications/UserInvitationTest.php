<?php

use App\Models\Shelter;
use App\Models\User;
use App\Notifications\UserInvitation;

test('invitation email names the app from the configured app name', function () {
    config(['app.name' => 'Abrigo Teste']);
    app()->setLocale('pt');

    $shelter = Shelter::factory()->create();
    $recipient = User::factory()->forShelter($shelter, 'staff')->create();

    $lines = (new UserInvitation('token'))->toMail($recipient)->introLines;

    expect($lines)->toContain('Foi convidado a juntar-se à plataforma Abrigo Teste.');
});

test('invitation email subject names the app from the configured app name', function () {
    config(['app.name' => 'Abrigo Teste']);
    app()->setLocale('pt');

    $recipient = User::factory()->admin()->create();

    $subject = (new UserInvitation('token'))->toMail($recipient)->subject;

    expect($subject)->toBe('Foi Convidado para a plataforma Abrigo Teste');
});

test('admin invitation email names the app from the configured app name', function () {
    config(['app.name' => 'Abrigo Teste']);
    app()->setLocale('pt');

    $recipient = User::factory()->admin()->create();

    $lines = (new UserInvitation('token'))->toMail($recipient)->introLines;

    expect($lines)->toContain('Foi convidado a juntar-se à plataforma Abrigo Teste como administrador.');
});
