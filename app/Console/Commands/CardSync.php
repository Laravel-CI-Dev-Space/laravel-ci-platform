<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\MemberCard;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\MatriculeGenerator;
use App\Services\QrCodeGenerator;
use Illuminate\Console\Command;

class CardSync extends Command
{
    protected $signature = 'cards:sync
                            {--dry-run : Affiche ce qui serait fait sans modifier la base}
                            {--user= : Synchronise uniquement le user avec cet ID}
                            {--force : Crée une carte niveau 1 pour tous les membres actifs sans carte, sans condition de points}';

    protected $description = 'Crée et active les cartes membres manquantes selon la réputation de chaque membre. Génère aussi les matricules et QR codes absents.';

    public function __construct(
        private MatriculeGenerator $matriculeGenerator,
        private QrCodeGenerator $qrCodeGenerator,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $dry   = $this->option('dry-run');
        $uid   = $this->option('user');
        $force = $this->option('force');

        $thresholds = [
            1 => (int) (SiteSetting::firstWhere('key', 'card_level_1_points')?->value ?? 300),
            2 => (int) (SiteSetting::firstWhere('key', 'card_level_2_points')?->value ?? 600),
            3 => (int) (SiteSetting::firstWhere('key', 'card_level_3_points')?->value ?? 900),
        ];

        $this->info('Seuils actifs : ' . implode(', ', array_map(
            fn ($l, $pts) => "Niv.$l = {$pts} pts",
            array_keys($thresholds),
            $thresholds,
        )));

        $matriculesCreated = 0;
        $cardsCreated      = 0;
        $qrsCreated        = 0;

        // 1. Matricules : tous les membres, profil ou non
        $matriculeQuery = User::query();
        if ($uid) {
            $matriculeQuery->where('id', $uid);
        }
        $matriculeQuery->chunkById(100, function ($users) use ($dry, &$matriculesCreated) {
            foreach ($users as $user) {
                if (! $user->matricule) {
                    $this->line("  · [{$user->id}] {$user->name} - matricule manquant");
                    if (! $dry) {
                        $this->matriculeGenerator->assignIfMissing($user->refresh());
                    }
                    $matriculesCreated++;
                }
            }
        });

        // 2a. Mode --force : carte N1 pour tous les membres actifs sans aucune carte
        if ($force) {
            $this->info('Mode --force : attribution d\'une carte niveau 1 à tous les membres sans carte.');
            $forceQuery = User::with(['memberCards'])->where('is_active', true);
            if ($uid) {
                $forceQuery->where('id', $uid);
            }
            $forceQuery->chunkById(100, function ($users) use ($dry, &$cardsCreated, &$qrsCreated) {
                foreach ($users as $user) {
                    if ($user->memberCards->isNotEmpty()) {
                        continue;
                    }
                    $this->line("  · [{$user->id}] {$user->name} - carte niveau 1 forcée");
                    if (! $dry) {
                        $card = MemberCard::create([
                            'user_id'          => $user->id,
                            'level'            => 1,
                            'is_active'        => true,
                            'forced_by_admin'  => true,
                            'activated_at'     => now(),
                        ]);
                        if (! $card->qr_code_svg) {
                            $card->update(['qr_code_svg' => $this->qrCodeGenerator->forMember($user->github_username)]);
                            $qrsCreated++;
                        }
                    }
                    $cardsCreated++;
                }
            });
        }

        // 2b. Cartes selon réputation : membres avec profil uniquement
        $query = User::whereHas('profile')->with(['profile', 'memberCards']);
        if ($uid) {
            $query->where('id', $uid);
        }

        $query->chunkById(100, function ($users) use ($thresholds, $dry, &$cardsCreated, &$qrsCreated) {
            foreach ($users as $user) {
                $points = (int) ($user->profile?->points ?? 0);

                foreach ($thresholds as $level => $required) {
                    if ($points < $required) {
                        continue;
                    }

                    $existing = $user->memberCards->firstWhere('level', $level);

                    if (! $existing) {
                        $this->line("  · [{$user->id}] {$user->name} - carte niveau {$level} à créer ({$points} pts >= {$required})");
                        if (! $dry) {
                            $card = MemberCard::create([
                                'user_id'   => $user->id,
                                'level'     => $level,
                                'is_active' => true,
                                'activated_at' => now(),
                            ]);
                            // QR via observer mais on force ici aussi si absent
                            if (! $card->qr_code_svg) {
                                $card->update(['qr_code_svg' => $this->qrCodeGenerator->forMember($user->github_username)]);
                                $qrsCreated++;
                            }
                        }
                        $cardsCreated++;
                    } elseif ($existing && ! $existing->qr_code_svg) {
                        // QR manquant sur une carte existante
                        $this->line("  · [{$user->id}] {$user->name} - QR manquant sur carte niv.{$level}");
                        if (! $dry) {
                            $existing->update(['qr_code_svg' => $this->qrCodeGenerator->forMember($user->github_username)]);
                        }
                        $qrsCreated++;
                    }
                }
            }
        });

        $this->newLine();
        $suffix = $dry ? ' (dry-run)' : '';
        $this->info("Terminé{$suffix} - Matricules : {$matriculesCreated}, Cartes : {$cardsCreated}, QR : {$qrsCreated}");

        return self::SUCCESS;
    }
}
