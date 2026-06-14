<?php

declare(strict_types=1);

namespace Tests;

use Database\Seeders\AboutOriginSectionSeeder;
use Database\Seeders\CommunityValueSeeder;
use Database\Seeders\GradeSeeder;
use Database\Seeders\HomeStatSeeder;
use Database\Seeders\JobOfferCategorySeeder;
use Database\Seeders\JobSkillSeeder;
use Database\Seeders\PartnerSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SiteSettingSeeder;
use Database\Seeders\TagSeeder;
use Database\Seeders\TeamMemberSeeder;
use Database\Seeders\TimelineEventSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedEssentials();
    }

    /**
     * Seed the reference data required for the application to boot
     * correctly in tests (roles/permissions, site settings, vitrine
     * config, job taxonomy, reputation grades, etc.).
     */
    protected function seedEssentials(): void
    {
        $this->seed([
            RoleSeeder::class,
            TagSeeder::class,
            JobOfferCategorySeeder::class,
            JobSkillSeeder::class,
            SiteSettingSeeder::class,
            HomeStatSeeder::class,
            PartnerSeeder::class,
            TeamMemberSeeder::class,
            CommunityValueSeeder::class,
            TimelineEventSeeder::class,
            AboutOriginSectionSeeder::class,
            GradeSeeder::class,
        ]);
    }
}
