<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\Question;
use App\Models\Tag;
use App\Models\User;

it('lets an admin enable view-as-member, post a question, then disable it', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Profile::create(['user_id' => $admin->id]);

    // 1. Enable view-as-member.
    $response = $this->actingAs($admin)->post(route('view-as-member.enable'));

    $response->assertRedirect(route('dashboard.member.overview'));
    expect(session('viewing_as_member'))->toBeTrue();

    // The member dashboard is now reachable.
    $this->get(route('dashboard.member.overview'))->assertOk();

    // 2. Post a question while viewing as a member.
    $tag = Tag::create(['name' => 'View As Member Tag', 'slug' => 'view-as-member-tag', 'scope' => 'forum']);

    $response = $this->post(route('forum.questions.store'), [
        'title' => 'Quelle est la meilleure pratique pour les jobs en Laravel ?',
        'body'  => 'Je voudrais comprendre comment structurer mes jobs en file d\'attente correctement.',
        'tags'  => [$tag->id],
    ]);

    $question = Question::first();
    expect($question)->not->toBeNull();
    expect($question->user_id)->toBe($admin->id);
    $response->assertRedirect(route('forum.show', $question->slug));

    // 3. Disable view-as-member, back to admin mode.
    $response = $this->post(route('view-as-member.disable'));

    $response->assertRedirect('/admin');
    expect(session('viewing_as_member'))->toBeNull();
});

it('blocks a regular member from accessing the member dashboard via view-as-member without an active session', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    Profile::create(['user_id' => $admin->id]);

    $this->actingAs($admin)->get(route('dashboard.member.overview'))->assertForbidden();
});
