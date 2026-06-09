<?php

namespace App\Http\Controllers;

use App\Enums\Events\EventRegistrationStatus;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /** Redirects the authenticated user to their role-specific dashboard. */
    public function redirect(): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->hasRole('super-admin')) {
            return redirect()->route('dashboard.super-admin');
        }
        if ($user->hasRole('admin')) {
            return redirect()->route('dashboard.admin');
        }
        if ($user->hasRole('moderator')) {
            return redirect()->route('dashboard.moderator.overview');
        }
        if ($user->hasRole('member')) {
            return redirect()->route('dashboard.member.overview');
        }

        Auth::logout();

        return redirect()->route('login')->with('error', 'Accès non autorisé.');
    }

    /** Redirects admins and super-admins to the Filament admin panel. */
    public function adminPanel(): RedirectResponse
    {
        return redirect(Filament::getPanel('admin')->getUrl());
    }

    public function memberOverview(): View
    {
        /** @var User $user */
        $user = Auth::user();

        return view('dashboard.member.overview', [
            'questionCount'   => $user->questions()->count(),
            'answerCount'     => $user->answers()->count(),
            'articleCount'    => $user->articles()->where('status', 'published')->count(),
            'votesScore'      => $user->questions()->sum('votes_score'),
            'recentQuestions' => $user->questions()->latest()->take(5)->get(),
            'recentArticles'  => $user->articles()->latest()->take(5)->get(),
            'upcomingRegs'    => $user->eventRegistrations()
                ->with('event')
                ->whereNot('status', EventRegistrationStatus::CANCELLED)
                ->whereHas('event', fn ($q) => $q->where('start_date', '>', now()))
                ->latest()
                ->take(3)
                ->get(),
            'recentApps' => $user->jobApplications()
                ->with(['jobOffer.company'])
                ->whereHas('jobOffer')
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }

    public function memberEvents(): View
    {
        /** @var User $user */
        $user = Auth::user();

        $registrations = $user->eventRegistrations()
            ->with('event.type')
            ->whereNot('status', EventRegistrationStatus::CANCELLED)
            ->whereHas('event')
            ->get()
            ->sortBy(fn ($registration) => $registration->event->start_date)
            ->values();

        return view('dashboard.member.events', compact('registrations'));
    }

    public function memberApplications(): View
    {
        /** @var User $user */
        $user = Auth::user();

        $applications = $user->jobApplications()
            ->with(['jobOffer.company'])
            ->whereHas('jobOffer')
            ->latest()
            ->paginate(15);

        return view('dashboard.member.applications', compact('applications'));
    }

    public function memberFavorites(): View
    {
        /** @var User $user */
        $user = Auth::user();

        $favorites = $user->jobFavorites()
            ->with(['jobOffer.company', 'jobOffer.skills'])
            ->latest()
            ->get()
            ->map(fn ($favorite) => $favorite->jobOffer)
            ->filter();

        return view('dashboard.member.favorites', compact('favorites'));
    }
}
