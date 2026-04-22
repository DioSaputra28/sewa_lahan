<!DOCTYPE html>

<html class="light" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>{{ get_site_name() }} - Modern Market Lapak Rentals</title>
    @php
        $faviconUrl = get_versioned_favicon_url();
    @endphp
    @if (filled($faviconUrl))
        <link rel="icon" href="{{ $faviconUrl }}">
        <link rel="shortcut icon" href="{{ $faviconUrl }}">
    @endif
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#47eb7e",
                        "background-light": "#f6f8f6",
                        "background-dark": "#112116",
                    },
                    fontFamily: {
                        "display": ["Inter"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    @yield('head')
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 antialiased">
    @php
        $officeWhatsappLink = get_office_whatsapp_link();
    @endphp

    @include('web.layouts.navbar')

    @yield('content')

    @include('web.layouts.footer')

    @if (filled($officeWhatsappLink))
        <a
            href="{{ $officeWhatsappLink }}"
            target="_blank"
            rel="noopener noreferrer"
            aria-label="Chat via WhatsApp"
            class="fixed bottom-6 right-6 z-50 inline-flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] text-white shadow-xl shadow-emerald-600/30 transition-transform hover:scale-105 active:scale-95"
        >
            <span class="material-symbols-outlined text-2xl">chat</span>
        </a>
    @endif
</body>
</html>
