<?php

use App\Models\User;
use App\Notifications\UserInvitation;

test('mails use the app logo and the orange brand colour', function () {
    config(['app.name' => 'Focinhos']);

    $html = (string) (new UserInvitation('token'))->toMail(User::factory()->make())->render();

    expect($html)
        ->toContain(asset('apple-touch-icon.png'))
        ->toContain('#ff8904')
        ->toContain('>Focinhos</span>');
});
