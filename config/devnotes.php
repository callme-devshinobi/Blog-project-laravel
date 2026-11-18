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

    // ---------- Paid plan ----------
    'billing' => [
        'grace_days'  => (int) env('BILLING_GRACE_DAYS', 3),
        'price_label' => env('PRO_PRICE_LABEL', 'Pro plan, billed monthly'),   // display only
    ],

    // ---------- AI providers writers can connect (Claude is the default) ----------
    'ai' => [
        'default_provider' => 'anthropic',
        'providers' => [
            'anthropic' => [
                'label' => 'Claude (Anthropic)',
                'hint'  => 'Keys usually start with sk-ant-',
                'url'   => 'https://console.anthropic.com',
                'model' => env('AI_MODEL_ANTHROPIC'),
            ],
            'openai' => [
                'label' => 'ChatGPT (OpenAI)',
                'hint'  => 'Keys usually start with sk-',
                'url'   => 'https://platform.openai.com/api-keys',
                'model' => env('AI_MODEL_OPENAI'),
            ],
            'gemini' => [
                'label' => 'Gemini (Google)',
                'hint'  => 'Keys usually start with AIza',
                'url'   => 'https://aistudio.google.com/app/apikey',
                'model' => env('AI_MODEL_GEMINI'),
            ],
        ],
    ],
];