@extends('layouts.base')

@section('body')
<div class="min-h-screen bg-gray-50 text-gray-900">

    <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0">
                <img src="{{ asset('assets/logo.jpeg') }}" alt="Laravel CI"
                     class="w-9 h-9 rounded-full border-2 border-primary object-cover">
                <span class="font-extrabold text-lg text-[#1C1C2E]">
                    Laravel <span class="text-primary">CI</span>
                </span>
            </a>

            <nav class="flex items-center gap-2 sm:gap-4 text-sm font-semibold">
                <a href="{{ route('events.index') }}"
                   class="px-3 py-2 rounded-lg {{ request()->routeIs('events.*') ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:text-primary' }} transition-colors">
                    Événements
                </a>

                @auth
                    <a href="{{ route('dashboard') }}"
                       class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-orange-500 transition-colors">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="px-4 py-2 text-gray-600 hover:text-primary transition-colors">
                        Connexion
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    @if(session('success') || session('error'))
        <div class="max-w-6xl mx-auto px-4 sm:px-6 pt-4">
            @if(session('success'))
                <div class="flex items-start gap-2 bg-green-50 border-l-4 border-green-500 rounded-md p-4 text-sm text-green-800">
                    <i class="fa-solid fa-circle-check mt-0.5 shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="flex items-start gap-2 bg-red-50 border-l-4 border-red-500 rounded-md p-4 text-sm text-red-800">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5 shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
        </div>
    @endif

    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-10">
        @yield('content')
    </main>

    <footer class="border-t border-gray-200 bg-white mt-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6 text-center text-sm text-gray-500">
            Laravel Côte d'Ivoire — Communauté open source {{ date('Y') }}
        </div>
    </footer>
</div>
@endsection
