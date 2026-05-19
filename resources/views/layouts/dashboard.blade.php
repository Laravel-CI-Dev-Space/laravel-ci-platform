@extends('layouts.base')

@section('body')
<div class="min-h-screen bg-gray-50">

    {{-- ── Navbar ────────────────────────────────────────────── --}}
    <nav class="bg-[#1C1C2E] px-6 py-3 flex items-center justify-between shadow-lg">
        <a href="{{ route('dashboard') }}" class="text-primary font-extrabold text-lg tracking-tight">
            Laravel <span class="text-white">CI</span>
        </a>

        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 text-white text-sm">
                <img src="{{ auth()->user()->avatar }}"
                     alt="{{ auth()->user()->name }}"
                     class="w-8 h-8 rounded-full border-2 border-primary object-cover">
                <span class="hidden sm:inline font-medium">{{ auth()->user()->name }}</span>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex items-center gap-1.5 text-gray-400 hover:text-white text-sm transition-colors px-2 py-1 rounded hover:bg-white/10">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span class="hidden sm:inline">Déconnexion</span>
                </button>
            </form>
        </div>
    </nav>

    {{-- ── Contenu ───────────────────────────────────────────── --}}
    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
        @yield('content')
    </main>

</div>
@endsection
