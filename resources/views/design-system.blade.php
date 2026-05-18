@extends('layouts.base')

@push('head')
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
@endpush

@section('title', 'Design System')

@php
    $nav = [
        ['id' => 'stats', 'label' => 'Stats primaires'],
        ['id' => 'stats-secondary', 'label' => 'Stats secondaires'],
        ['id' => 'events', 'label' => 'Événements'],
        ['id' => 'posts', 'label' => 'Articles'],
        ['id' => 'threads', 'label' => 'Forum'],
        ['id' => 'jobs', 'label' => 'Emplois'],
        ['id' => 'members', 'label' => 'Membres'],
    ];
@endphp

@section('body')
    <div class="min-h-screen bg-gray-50 text-gray-900 font-[Nunito]">

        {{-- ── Top bar ──────────────────────────────────────────── --}}
        <header
            class="sticky top-0 z-30 bg-white/90 backdrop-blur border-b border-gray-200 px-6 py-4 flex items-center gap-3">
            <span class="material-icons text-primary">palette</span>
            <div>
                <h1 class="text-lg font-bold leading-none text-gray-900">Design System</h1>
                <p class="text-xs text-gray-400 mt-0.5">Laravel Côte d'Ivoire — composants Blade</p>
            </div>
            <span class="ml-auto text-xs text-gray-500 bg-gray-100 px-3 py-1.5 rounded-lg flex items-center gap-1">
                <span class="material-icons text-sm">layers</span>
                Cards
            </span>
        </header>

        <div class="flex">

            {{-- ── Sidebar ──────────────────────────────────────── --}}
            <aside
                class="sticky top-[61px] h-[calc(100vh-61px)] w-56 shrink-0 bg-white border-r border-gray-200 overflow-y-auto p-5 hidden lg:block">
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Composants</p>
                <nav class="flex flex-col gap-0.5">
                    @foreach ($nav as $item)
                        <a href="#{{ $item['id'] }}" data-nav-link="{{ $item['id'] }}"
                            class="text-sm text-gray-500 hover:text-gray-900 py-1.5 px-3 rounded-lg hover:bg-gray-50 transition-colors block">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>
            </aside>

            {{-- ── Contenu principal ────────────────────────────── --}}
            <main class="flex-1 min-w-0 p-6 lg:p-10 space-y-16">

                {{-- ===== Stats primaires ===== --}}
                <section id="stats" data-section>
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Card Stats</h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Barre accent en haut, icône SVG dans un conteneur coloré, chiffre clé + label.
                            La prop <code class="text-xs bg-gray-100 px-1 rounded">accent</code> (défaut <code
                                class="text-xs bg-gray-100 px-1 rounded">bg-primary</code>)
                            contrôle la couleur de la barre et du conteneur d'icône.
                        </p>
                    </div>

                    <div class="max-w-xs">
                        <div class="rounded-xl overflow-hidden border border-gray-200 bg-white">
                            <div class="flex items-center justify-between px-4 py-2 bg-gray-50 border-b border-gray-200">
                                <span class="text-xs font-mono text-gray-400">x-card.stat</span>
                                @include('design-system._copy-btn', ['snippet' => $snippets['stat']])
                            </div>
                            <div class="p-4">
                                <x-card.stat value="500+" label="Membres Actifs">
                                    <x-slot:icon>
                                        <svg class="size-6 shrink-0 text-white" fill="none" stroke-width="1.5"
                                            stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                                        </svg>
                                    </x-slot:icon>
                                </x-card.stat>
                            </div>
                        </div>
                    </div>
                </section>

                <hr class="border-gray-200">

                {{-- ===== Stats secondaires ===== --}}
                <section id="stats-secondary" data-section>
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Card Stats secondaires</h2>
                        <p class="text-sm text-gray-500 mt-1">Stats compactes pour sidebar ou bandes de métriques.</p>
                    </div>

                    <div class="max-w-xs">
                        <div class="rounded-xl overflow-hidden border border-gray-200 bg-white">
                            <div class="flex items-center justify-between px-4 py-2 bg-gray-50 border-b border-gray-200">
                                <span class="text-xs font-mono text-gray-400">x-card.stat-secondary</span>
                                @include('design-system._copy-btn', ['snippet' => $snippets['stat_sec']])
                            </div>
                            <div class="p-4">
                                <x-card.stat-secondary icon="forum" value="1.2k"
                                    description="Messages mensuels sur le forum" />
                            </div>
                        </div>
                    </div>
                </section>

                <hr class="border-gray-200">

                {{-- ===== Événements ===== --}}
                <section id="events" data-section>
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Card Événement</h2>
                        <p class="text-sm text-gray-500 mt-1">Cover, badges date/type, progress bar places, CTA.</p>
                    </div>

                    <div class="max-w-sm">
                        <div class="rounded-xl overflow-hidden border border-gray-200 bg-white">
                            <div class="flex items-center justify-between px-4 py-2 bg-gray-50 border-b border-gray-200">
                                <span class="text-xs font-mono text-gray-400">x-card.event</span>
                                @include('design-system._copy-btn', ['snippet' => $snippets['event']])
                            </div>
                            <div class="p-4">
                                <x-card.event :event="$event" />
                            </div>
                        </div>
                    </div>
                </section>

                <hr class="border-gray-200">

                {{-- ===== Articles ===== --}}
                <section id="posts" data-section>
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Card Article</h2>
                        <p class="text-sm text-gray-500 mt-1">Cover, badge catégorie, temps de lecture, auteur.</p>
                    </div>

                    <div class="max-w-sm">
                        <div class="rounded-xl overflow-hidden border border-gray-200 bg-white">
                            <div class="flex items-center justify-between px-4 py-2 bg-gray-50 border-b border-gray-200">
                                <span class="text-xs font-mono text-gray-400">x-card.post</span>
                                @include('design-system._copy-btn', ['snippet' => $snippets['post']])
                            </div>
                            <div class="p-4">
                                <x-card.post :post="$post" />
                            </div>
                        </div>
                    </div>
                </section>

                <hr class="border-gray-200">

                {{-- ===== Forum ===== --}}
                <section id="threads" data-section>
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Card Forum</h2>
                        <p class="text-sm text-gray-500 mt-1">Label coloré, tags, votes/réponses, indicateur statut.</p>
                    </div>

                    <div class="max-w-lg">
                        <div class="rounded-xl overflow-hidden border border-gray-200 bg-white">
                            <div class="flex items-center justify-between px-4 py-2 bg-gray-50 border-b border-gray-200">
                                <span class="text-xs font-mono text-gray-400">x-card.forum-thread</span>
                                @include('design-system._copy-btn', ['snippet' => $snippets['thread']])
                            </div>
                            <div class="p-4">
                                <x-card.forum-thread :thread="$thread" />
                            </div>
                        </div>
                    </div>
                </section>

                <hr class="border-gray-200">

                {{-- ===== Emplois ===== --}}
                <section id="jobs" data-section>
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Card Offre d'emploi</h2>
                        <p class="text-sm text-gray-500 mt-1">Logo entreprise, badges contrat/remote, stack tech, CTA.</p>
                    </div>

                    <div class="max-w-sm">
                        <div class="rounded-xl overflow-hidden border border-gray-200 bg-white">
                            <div class="flex items-center justify-between px-4 py-2 bg-gray-50 border-b border-gray-200">
                                <span class="text-xs font-mono text-gray-400">x-card.job</span>
                                @include('design-system._copy-btn', ['snippet' => $snippets['job']])
                            </div>
                            <div class="p-4">
                                <x-card.job :job="$job" />
                            </div>
                        </div>
                    </div>
                </section>

                <hr class="border-gray-200">

                {{-- ===== Membres ===== --}}
                <section id="members" data-section>
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Card Membre</h2>
                        <p class="text-sm text-gray-500 mt-1">Avatar (fallback initiales), pseudo, points, badge rang
                            🥇🥈🥉.</p>
                    </div>

                    <div class="max-w-xs">
                        <div class="rounded-xl overflow-hidden border border-gray-200 bg-white">
                            <div class="flex items-center justify-between px-4 py-2 bg-gray-50 border-b border-gray-200">
                                <span class="text-xs font-mono text-gray-400">x-card.member</span>
                                @include('design-system._copy-btn', ['snippet' => $snippets['member']])
                            </div>
                            <div class="p-4">
                                <x-card.member :member="$member" :rank="1" />
                            </div>
                        </div>
                    </div>
                </section>

                <div class="h-16"></div>
            </main>
        </div>
    </div>

    {{-- ── Script clipboard + sidebar active ──────────────────── --}}
    <script>
        document.querySelectorAll('[data-copy-btn]').forEach(btn => {
            btn.addEventListener('click', () => {
                navigator.clipboard.writeText(btn.dataset.snippet).then(() => {
                    const label = btn.querySelector('[data-copy-label]');
                    label.textContent = 'Copié !';
                    btn.classList.add('text-green-600', 'border-green-300', 'bg-green-50');
                    setTimeout(() => {
                        label.textContent = 'Copier';
                        btn.classList.remove('text-green-600', 'border-green-300',
                            'bg-green-50');
                    }, 2000);
                });
            });
        });

        const navLinks = document.querySelectorAll('[data-nav-link]');

        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                navLinks.forEach(link => {
                    const active = link.dataset.navLink === entry.target.id;
                    link.classList.toggle('text-primary', active);
                    link.classList.toggle('bg-primary/5', active);
                    link.classList.toggle('font-semibold', active);
                    link.classList.toggle('text-gray-500', !active);
                    link.classList.toggle('font-normal', !active);
                });
            });
        }, {
            rootMargin: '-20% 0px -70% 0px'
        });

        document.querySelectorAll('[data-section]').forEach(s => observer.observe(s));
    </script>
@endsection
