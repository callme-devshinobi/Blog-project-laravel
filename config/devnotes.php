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
        'plan_name'     => 'Pro',
        'tagline'       => 'Use your own domain and your own AI key.',
        'features'      => [
            'Use your own domain for your blog',
            'Connect your own AI key (Claude, ChatGPT or Gemini) for writing help',
        ],
        'grace_days'    => (int) env('BILLING_GRACE_DAYS', 3),
        'price_ngn'     => (int) env('PRO_PRICE_NGN', 5000),
        'price_label'   => '₦' . number_format((int) env('PRO_PRICE_NGN', 5000)) . ' per month',
        'checkout_open' => true,
    ],

        // ---------- Look and feel (can be changed in Admin, General settings) ----------
        'site' => [
            'name'         => 'DevNotes',
            'footer'       => 'Built with Laravel ♥ by Dennis Onwuegbu',
            'hero_eyebrow' => 'Welcome to DevNotes',
            'hero_title'   => 'Thoughts, tutorials and real-world tech insights.',
            'hero_desc'    => 'A blog for developers, creators and curious minds. Learn, build and grow — one post at a time.',
            'hero_image'   => 'https://kimi-web-img.kimi.ai/img/cdn.pixabay.com/4f59813a0eb8024b15572745b58916dc3957b4a1.jpg',
            'logo_path'       => null,
            'hero_image_path' => null,
            'colors' => [
                'brand'  => '#6c63ff',
                'header' => '#0c0c16',
                'page'   => '#f6f7fb',
            ],
        ],

            // ---------- Main About / Contact / FAQ text (editable in Admin, Pages) ----------
    'pages' => [

        'about' => [
            'eyebrow'        => 'About DevNotes',
            'title'          => 'Learn, build and grow — one post at a time.',
            'intro'          => 'DevNotes is a blog for developers, creators and curious minds. Here I share practical tutorials, honest lessons from real projects, and the habits that keep me improving.',
            'author_heading' => "Hi, I'm Dennis Onwuegbu 👋",
            'lead'           => "I'm a software developer and writer who believes the best way to learn is to build — and then write about it.",
            'body'           => "I started DevNotes to document my own journey with Laravel, JavaScript and web development, and it quickly grew into a place where beginners and experienced devs alike can find clear, practical guidance.\n\nEverything on this blog comes from real projects and real mistakes. No fluff, no gatekeeping — just what actually works.",
            'chips'          => ['Laravel', 'PHP', 'JavaScript', 'CSS', 'MySQL', 'HTML', 'Iconic'],
            'portrait_name'  => 'Dennis Onwuegbu',
            'portrait_role'  => 'Writer & Developer',
            'stats' => [
                ['value' => '{posts}',      'label' => 'Articles published'],
                ['value' => '{categories}', 'label' => 'Categories covered'],
                ['value' => '{blogs}',      'label' => 'Blogs on the platform'],
                ['value' => '2024',         'label' => 'DevNotes founded'],
            ],
            'find_heading' => "What you'll find here",
            'find' => [
                ['tag' => 'TUTORIALS', 'title' => 'Step-by-step guides',          'text' => 'From "Getting Started with Laravel" to full project builds — written for humans, not robots.'],
                ['tag' => 'TOOLS',     'title' => 'Essential tools & resources',  'text' => 'VS Code extensions, packages and workflows that genuinely make development faster and more enjoyable.'],
                ['tag' => 'MINDSET',   'title' => 'Honest career insights',       'text' => 'Habits, lessons and the occasional hard truth about growing as a developer.'],
            ],
            'why_heading' => 'Why I write',
            'why_body'    => "Teaching is the best form of learning. Every tutorial on DevNotes forces me to understand a topic deeply enough to explain it simply.\n\nIf a post helps one person avoid a bug I spent three hours debugging, it's worth it.\n\nNew articles drop regularly — subscribe so you never miss one.",
            'cta_title'           => "Let's build something together",
            'cta_text'            => "Subscribe for new tutorials every week, or reach out if you have a topic you'd love to see covered.",
            'cta_primary_label'   => 'Subscribe to the newsletter',
            'cta_primary_url'     => '',
            'cta_secondary_label' => 'Get in touch →',
            'cta_secondary_url'   => '/contact',
        ],

        'contact' => [
            'eyebrow' => 'Get in touch',
            'title'   => "Let's talk.",
            'intro'   => 'Have a question, a topic suggestion, or a project idea? Drop a message below — I read every email and usually reply within 24 hours.',
            'emails'  => [
                ['title' => 'Email',             'text' => 'For general enquiries, collaborations and feedback.', 'address' => 'hello@devnotes.dev'],
                ['title' => 'Topic suggestions', 'text' => 'Want a tutorial on something specific? Let me know — the best posts start with reader requests.', 'address' => 'ideas@devnotes.dev'],
            ],
            'response_title' => 'Response time',
            'response_text'  => 'I aim to reply within 24 hours on weekdays. For faster answers, check the FAQ below.',
            'social_heading' => 'Find me elsewhere',
            'social_text'    => 'I share snippets, behind-the-scenes and dev tips across these platforms.',
            'social' => [
                ['label' => 'GitHub',      'url' => ''],
                ['label' => 'LinkedIn',    'url' => ''],
                ['label' => 'X / Twitter', 'url' => ''],
                ['label' => 'YouTube',     'url' => ''],
            ],
            'form_enabled' => true,
            'form_heading' => 'Send a message',
            'form_note'    => 'Your details are never shared. Ever.',
        ],

        'faq' => [
            'title'    => 'Frequently asked questions',
            'subtitle' => 'Quick answers before you hit send.',
            'items' => [
                ['q' => 'Can I request a tutorial topic?', 'a' => 'Absolutely — use the "Suggest a topic" subject above. Reader requests shape the DevNotes content calendar, and popular suggestions usually become full series.'],
                ['q' => 'Do you accept guest posts or collaborations?', 'a' => 'Occasionally, yes. If you have a genuinely useful technical article in mind, pitch it via email with an outline. Quality and originality matter more than anything else.'],
                ['q' => 'I found a bug in one of your tutorials. What should I do?', 'a' => 'Please report it! Choose "Report a code issue" in the subject dropdown and include the article link and the code that\'s failing. Fixes are usually published within a few days.'],
                ['q' => 'How can I support DevNotes?', 'a' => "Subscribe to the newsletter, share articles that helped you, and leave comments — that's more than enough. 🙌"],
            ],
        ],
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