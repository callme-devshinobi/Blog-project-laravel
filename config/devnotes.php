<?php

$appHost = parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST);

return [
    // hosts that belong to DevNotes itself
    'main_hosts' => array_values(array_unique(array_filter(array_merge(
        [$appHost, 'www.' . $appHost, 'localhost', '127.0.0.1'],
        array_map('trim', explode(',', env('MAIN_HOSTS', '')))
    )))),

    // what owners point their DNS at (shown on the settings page)
    'domain_target' => env('DOMAIN_TARGET', $appHost),
    'server_ip'     => env('SERVER_IP', ''),
];