<?php

/*
|--------------------------------------------------------------------------
| Demo mode
|--------------------------------------------------------------------------
|
| A public try-out instance: the login page offers "Try the demo", which
| copies the template shelter (seeded with DocumentationDemoSeeder) into a
| throwaway shelter with its own manager account. Sandboxes are deleted with
| their files after `ttl_minutes` (`demo:prune`, every ten minutes). Run it
| with PUBLIC_PORTAL_ENABLED=false so sandboxes are never listed publicly.
|
*/

return [
    'enabled' => (bool) env('DEMO_MODE', false),
    'template_shelter_id' => (int) env('DEMO_TEMPLATE_SHELTER_ID', 1),
    'ttl_minutes' => (int) env('DEMO_TTL_MINUTES', 120),
    'max_sandboxes' => (int) env('DEMO_MAX_SANDBOXES', 30),
    'contact' => env('DEMO_CONTACT'),
];
