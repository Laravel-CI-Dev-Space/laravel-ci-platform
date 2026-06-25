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
                            {--user= : Synchronise uniquement le user avec cet ID}';

    protected $description = 'Crée et active les cartes membres manquantes selon la réputation de chaque membre. Génère aussi les matricules et QR codes absents.';

    public function __construct(
        private MatriculeGenerator $matriculeGenerator,
        private QrCodeGenerator $qrCodeGenerator,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $dry = $this->option('dry-run');
        $uid = $this->option('user');

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

        $query = User::whereHas('profile')->with(['profile', 'memberCards']);
        if ($uid) {
            $query->where('id', $uid);
        }

        $matriculesCreated = 0;
        $cardsCreated      = 0;
        $qrsCreated        = 0;

        $query->chunkById(100, function ($users) use ($thresholds, $dry, &$matriculesCreated, &$cardsCreated, &$qrsCreated) {
            foreach ($users as $user) {
                // 1. Matricule manquant
                if (! $user->matricule) {
                    $this->line("  · [{$user->id}] {$user->name} — matricule manquant");
                    if (! $dry) {
                        $this->matriculeGenerator->assignIfMissing($user->refresh());
                    }
                    $matriculesCreated++;
                }

                $points = (int) ($user->profile?->points ?? 0);

                // 2. Cartes selon les seuils
                foreach ($thresholds as $level => $required) {
                    if ($points < $required) {
                        continue;
                    }

                    $existing = $user->memberCards->firstWhere('level', $level);

                    if (! $existing) {
                        $this->line("  · [{$user->id}] {$user->name} — carte niveau {$level} à créer ({$points} pts >= {$required})");
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
                        $this->line("  · [{$user->id}] {$user->name} — QR manquant sur carte niv.{$level}");
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
        $this->info("Terminé{$suffix} — Matricules : {$matriculesCreated}, Cartes : {$cardsCreated}, QR : {$qrsCreated}");

        return self::SUCCESS;
    }
}
