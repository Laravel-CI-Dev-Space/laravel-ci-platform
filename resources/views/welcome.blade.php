@extends('layouts.base')

@section('title', 'Laravel CI — Hub des développeurs Laravel ivoiriens')

@section('body')
<div class="min-h-screen bg-[#1C1C2E] text-white">

    {{-- ── Navigation ───────────────────────────────────────── --}}
    <header class="max-w-5xl mx-auto px-6 py-5 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <img src="{{ asset('assets/logo.jpeg') }}" alt="Laravel CI"
                 class="w-9 h-9 rounded-full border-2 border-primary object-cover">
            <span class="font-extrabold text-lg">
                Laravel <span class="text-primary">CI</span>
            </span>
        </div>

        <nav class="flex items-center gap-3">
            @auth
                <a href="{{ route('dashboard') }}"
                   class="px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-orange-500 transition-colors">
                    <i class="fa-solid fa-gauge-high mr-1.5"></i>Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-sm font-medium rounded-lg transition-colors border border-white/20">
                    <i class="fa-brands fa-github mr-1.5"></i>Se connecter
                </a>
            @endauth
        </nav>
    </header>

    {{-- ── Hero ─────────────────────────────────────────────── --}}
    <section class="max-w-3xl mx-auto px-6 pt-16 pb-20 text-center">
        <span class="inline-flex items-center gap-2 bg-primary/15 text-primary border border-primary/30 text-xs font-semibold px-3 py-1.5 rounded-full mb-6">
            <i class="fa-solid fa-circle-dot text-[8px]"></i>
            Communauté open source — Côte d'Ivoire
        </span>

        <h1 class="text-4xl sm:text-5xl font-extrabold leading-tight mb-5">
            Le hub des développeurs<br>
            <span class="text-primary">Laravel ivoiriens</span>
        </h1>

        <p class="text-gray-400 text-lg leading-relaxed mb-8 max-w-xl mx-auto">
            Rejoignez une communauté de plus de 500 développeurs passionnés.
            Forum, événements, offres d'emploi et ressources Laravel — tout en un.
        </p>

        @guest
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-3 px-7 py-3.5 bg-primary text-white font-bold text-base rounded-xl hover:bg-orange-500 transition-colors shadow-lg shadow-primary/25">
                <svg viewBox="0 0 24 24" class="w-5 h-5 fill-white shrink-0" aria-hidden="true">
                    <path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/>
                </svg>
                Rejoindre avec GitHub
            </a>
        @else
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center gap-2 px-7 py-3.5 bg-primary text-white font-bold text-base rounded-xl hover:bg-orange-500 transition-colors">
                <i class="fa-solid fa-arrow-right"></i>
                Accéder à mon espace
            </a>
        @endguest
    </section>

    {{-- ── Features ─────────────────────────────────────────── --}}
    <section class="bg-white/5 border-y border-white/10">
        <div class="max-w-5xl mx-auto px-6 py-14 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            <div class="text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-primary/15 rounded-xl mb-4">
                    <i class="fa-solid fa-comments text-primary text-xl"></i>
                </div>
                <h3 class="font-bold text-white mb-1">Forum technique</h3>
                <p class="text-sm text-gray-400">Posez vos questions Laravel, entraidez-vous.</p>
            </div>

            <div class="text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-primary/15 rounded-xl mb-4">
                    <i class="fa-solid fa-newspaper text-primary text-xl"></i>
                </div>
                <h3 class="font-bold text-white mb-1">Blog & ressources</h3>
                <p class="text-sm text-gray-400">Tutoriels, boilerplates et guides PDF.</p>
            </div>

            <div class="text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-primary/15 rounded-xl mb-4">
                    <i class="fa-solid fa-calendar-days text-primary text-xl"></i>
                </div>
                <h3 class="font-bold text-white mb-1">Événements</h3>
                <p class="text-sm text-gray-400">Meetups, hackathons et talks en ligne.</p>
            </div>

            <div class="text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-primary/15 rounded-xl mb-4">
                    <i class="fa-solid fa-briefcase text-primary text-xl"></i>
                </div>
                <h3 class="font-bold text-white mb-1">Job Board</h3>
                <p class="text-sm text-gray-400">Offres Laravel en Côte d'Ivoire et en remote.</p>
            </div>

        </div>
    </section>

    {{-- ── Footer ───────────────────────────────────────────── --}}
    <footer class="max-w-5xl mx-auto px-6 py-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500">
        <span>
            <span class="text-primary font-semibold">Laravel Côte d'Ivoire</span>
            &mdash; 2026 &mdash; Open Source MIT
        </span>
        <span>v{{ app()->version() }}</span>
    </footer>

</div>
@endsection
