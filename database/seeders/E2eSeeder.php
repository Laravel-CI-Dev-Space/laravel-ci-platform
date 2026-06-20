<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\CompanyAccountStatus;
use App\Enums\UserRole;
use App\Models\Article;
use App\Models\Company;
use App\Models\CompanyAccount;
use App\Models\JobOffer;
use App\Models\Profile;
use App\Models\Question;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeds the fixed accounts and sample content used by the Playwright E2E suite.
 * Only run against the dedicated `database/e2e.sqlite` database (see .env.e2e).
 */
class E2eSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->create([
            'name'              => 'E2E Admin',
            'email'             => 'e2e-admin@laravelci.com',
            'github_id'         => '900000001',
            'github_username'   => 'e2e-admin',
            'is_active'         => true,
            'email_verified_at' => now(),
        ]);
        $admin->syncRoles([UserRole::Admin->value]);
        Profile::firstOrCreate(['user_id' => $admin->id]);

        $member = User::factory()->create([
            'name'              => 'E2E Member',
            'email'             => 'e2e-member@laravelci.com',
            'github_id'         => '900000002',
            'github_username'   => 'e2e-member',
            'is_active'         => true,
            'email_verified_at' => now(),
        ]);
        $member->syncRoles([UserRole::Member->value]);
        Profile::firstOrCreate(['user_id' => $member->id]);

        $company = Company::factory()->create(['name' => 'E2E Company']);

        CompanyAccount::create([
            'first_name'          => 'E2E',
            'last_name'           => 'Recruiter',
            'email'               => 'e2e-company@laravelci.com',
            'password'            => 'password',
            'position'            => 'RH',
            'status'              => CompanyAccountStatus::Active,
            'password_changed_at' => now(),
            'company_id'          => $company->id,
        ]);

        $forumTag = Tag::firstOrCreate(
            ['slug' => 'e2e-forum-tag'],
            ['name' => 'E2E Forum', 'scope' => 'forum']
        );
        $blogTag = Tag::firstOrCreate(
            ['slug' => 'e2e-blog-tag'],
            ['name' => 'E2E Blog', 'scope' => 'blog']
        );

        $question = Question::factory()->create([
            'user_id' => $member->id,
            'title'   => 'Comment fonctionne ce test E2E sur le forum ?',
        ]);
        $question->tags()->sync([$forumTag->id]);

        $article = Article::factory()->published()->create([
            'user_id' => $member->id,
            'title'   => 'Article de test pour la suite E2E Playwright',
        ]);
        $article->tags()->sync([$blogTag->id]);

        JobOffer::factory()->active()->create([
            'company_id' => $company->id,
            'title'      => 'Développeur Laravel — Offre E2E',
        ]);

        $this->command->info('✅ Données E2E seedées (admin, membre, entreprise, contenus).');
    }
}
