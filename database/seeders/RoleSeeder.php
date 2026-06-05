<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Creates the platform roles.
     * Account suspension is handled via is_active and suspended_until — no inactive role needed.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions
        $permissions = [
            // Forum
            'forum.question.create',
            'forum.question.edit',
            'forum.question.delete',
            'forum.question.pin',
            'forum.answer.create',
            'forum.answer.delete',
            'forum.vote',
            'forum.report',

            // Blog
            'blog.article.create',
            'blog.article.edit',
            'blog.article.delete',
            'blog.article.publish',
            'blog.resource.upload',
            'blog.resource.download',

            // Events
            'event.register',
            'event.cancel',
            'event.create',
            'event.manage',

            // Jobs
            'job.apply',
            'job.favorite',
            'job.offer.create',
            'job.offer.manage',

            // Moderation
            'moderation.report.handle',
            'moderation.content.hide',
            'moderation.user.suspend',

            // Admin
            'admin.access',
            'admin.user.manage',
            'admin.user.ban',
            'admin.settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Roles — names must match exactly what the app uses in hasRole() / middleware
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin',  'guard_name' => 'web']);
        $admin      = Role::firstOrCreate(['name' => 'admin',        'guard_name' => 'web']);
        $moderator  = Role::firstOrCreate(['name' => 'moderator',   'guard_name' => 'web']);
        $member     = Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);

        // Super admin — all permissions
        $superAdmin->givePermissionTo(Permission::all());

        // Admin — all except settings
        $admin->givePermissionTo(
            Permission::whereNotIn('name', ['admin.settings'])->get()
        );

        // Moderator
        $moderator->givePermissionTo([
            'forum.question.pin',
            'forum.question.delete',
            'forum.answer.delete',
            'moderation.report.handle',
            'moderation.content.hide',
            'moderation.user.suspend',
            'admin.access',
        ]);

        // Member
        $member->givePermissionTo([
            'forum.question.create',
            'forum.question.edit',
            'forum.answer.create',
            'forum.vote',
            'forum.report',
            'blog.article.create',
            'blog.article.edit',
            'blog.resource.upload',
            'blog.resource.download',
            'event.register',
            'event.cancel',
            'job.apply',
            'job.favorite',
            'job.offer.create',
        ]);

        $this->command->info('✅ Roles and permissions seeded.');
    }
}
