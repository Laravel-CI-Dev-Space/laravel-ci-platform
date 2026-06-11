<?php

namespace Database\Seeders;

use App\Models\VitrineSetting;
use Illuminate\Database\Seeder;

class VitrineSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [

            // ─── HOME › HERO ─────────────────────────────────────────────
            ['code' => 'home_hero_badge',       'type' => 'text',     'group' => 'home', 'section' => 'hero', 'value' => 'Laravel 11 · PHP 8.3 · Open source'],
            ['code' => 'home_hero_title',        'type' => 'text',     'group' => 'home', 'section' => 'hero', 'value' => "The Laravel Community of Côte d'Ivoire"],
            ['code' => 'home_hero_lead',         'type' => 'textarea', 'group' => 'home', 'section' => 'hero', 'value' => 'Join 500+ developers — share, learn, and grow together. The first structured home for Ivorian Laravel & PHP builders, in Abidjan and across the diaspora.'],
            ['code' => 'home_hero_cta_primary',  'type' => 'text',     'group' => 'home', 'section' => 'hero', 'value' => 'Join the Community'],
            ['code' => 'home_hero_cta_secondary', 'type' => 'text',     'group' => 'home', 'section' => 'hero', 'value' => 'Explore the Forum'],

            // ─── HOME › STATS ─────────────────────────────────────────────
            ['code' => 'home_stats_members',   'type' => 'number', 'group' => 'home', 'section' => 'stats', 'value' => '500'],
            ['code' => 'home_stats_questions', 'type' => 'number', 'group' => 'home', 'section' => 'stats', 'value' => '1200'],
            ['code' => 'home_stats_events',    'type' => 'number', 'group' => 'home', 'section' => 'stats', 'value' => '24'],
            ['code' => 'home_stats_articles',  'type' => 'number', 'group' => 'home', 'section' => 'stats', 'value' => '80'],

            // ─── HOME › CTA ───────────────────────────────────────────────
            ['code' => 'home_cta_title',  'type' => 'text',     'group' => 'home', 'section' => 'cta', 'value' => 'Ready to build the future of Ivorian tech?'],
            ['code' => 'home_cta_lead',   'type' => 'textarea', 'group' => 'home', 'section' => 'cta', 'value' => 'Sign in with GitHub, ask your first question, and meet 500+ developers who have your back.'],
            ['code' => 'home_cta_button', 'type' => 'text',     'group' => 'home', 'section' => 'cta', 'value' => 'Join the Community'],

            // ─── ABOUT › HERO ─────────────────────────────────────────────
            ['code' => 'about_hero_badge', 'type' => 'text',     'group' => 'about', 'section' => 'hero', 'value' => 'Founded 2026 · Open source'],
            ['code' => 'about_hero_title', 'type' => 'text',     'group' => 'about', 'section' => 'hero', 'value' => "We're building the home for Ivorian Laravel developers"],
            ['code' => 'about_hero_lead',  'type' => 'textarea', 'group' => 'about', 'section' => 'hero', 'value' => "Laravel Côte d'Ivoire is the first structured developer community dedicated to Laravel & PHP in Côte d'Ivoire and the diaspora — built on knowledge sharing, inclusion, and collective growth."],
            ['code' => 'about_hero_cta',   'type' => 'text',     'group' => 'about', 'section' => 'hero', 'value' => 'Join us'],

            // ─── ABOUT › MISSION ──────────────────────────────────────────
            ['code' => 'about_mission_title', 'type' => 'text',     'group' => 'about', 'section' => 'mission', 'value' => 'Our mission'],
            ['code' => 'about_mission_body',  'type' => 'textarea', 'group' => 'about', 'section' => 'mission', 'value' => 'Give every Ivorian developer a structured place to learn Laravel properly, ask questions without fear, find meaningful work, and grow alongside peers who understand their context.'],
            ['code' => 'about_vision_title',  'type' => 'text',     'group' => 'about', 'section' => 'mission', 'value' => 'Our vision'],
            ['code' => 'about_vision_body',   'type' => 'textarea', 'group' => 'about', 'section' => 'mission', 'value' => "A West Africa where world-class software is built by local talent — and where Côte d'Ivoire is recognized as a hub of Laravel and PHP excellence on the global stage."],

            // ─── ABOUT › VALUES ───────────────────────────────────────────
            ['code' => 'about_values_items', 'type' => 'json', 'group' => 'about', 'section' => 'values', 'value' => json_encode([
                ['icon' => 'fa-solid fa-award',                  'title' => 'Excellence', 'body' => 'We hold each other to a high bar — clean code, real craft.'],
                ['icon' => 'fa-solid fa-hands-holding-circle',   'title' => 'Sharing',    'body' => 'Knowledge grows when given away. No gatekeeping here.'],
                ['icon' => 'fa-solid fa-people-group',           'title' => 'Inclusion',  'body' => 'Every level, every background, every question is welcome.'],
                ['icon' => 'fa-solid fa-seedling',               'title' => 'Impact',     'body' => "We build tech that improves life in Côte d'Ivoire."],
            ])],

            // ─── ABOUT › TEAM ─────────────────────────────────────────────
            ['code' => 'about_team_members', 'type' => 'json', 'group' => 'about', 'section' => 'team', 'value' => json_encode([
                ['initials' => 'SB', 'name' => 'Serge Brou',    'role' => 'Founder & Architect',     'avatar_class' => 'av-1', 'github' => '#'],
                ['initials' => 'FD', 'name' => 'Fatou Diallo',  'role' => 'Community Lead',           'avatar_class' => 'av-3', 'github' => '#'],
                ['initials' => 'AD', 'name' => 'Aïcha Doumbia', 'role' => 'Content & Events',         'avatar_class' => 'av-5', 'github' => '#'],
                ['initials' => 'YT', 'name' => 'Yao Térence',   'role' => 'Open Source Maintainer',   'avatar_class' => 'av-4', 'github' => '#'],
            ])],

            // ─── ABOUT › CTA ──────────────────────────────────────────────
            ['code' => 'about_cta_title',  'type' => 'text',     'group' => 'about', 'section' => 'cta', 'value' => 'Your seat at the table is ready'],
            ['code' => 'about_cta_lead',   'type' => 'textarea', 'group' => 'about', 'section' => 'cta', 'value' => 'Join 500+ Ivorian developers building the future of African tech, one commit at a time.'],
            ['code' => 'about_cta_button', 'type' => 'text',     'group' => 'about', 'section' => 'cta', 'value' => 'Join the Community'],

            // ─── GLOBAL › SEO ─────────────────────────────────────────────
            ['code' => 'global_default_title',       'type' => 'text',     'group' => 'global', 'section' => 'seo',    'value' => "Laravel CI — The Laravel Community of Côte d'Ivoire"],
            ['code' => 'global_default_description', 'type' => 'textarea', 'group' => 'global', 'section' => 'seo',    'value' => 'Join 500+ Ivorian Laravel & PHP developers. Share knowledge, find jobs, attend events, and grow together in Abidjan and across the diaspora.'],
            ['code' => 'global_og_image',            'type' => 'text',     'group' => 'global', 'section' => 'seo',    'value' => 'assets/web/img/logo.png'],

            // ─── GLOBAL › SOCIAL ──────────────────────────────────────────
            ['code' => 'global_social_links', 'type' => 'json', 'group' => 'global', 'section' => 'social', 'value' => json_encode([
                ['platform' => 'github', 'url' => 'https://github.com/Laravel-CI-Dev-Space/laravel-ci'],
            ])],

        ];

        foreach ($settings as $setting) {
            VitrineSetting::updateOrCreate(
                ['code' => $setting['code']],
                $setting
            );
        }
    }
}
