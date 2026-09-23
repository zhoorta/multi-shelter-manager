<?php

test('the home page is publicly accessible when the public portal is enabled', function () {
    config(['app.public_portal_enabled' => true]);

    $this->get(route('home'))->assertOk();
});

test('the home page redirects to login when the public portal is disabled', function () {
    config(['app.public_portal_enabled' => false]);

    $this->get(route('home'))->assertRedirect(route('login'));
});
