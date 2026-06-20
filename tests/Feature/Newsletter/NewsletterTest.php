<?php

use App\Livewire\Web\NewsletterModal;
use App\Mail\NewsletterArticleMail;
use App\Mail\NewsletterEventMail;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

beforeEach(fn () => Mail::fake());

it('creates a subscriber via the modal', function () {
    Livewire::test(NewsletterModal::class)
        ->set('email', 'abonne@test.ci')
        ->set('name', 'Jean')
        ->call('subscribe')
        ->assertSet('subscribed', true);

    expect(NewsletterSubscriber::where('email', 'abonne@test.ci')->exists())->toBeTrue();
});

it('does not create a duplicate subscriber', function () {
    NewsletterSubscriber::factory()->create(['email' => 'deja@test.ci']);

    Livewire::test(NewsletterModal::class)
        ->set('email', 'deja@test.ci')
        ->call('subscribe')
        ->assertSet('subscribed', true);

    expect(NewsletterSubscriber::where('email', 'deja@test.ci')->count())->toBe(1);
});

it('reactivates a previously unsubscribed email', function () {
    $subscriber = NewsletterSubscriber::factory()->create([
        'email'           => 'revenu@test.ci',
        'unsubscribed_at' => now(),
    ]);

    Livewire::test(NewsletterModal::class)
        ->set('email', 'revenu@test.ci')
        ->call('subscribe')
        ->assertSet('subscribed', true);

    expect($subscriber->fresh()->isActive())->toBeTrue();
});

it('validates email format in the modal', function () {
    Livewire::test(NewsletterModal::class)
        ->set('email', 'pas-un-email')
        ->call('subscribe')
        ->assertHasErrors(['email']);
});

it('unsubscribes via the token link', function () {
    $subscriber = NewsletterSubscriber::factory()->create();

    $this->get(route('newsletter.unsubscribe', $subscriber->token))
        ->assertOk()
        ->assertSee('désabonné');

    expect($subscriber->fresh()->isActive())->toBeFalse();
});

it('sends newsletter article mail only to active subscribers', function () {
    NewsletterSubscriber::factory()->count(3)->create();
    $inactive = NewsletterSubscriber::factory()->create(['unsubscribed_at' => now()]);

    $subscribers = NewsletterSubscriber::active()->get();
    $article     = \App\Models\Article::factory()->published()->create();

    foreach ($subscribers as $subscriber) {
        Mail::to($subscriber->email)->send(new NewsletterArticleMail($article, $subscriber));
    }

    Mail::assertSent(NewsletterArticleMail::class, 3);
    Mail::assertNotSent(NewsletterArticleMail::class, fn ($mail) => $mail->hasTo($inactive->email));
});

it('sends newsletter event mail only to active subscribers', function () {
    NewsletterSubscriber::factory()->count(2)->create();
    $subscribers = NewsletterSubscriber::active()->get();
    $event       = \App\Models\Event::factory()->create(['status' => 'published']);

    foreach ($subscribers as $subscriber) {
        Mail::to($subscriber->email)->send(new NewsletterEventMail($event, $subscriber));
    }

    Mail::assertSent(NewsletterEventMail::class, 2);
});
