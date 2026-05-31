<?php

declare(strict_types=1);

use App\Actions\Forum\CreateQuestionAction;
use App\Livewire\Dashboard\Forum\Question\CreateDrawer;
use App\Livewire\Dashboard\Forum\Question\Index;
use App\Models\Question;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
});

// ─── CreateQuestionAction ─────────────────────────────────────────────────────

describe('CreateQuestionAction', function (): void {
    it('crée une question avec des données valides', function (): void {
        $user = User::factory()->create();
        $user->assignRole('membre-actif');

        $question = (new CreateQuestionAction)->execute($user, [
            'title'   => 'Comment utiliser Eloquent avec PostgreSQL ?',
            'content' => str_repeat('Contenu de test. ', 5),
        ]);

        expect($question)
            ->toBeInstanceOf(Question::class)
            ->and($question->user_id)->toBe($user->id)
            ->and($question->slug)->not->toBeEmpty()
            ->and($question->pinned)->toBeFalse()
            ->and($question->closed)->toBeFalse();
    });

    it('génère des slugs uniques pour des titres identiques', function (): void {
        $user = User::factory()->create();
        $user->assignRole('membre-actif');

        $action = new CreateQuestionAction;
        $title  = 'Comment configurer Laravel Scout ?';

        $q1 = $action->execute($user, ['title' => $title, 'content' => str_repeat('a', 50)]);
        $q2 = $action->execute($user, ['title' => $title, 'content' => str_repeat('b', 50)]);

        expect($q1->slug)->not->toBe($q2->slug);
    });

    it('refuse la création pour un utilisateur inactif', function (): void {
        $user = User::factory()->create(['is_active' => false]);
        $user->assignRole('membre-actif');

        expect(fn () => (new CreateQuestionAction)->execute($user, [
            'title'   => 'Titre suffisamment long pour valider',
            'content' => str_repeat('Contenu. ', 5),
        ]))->toThrow(AuthorizationException::class);
    });
})->group('action');

// ─── CreateDrawer ─────────────────────────────────────────────────────────────

describe('CreateDrawer', function (): void {
    it('s\'ouvre via l\'événement open-create-drawer', function (): void {
        $user = User::factory()->create();
        $user->assignRole('membre-actif');

        Livewire::actingAs($user)
            ->test(CreateDrawer::class)
            ->assertSet('open', false)
            ->dispatch('open-create-drawer')
            ->assertSet('open', true);
    });

    it('se ferme et réinitialise le formulaire', function (): void {
        $user = User::factory()->create();
        $user->assignRole('membre-actif');

        Livewire::actingAs($user)
            ->test(CreateDrawer::class)
            ->set('title', 'Un titre quelconque')
            ->set('content', 'Un contenu quelconque')
            ->call('closeDrawer')
            ->assertSet('open', false)
            ->assertSet('title', '')
            ->assertSet('content', '');
    });

    it('crée la question et dispatch question-created', function (): void {
        $user = User::factory()->create();
        $user->assignRole('membre-actif');

        Livewire::actingAs($user)
            ->test(CreateDrawer::class)
            ->set('title', 'Comment configurer Meilisearch avec Laravel Scout ?')
            ->set('content', str_repeat('Détail du problème. ', 5))
            ->call('save')
            ->assertSet('open', false)
            ->assertDispatched('question-created');

        expect(Question::where('user_id', $user->id)->exists())->toBeTrue();
    });

    it('échoue si le titre est absent', function (): void {
        $user = User::factory()->create();
        $user->assignRole('membre-actif');

        Livewire::actingAs($user)
            ->test(CreateDrawer::class)
            ->set('title', '')
            ->set('content', str_repeat('a', 50))
            ->call('save')
            ->assertHasErrors(['title' => 'required']);
    });

    it('échoue si le titre est trop court (< 10 chars)', function (): void {
        $user = User::factory()->create();
        $user->assignRole('membre-actif');

        Livewire::actingAs($user)
            ->test(CreateDrawer::class)
            ->set('title', 'Court')
            ->set('content', str_repeat('a', 50))
            ->call('save')
            ->assertHasErrors(['title' => 'min']);
    });

    it('échoue si le contenu est trop court (< 30 chars)', function (): void {
        $user = User::factory()->create();
        $user->assignRole('membre-actif');

        Livewire::actingAs($user)
            ->test(CreateDrawer::class)
            ->set('title', 'Un titre suffisamment long pour valider')
            ->set('content', 'Trop court')
            ->call('save')
            ->assertHasErrors(['content' => 'min']);
    });
})->group('livewire');

// ─── Index ────────────────────────────────────────────────────────────────────

describe('Index', function (): void {
    it('affiche la liste des questions', function (): void {
        $user     = User::factory()->create();
        $question = Question::factory()->forUser($user)->create();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->assertSee($question->title);
    });

    it('filtre les questions par recherche', function (): void {
        $user = User::factory()->create();
        Question::factory()->forUser($user)->create(['title' => 'Eloquent avec PostgreSQL en production']);
        Question::factory()->forUser($user)->create(['title' => 'Authentification via GitHub Socialite']);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('search', 'Eloquent')
            ->assertSee('Eloquent avec PostgreSQL en production')
            ->assertDontSee('Authentification via GitHub Socialite');
    });

    it('affiche les questions épinglées en premier', function (): void {
        $user    = User::factory()->create();
        $regular = Question::factory()->forUser($user)->create([
            'pinned'     => false,
            'created_at' => now(),
        ]);
        $pinned = Question::factory()->forUser($user)->create([
            'pinned'     => true,
            'created_at' => now()->subDay(),
        ]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->set('sort', 'recent')
            ->assertSeeInOrder([$pinned->title, $regular->title]);
    });

    it('dispatch open-create-drawer au clic sur le bouton', function (): void {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('openCreateDrawer')
            ->assertDispatched('open-create-drawer');
    });

    it('rafraîchit la liste quand question-created est dispatché', function (): void {
        $user = User::factory()->create();

        $component = Livewire::actingAs($user)->test(Index::class);

        Question::factory()->forUser($user)->create(['title' => 'Nouvelle question après dispatch']);

        $component
            ->dispatch('question-created', id: 1)
            ->assertSee('Nouvelle question après dispatch');
    });
})->group('livewire');
