<?php

namespace Database\Seeders;

use App\Models\Pole;
use App\Models\PoleMember;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pole_members')->delete();
        DB::table('poles')->delete();

        // ── Pôles ────────────────────────────────────────────────────────
        $poleNames = [
            ['name' => 'Communication',    'slug' => 'communication',  'position' => 1],
            ['name' => 'Partenariat',      'slug' => 'partenariat',    'position' => 2],
            ['name' => 'Tech & Formation', 'slug' => 'tech-formation', 'position' => 3],
        ];

        foreach ($poleNames as $data) {
            Pole::create(array_merge($data, ['is_active' => true]));
        }

        $p = fn (string $name) => Pole::where('name', $name)->value('id');

        // ── Membres : 1 personne = 1 pôle · 3 max par pôle · 1 responsable ──
        //
        // Tech & Formation  → Franck Soro (senior 3-5ans)  | Pierre Ahoble (senior 5ans+) | Aboubacar Diarra (Laravel+Flutter)
        // Communication     → Antoine Amon (background comm) | Samassi Adama (senior)      | Abolé Ange N'Doman
        // Partenariat       → Franck Boris Koffi (réseau intl) | Lacina Levi Ouattara
        //
        $members = [
            // ── Tech & Formation (3/3) ────────────────────────────────────
            [
                'pole_id'    => $p('Tech & Formation'),
                'first_name' => 'Franck',
                'last_name'  => 'Soro',
                'email'      => 'kigninnama@gmail.com',
                'poste'      => 'Développeur senior',
                'role'       => 'responsable',
                'order'      => 1,
            ],
            [
                'pole_id'    => $p('Tech & Formation'),
                'first_name' => 'Pierre',
                'last_name'  => 'Ahoble',
                'email'      => 'pierreahoble.dev@gmail.com',
                'poste'      => 'Développeur senior',
                'role'       => 'adjoint',
                'order'      => 2,
            ],
            [
                'pole_id'    => $p('Tech & Formation'),
                'first_name' => 'Aboubacar',
                'last_name'  => 'Diarra',
                'email'      => 'diarraaboubacar030@gmail.com',
                'poste'      => 'Développeur web & mobile',
                'role'       => 'membre',
                'order'      => 3,
            ],

            // ── Communication (3/3) ───────────────────────────────────────
            [
                'pole_id'    => $p('Communication'),
                'first_name' => 'Antoine Alexandre',
                'last_name'  => 'Amon Oi Amon',
                'email'      => 'amonoiamon@gmail.com',
                'poste'      => 'Communication & Design',
                'role'       => 'responsable',
                'order'      => 1,
            ],
            [
                'pole_id'    => $p('Communication'),
                'first_name' => 'Samassi',
                'last_name'  => 'Adama',
                'email'      => 'asamassiadama@gmail.com',
                'poste'      => 'Développeur senior',
                'role'       => 'adjoint',
                'order'      => 2,
            ],
            [
                'pole_id'    => $p('Communication'),
                'first_name' => 'Abolé Ange Emmanuel Daniel',
                'last_name'  => "N'Doman",
                'email'      => 'emmanuelange963@gmail.com',
                'poste'      => 'Développeur junior',
                'role'       => 'membre',
                'order'      => 3,
            ],

            // ── Partenariat (2/3) ─────────────────────────────────────────
            [
                'pole_id'    => $p('Partenariat'),
                'first_name' => 'Franck Boris',
                'last_name'  => 'Koffi',
                'email'      => 'franck.koffi@epitech',
                'poste'      => 'Développeur & Partenariats',
                'role'       => 'responsable',
                'order'      => 1,
            ],
            [
                'pole_id'    => $p('Partenariat'),
                'first_name' => 'Lacina Levi',
                'last_name'  => 'Ouattara',
                'email'      => 'ouattaralevi365@gmail.com',
                'poste'      => 'Étudiant',
                'role'       => 'adjoint',
                'order'      => 2,
            ],

        ];

        foreach ($members as $data) {
            PoleMember::create(array_merge($data, ['status' => 'actif']));
        }

    }
}
