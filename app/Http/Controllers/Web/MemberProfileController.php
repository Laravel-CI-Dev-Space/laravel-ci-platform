<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class MemberProfileController extends Controller
{
    public function show(string $username): View
    {
        $member = User::where('github_username', $username)
            ->with('profile.grade')
            ->withCount(['questions', 'answers', 'articles'])
            ->firstOrFail();

        if ($member->profile) {
            $member->bio        = $member->profile->bio;
            $member->location   = collect([$member->profile->city, $member->profile->country])->filter()->implode(', ');
            $member->skills     = $member->profile->tech_stack;
            $member->reputation = $member->profile->points;
            $member->grade      = $member->profile->grade;
        }

        $recentQuestions = $member->questions()->with('tags')->latest()->take(5)->get();
        $recentArticles  = $member->articles()->where('status', 'published')->latest()->take(5)->get();

        return view('web.members.show', compact('member', 'recentQuestions', 'recentArticles'));
    }
}
