<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Teknokrat University Event') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('src/output.css') }}">
</head>
<body class="min-h-screen bg-white font-['Manrope',sans-serif] text-[#ab021c]">
@php
    $guestMenu = [
        ['label' => 'Home', 'url' => route('frontend.home')],
        ['label' => 'List Event', 'url' => route('frontend.events')],
        ['label' => 'Login', 'url' => route('login')],
    ];

    $authMenu = [
        ['label' => 'Home', 'url' => route('frontend.home')],
        ['label' => 'Profile', 'url' => route('profile.edit')],
        ['label' => 'My Event', 'url' => route('frontend.my-events')],
        ['label' => 'List Event', 'url' => route('frontend.events')],
        ['label' => 'Sertifikat', 'url' => route('frontend.certificates')],
    ];

    $activeUrl = url()->current();
    $menuItems = auth()->check() ? $authMenu : $guestMenu;
@endphp

<div class="flex min-h-screen flex-col">
    <header id="appHeader" class="sticky top-0 z-50 bg-[#ab021c]/95 text-white backdrop-blur">
        <div class="mx-auto flex min-h-[72px] w-full max-w-7xl items-center justify-between gap-3 px-4">
            <a href="{{ route('frontend.home') }}" class="inline-flex items-center gap-2 font-['Sora',sans-serif] text-sm font-extrabold tracking-wide">
                <img src="{{ asset('logo.png') }}" alt="Logo Universitas Teknokrat Indonesia" class="h-8 w-8 shrink-0 object-contain md:h-9 md:w-9">
                <span class="text-[11px] leading-tight md:text-sm">
                    <span class="block">E-Event Teknokrat</span>
                    <span class="block text-[10px] font-semibold md:text-xs">Universitas Teknokrat Indonesia</span>
                </span>
            </a>

            <nav class="hidden items-center gap-2 md:flex" aria-label="Desktop Menu">
                @foreach($menuItems as $item)
                    @php $isActive = $activeUrl === $item['url']; @endphp
                    <a
                        href="{{ $item['url'] }}"
                        class="inline-flex rounded-full px-4 py-2 text-sm font-bold transition {{ $isActive ? 'bg-white text-[#ab021c]' : 'bg-white/10 text-white hover:bg-white/20' }}"
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach

                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex rounded-full bg-white/10 px-4 py-2 text-sm font-bold text-white transition hover:bg-white/20">
                            Log out
                        </button>
                    </form>
                @endauth
            </nav>

            <button
                id="burgerButton"
                class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-white/10 text-xl font-black text-white transition hover:bg-white/20 md:hidden"
                aria-controls="mobileMenu"
                aria-expanded="false"
                aria-label="Buka menu"
            >
                &#9776;
            </button>
        </div>
    </header>

    <div
        id="mobileMenu"
        class="fixed left-0 right-0 z-[60] hidden bg-black/45 opacity-0 transition-opacity duration-200 md:hidden"
        style="top: 72px; height: calc(100dvh - 72px);"
    >
        <aside id="mobilePanel" class="ml-auto flex h-full w-[min(360px,86vw)] translate-x-full flex-col overflow-y-auto bg-[#ab021c] px-4 py-4 transition-transform duration-200">
            <div class="mt-1 grid gap-1">
                @foreach($menuItems as $item)
                    @php $isActive = $activeUrl === $item['url']; @endphp
                    <a
                        href="{{ $item['url'] }}"
                        class="mobile-link rounded-xl px-3 py-3 text-base font-bold transition {{ $isActive ? 'bg-white text-[#ab021c]' : 'text-white hover:bg-white/10' }}"
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach

                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full rounded-xl px-3 py-3 text-left text-base font-bold text-white transition hover:bg-white/10">
                            Log out
                        </button>
                    </form>
                @endauth
            </div>
        </aside>
    </div>

    <main class="flex-1 py-6 md:py-9">
        <div class="mx-auto w-full max-w-7xl px-4">
            @yield('content')
        </div>
    </main>

    <footer class="bg-white px-3 pb-4 pt-2 md:px-4 md:pb-5">
        <div class="mx-auto w-full max-w-[1800px] rounded-t-[36px] bg-[#b70729] px-4 py-5 text-center font-['Sora',sans-serif] text-[11px] uppercase tracking-[0.45px] md:text-[1.1rem] md:tracking-[0.6px]">
            <span class="font-extrabold text-[#ffe600]">&copy; Copyright {{ date('Y') }}.</span>
            <span class="font-semibold text-white"> All Rights Reserved By Universitas Teknokrat Indonesia</span>
        </div>
    </footer>
</div>

<script>
    (function () {
        const appHeader = document.getElementById('appHeader');
        const burgerButton = document.getElementById('burgerButton');
        const mobileMenu = document.getElementById('mobileMenu');
        const mobilePanel = document.getElementById('mobilePanel');
        const mobileLinks = mobileMenu ? mobileMenu.querySelectorAll('.mobile-link') : [];
        let closeTimer = null;

        if (!appHeader || !burgerButton || !mobileMenu || !mobilePanel) {
            return;
        }

        function syncMobileMenuBounds() {
            const headerHeight = Math.ceil(appHeader.getBoundingClientRect().height || 72);
            const viewportHeight = window.visualViewport
                ? Math.round(window.visualViewport.height)
                : window.innerHeight;
            const boundedHeight = Math.max(0, viewportHeight - headerHeight);

            mobileMenu.style.top = headerHeight + 'px';
            mobileMenu.style.height = boundedHeight + 'px';
        }

        function openMenu() {
            if (closeTimer) {
                clearTimeout(closeTimer);
                closeTimer = null;
            }

            syncMobileMenuBounds();
            mobileMenu.classList.remove('hidden');
            requestAnimationFrame(function () {
                mobileMenu.classList.remove('opacity-0');
                mobileMenu.classList.add('opacity-100');
                mobilePanel.classList.remove('translate-x-full');
            });

            burgerButton.setAttribute('aria-expanded', 'true');
            document.body.style.overflow = 'hidden';
        }

        function closeMenu() {
            mobileMenu.classList.remove('opacity-100');
            mobileMenu.classList.add('opacity-0');
            mobilePanel.classList.add('translate-x-full');
            burgerButton.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';

            closeTimer = setTimeout(function () {
                mobileMenu.classList.add('hidden');
            }, 200);
        }

        burgerButton.addEventListener('click', function () {
            const isOpen = burgerButton.getAttribute('aria-expanded') === 'true';
            if (isOpen) {
                closeMenu();
                return;
            }
            openMenu();
        });

        mobileLinks.forEach(function (link) {
            link.addEventListener('click', closeMenu);
        });

        mobileMenu.addEventListener('click', function (event) {
            if (event.target === mobileMenu) {
                closeMenu();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && burgerButton.getAttribute('aria-expanded') === 'true') {
                closeMenu();
            }
        });

        window.addEventListener('resize', syncMobileMenuBounds);
        window.addEventListener('orientationchange', syncMobileMenuBounds);
        if (window.visualViewport) {
            window.visualViewport.addEventListener('resize', syncMobileMenuBounds);
        }

        syncMobileMenuBounds();
    })();
</script>
@stack('scripts')
</body>
</html>
