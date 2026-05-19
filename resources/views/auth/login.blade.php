@extends('layouts.base')

@section('title', 'Connexion')

@section('body')
<div class="min-h-screen bg-[#1C1C2E] flex items-center justify-center p-4">
    <div class="w-full max-w-105">

        {{-- Carte principale --}}
        <div class="bg-white rounded-2xl shadow-2xl px-8 py-10 text-center">

            {{-- Logo --}}
            <div class="mb-6">
                <img src="{{ asset('assets/logo.jpeg') }}" alt="Laravel CI"
                     class="w-22.5 h-22.5 object-contain rounded-full border-[3px] border-primary p-1 mx-auto">
            </div>

            {{-- Titre --}}
            <h1 class="text-2xl font-extrabold text-[#1C1C2E] mb-1">
                Laravel <span class="text-primary">CI</span>
            </h1>
            <p class="text-sm text-gray-400 mb-8">
                Hub communautaire des développeurs Laravel ivoiriens
            </p>

            <hr class="border-gray-100 mb-6">

            {{-- Alertes --}}
            @if(session('error'))
                <div class="flex items-start gap-2 bg-red-50 border-l-4 border-red-500 rounded-md p-3 mb-5 text-left text-sm text-red-700">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5 shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="flex items-start gap-2 bg-green-50 border-l-4 border-green-500 rounded-md p-3 mb-5 text-left text-sm text-green-700">
                    <i class="fa-solid fa-circle-check mt-0.5 shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            {{-- Bouton GitHub --}}
            <a href="{{ route('auth.github.redirect') }}"
               class="flex items-center justify-center gap-3 w-full px-6 py-3.5 bg-[#1C1C2E] text-white rounded-lg font-bold text-base border-2 border-transparent hover:bg-primary transition-colors duration-200">
                <svg viewBox="0 0 24 24" class="w-5 h-5 fill-white shrink-0" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/>
                </svg>
                Se connecter avec GitHub
            </a>

            {{-- Note --}}
            <p class="text-xs text-gray-400 mt-6 leading-relaxed">
                En vous connectant, vous acceptez les
                <a href="#" class="text-primary hover:underline underline-offset-2">conditions d'utilisation</a>
                de la plateforme.
            </p>

        </div>

        {{-- Footer --}}
        <p class="text-center text-xs text-gray-500 mt-5">
            <span class="text-primary font-semibold">Laravel Côte d'Ivoire</span>
            &mdash; 2026 &mdash; Open Source MIT
        </p>

    </div>
</div>
@endsection
