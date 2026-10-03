@php
    $pages = [
        'privacy' => [
            'title' => 'Privacy Policy',
            'intro' => 'This page explains how RenovaHub handles the information you share while planning a renovation.',
            'sections' => [
                ['Information we collect', 'When you create an account or write to us, we keep the details needed to run your workspace: your name, email address, account role, and the messages you send through the contact form.'],
                ['How it is used', 'We use that information to operate RenovaHub, reply to you, and keep homeowners, designers and contractors working from the same project record.'],
                ['Contact', 'Questions about privacy can be sent to hello@renovahub.com. RenovaHub is based in Colombo, Sri Lanka.'],
            ],
        ],
        'terms' => [
            'title' => 'Terms of Service',
            'intro' => 'These terms describe the agreement for using the RenovaHub workspace.',
            'sections' => [
                ['Using RenovaHub', 'RenovaHub is a shared place to plan, collaborate on and track a renovation. You are responsible for the accuracy of the project information you add and for keeping your login private.'],
                ['Accounts', 'Homeowner, contractor and designer accounts are for the people invited into a project. Do not share access in a way that exposes another person’s private documents or quotations.'],
                ['Contact', 'Questions about these terms can be sent to hello@renovahub.com.'],
            ],
        ],
    ];
    $page = $pages[$page];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $page['title'] }} — RenovaHub</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fraunces:400,500,600|outfit:300,400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-cream font-outfit text-charcoal antialiased">
        <header class="border-b border-line bg-ivory/80">
            <div class="mx-auto flex h-16 max-w-3xl items-center justify-between px-5">
                <x-brand-logo />
                <a href="{{ url('/') }}" class="text-sm text-mist transition hover:text-forest">Back to home</a>
            </div>
        </header>
        <main class="mx-auto max-w-3xl px-5 py-14">
            <p class="text-[11px] font-medium uppercase tracking-[0.22em] text-olive">RenovaHub</p>
            <h1 class="mt-3 font-serif text-4xl font-medium tracking-[-0.03em] text-forest sm:text-5xl">{{ $page['title'] }}</h1>
            <p class="mt-4 text-[15px] leading-relaxed text-mist">{{ $page['intro'] }}</p>
            <div class="mt-10 space-y-8">
                @foreach ($page['sections'] as [$heading, $copy])
                    <section>
                        <h2 class="font-serif text-2xl text-forest">{{ $heading }}</h2>
                        <p class="mt-2 text-sm leading-relaxed text-mist">{{ $copy }}</p>
                    </section>
                @endforeach
            </div>
        </main>
    </body>
</html>
