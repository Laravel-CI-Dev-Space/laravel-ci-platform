<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\MemberCard;
use App\Models\Profile;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\MatriculeGenerator;
use App\Services\QrCodeGenerator;
use Database\Seeders\GradeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

// ─── MatriculeGenerator ────────────────────────────────────────────────────────

describe('MatriculeGenerator', function () {
    it('génère un matricule au bon format', function () {
        $generator = app(MatriculeGenerator::class);
        $user      = User::factory()->create(['created_at' => '2026-06-25 10:00:00']);

        $matricule = $generator->generate($user);

        expect($matricule)->toMatch('/^LARAVELCI-\d{2}-\d{2}-\d{4}-\d{4}$/');
        expect($matricule)->toContain('25-06-2026');
    });

    it('le UserObserver assigne automatiquement le matricule à la création', function () {
        $user = User::factory()->create();
        $user->refresh();

        expect($user->matricule)->not->toBeNull();
        expect($user->matricule)->toStartWith('LARAVELCI-');
    });

    it('ne réassigne pas si le matricule existe déjà', function () {
        $generator = app(MatriculeGenerator::class);
        $user      = User::factory()->create();
        $user->refresh();
        $existing  = $user->matricule;

        $returned = $generator->assignIfMissing($user);

        expect($returned)->toBe($existing);
    });

    it('garantit l\'unicité des matricules sur 5 users', function () {
        $users = User::factory()->count(5)->create();

        $matricules = $users->map(fn ($u) => $u->refresh()->matricule);

        expect($matricules->filter()->unique()->count())->toBe(5);
    });
});

// ─── QrCodeGenerator ──────────────────────────────────────────────────────────

describe('QrCodeGenerator', function () {
    it('génère un SVG valide', function () {
        $generator = app(QrCodeGenerator::class);
        $user      = User::factory()->create(['github_username' => 'test-user']);

        $svg = $generator->forMember($user->github_username);

        expect($svg)->toContain('<svg');
        expect($svg)->toContain('</svg>');
    });

    it('encode l\'URL du profil public dans le QR (SVG non vide)', function () {
        $generator = app(QrCodeGenerator::class);
        $user      = User::factory()->create(['github_username' => 'ky-wilson']);

        $svg = $generator->forMember($user->github_username);

        expect(strlen($svg))->toBeGreaterThan(500);
    });
});

// ─── Auto-unlock via ProfileObserver ──────────────────────────────────────────

describe('Auto-unlock des cartes', function () {
    beforeEach(function () {
        $this->seed(GradeSeeder::class);

        foreach ([
            'card_level_1_points' => '300',
            'card_level_2_points' => '600',
            'card_level_3_points' => '900',
        ] as $key => $value) {
            SiteSetting::updateOrCreate(['key' => $key], [
                'group' => 'member_cards', 'type' => 'number',
                'label' => $key, 'value' => $value, 'order' => 1,
            ]);
        }
    });

    it('crée une carte niveau 1 quand le membre atteint 300 pts', function () {
        $user    = User::factory()->create();
        $profile = Profile::create(['user_id' => $user->id, 'points' => 100]);

        expect($user->memberCards()->count())->toBe(0);

        $profile->update(['points' => 350]);

        expect($user->memberCards()->where('level', 1)->where('is_active', true)->count())->toBe(1);
    });

    it('crée les 3 cartes quand le membre atteint 900 pts', function () {
        $user    = User::factory()->create();
        $profile = Profile::create(['user_id' => $user->id, 'points' => 100]);

        $profile->update(['points' => 950]);

        expect($user->memberCards()->active()->count())->toBe(3);
    });

    it('ne recrée pas une carte déjà existante', function () {
        $user    = User::factory()->create();
        $profile = Profile::create(['user_id' => $user->id, 'points' => 400]);

        $profile->update(['points' => 450]);

        expect($user->memberCards()->where('level', 1)->count())->toBe(1);
    });
});

// ─── Activation admin (override) ──────────────────────────────────────────────

describe('Activation admin', function () {
    it('force l\'activation d\'une carte sans atteindre le seuil', function () {
        $admin = User::factory()->create();
        $user  = User::factory()->create();

        $card = MemberCard::create([
            'user_id'   => $user->id,
            'level'     => 1,
            'is_active' => false,
        ]);

        $card->activate($admin->id, forced: true);
        $card->refresh();

        expect($card->is_active)->toBeTrue();
        expect($card->forced_by_admin)->toBeTrue();
        expect($card->activated_by)->toBe($admin->id);
    });

    it('désactive une carte active', function () {
        $user = User::factory()->create();
        $card = MemberCard::create(['user_id' => $user->id, 'level' => 1, 'is_active' => true]);

        $card->deactivate();
        $card->refresh();

        expect($card->is_active)->toBeFalse();
    });
});

// ─── MemberCard — templateView scalable ───────────────────────────────────────

describe('MemberCard templateView', function () {
    it('retourne la bonne vue selon le niveau', function () {
        $user = User::factory()->create();

        foreach ([1, 2, 3] as $level) {
            $card = MemberCard::make(['user_id' => $user->id, 'level' => $level]);
            expect($card->templateView())->toBe("member-card.templates.level-{$level}");
        }
    });

    it('retourne le bon nom de niveau', function () {
        $user  = User::factory()->create();
        $names = config('member-card.level_names');

        foreach ([1, 2, 3] as $level) {
            $card = MemberCard::make(['user_id' => $user->id, 'level' => $level]);
            expect($card->levelName())->toBe($names[$level]);
        }
    });
});
