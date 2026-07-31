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
            ['name' => 'Tech & Formation',  'slug' => 'tech-formation',  'position' => 1],
            ['name' => 'Communication',     'slug' => 'communication',   'position' => 2],
            ['name' => 'Événements',        'slug' => 'evenements',      'position' => 3],
            ['name' => 'Partenariat',       'slug' => 'partenariat',     'position' => 4],
            ['name' => 'Employabilité',     'slug' => 'employabilite',   'position' => 5],
        ];

        foreach ($poleNames as $data) {
            Pole::create(array_merge($data, ['is_active' => true]));
        }

        $p = fn (string $name) => Pole::where('name', $name)->value('id');

        // ── Membres : 1 personne = 1 pôle · 3 max par pôle · 1 responsable ──
        //
        // Répartition basée sur les candidatures (pôle principal + profil) :
        //
        // Tech & Formation  → Franck Soro (senior 3-5ans)  | Pierre Ahoble (senior 5ans+) | Aboubacar Diarra (Laravel+Flutter)
        // Communication     → Antoine Amon (background comm) | Samassi Adama (senior)      | Abolé Ange N'Doman
        // Événements        → Grâce Kouacou (senior)        | Dally Kouadio Salomon        | Bahi Kipre
        // Partenariat       → Franck Boris Koffi (réseau intl) | Lacina Levi Ouattara
        // Employabilité     → Lacina Traore (backend/API)   | Yacouba Diarrassouba         | Osim Ephraïm David
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

            // ── Événements (3/3) ──────────────────────────────────────────
            [
                'pole_id'    => $p('Événements'),
                'first_name' => 'Grâce',
                'last_name'  => 'Kouacou',
                'email'      => 'kocograce455@gmail.com',
                'poste'      => 'Développeur senior',
                'role'       => 'responsable',
                'order'      => 1,
            ],
            [
                'pole_id'    => $p('Événements'),
                'first_name' => 'Dally',
                'last_name'  => 'Kouadio Salomon',
                'email'      => 'salomondylan1803@gmail.com',
                'poste'      => 'Développeur junior',
                'role'       => 'adjoint',
                'order'      => 2,
            ],
            [
                'pole_id'    => $p('Événements'),
                'first_name' => 'Bahi',
                'last_name'  => 'Kipre',
                'email'      => 'bahikiprewilfriedezechiel@gmail.com',
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

            // ── Employabilité (3/3) ───────────────────────────────────────
            [
                'pole_id'    => $p('Employabilité'),
                'first_name' => 'Lacina',
                'last_name'  => 'Traore',
                'email'      => 'traorelac01@gmail.com',
                'poste'      => 'Développeur backend',
                'role'       => 'responsable',
                'order'      => 1,
            ],
            [
                'pole_id'    => $p('Employabilité'),
                'first_name' => 'Yacouba',
                'last_name'  => 'Diarrassouba',
                'email'      => 'diarrassoubay252@gmail.com',
                'poste'      => 'Développeur junior',
                'role'       => 'adjoint',
                'order'      => 2,
            ],
            [
                'pole_id'    => $p('Employabilité'),
                'first_name' => 'Osim Ephraïm',
                'last_name'  => 'David',
                'email'      => 'mrephraim2.0@gmail.com',
                'poste'      => 'Développeur junior',
                'role'       => 'membre',
                'order'      => 3,
            ],
        ];

        foreach ($members as $data) {
            PoleMember::create(array_merge($data, ['status' => 'actif']));
        }
    }
}
