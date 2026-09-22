<?php

test('the home page is publicly accessible', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
});
