<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Mail\MentionMail;
use App\Mail\NewAnswerMail;
use App\Mail\WelcomeMail;
use App\Models\Answer;
use App\Models\Profile;
use App\Models\Question;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

uses(TestCase::class);

it('queues the welcome email', function () {
    Mail::fake();
    $user = User::factory()->create();

    app(NotificationService::class)->sendWelcome($user);

    Mail::assertQueued(WelcomeMail::class);
});

it('sends a new answer notification by default', function () {
    Mail::fake();
    $author   = User::factory()->create();
    $question = Question::factory()->for($author)->create();
    $answer   = Answer::factory()->for($question)->create();

    app(NotificationService::class)->sendNewAnswer($author, $answer);

    Mail::assertQueued(NewAnswerMail::class);
    expect($author->notifications()->count())->toBe(1);
});

it('does not send a new answer notification when the user opted out', function () {
    Mail::fake();
    $author = User::factory()->create();
    Profile::create([
        'user_id'                  => $author->id,
        'notification_preferences' => ['new_answer' => false],
    ]);
    $question = Question::factory()->for($author)->create();
    $answer   = Answer::factory()->for($question)->create();

    app(NotificationService::class)->sendNewAnswer($author, $answer);

    Mail::assertNotQueued(NewAnswerMail::class);
    expect($author->notifications()->count())->toBe(0);
});

it('builds the mention context and notifies the mentioned user for a question', function () {
    Mail::fake();
    $mentioned = User::factory()->create();
    $author    = User::factory()->create();
    $question  = Question::factory()->for($author)->create(['title' => 'Comment migrer vers Laravel 13 ?']);

    app(NotificationService::class)->sendMention($mentioned, $author, $question);

    Mail::assertQueued(MentionMail::class);

    $notification = $mentioned->notifications()->first();
    expect($notification->data['message'])->toContain($author->name);
    expect($notification->data['message'])->toContain($question->title);
    expect($notification->data['url'])->toBe(route('forum.show', $question->slug));
});

it('notifies all admins and super-admins of a new company registration request', function () {
    $admin      = User::factory()->create();
    $superAdmin = User::factory()->create();
    $member     = User::factory()->create();
    $admin->assignRole('admin');
    $superAdmin->assignRole('super-admin');
    $member->assignRole('member');

    app(NotificationService::class)->notifyAdmins('company_registration', [
        'message' => 'Nouvelle demande entreprise : Acme',
        'url'     => '/admin/company-registrations',
        'icon'    => 'fa-solid fa-building',
    ]);

    expect($admin->notifications()->count())->toBe(1);
    expect($superAdmin->notifications()->count())->toBe(1);
    expect($member->notifications()->count())->toBe(0);
});
