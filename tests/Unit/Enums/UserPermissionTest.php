<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use Tests\TestCase;

uses(TestCase::class);

it('gives super-admin all permissions', function () {
    $permissions = UserPermission::forRole(UserRole::SuperAdmin);

    expect($permissions)->toEqual(UserPermission::values());
});

it('gives pole-communication blog publish and admin access', function () {
    $permissions = UserPermission::forRole(UserRole::PoleCommunication);

    expect($permissions)->toContain('blog.article.publish');
    expect($permissions)->toContain('blog.article.unpublish');
    expect($permissions)->toContain('admin.access');
});

it('gives pole-evenements event management and admin access', function () {
    $permissions = UserPermission::forRole(UserRole::PoleEvenements);

    expect($permissions)->toContain('event.create');
    expect($permissions)->toContain('event.manage');
    expect($permissions)->toContain('admin.access');
    expect($permissions)->not->toContain('blog.article.publish');
});

it('gives pole-tech-formation forum moderation and admin access', function () {
    $permissions = UserPermission::forRole(UserRole::PoleTechFormation);

    expect($permissions)->toContain('forum.question.pin');
    expect($permissions)->toContain('moderation.report.handle');
    expect($permissions)->toContain('moderation.content.hide');
    expect($permissions)->toContain('admin.access');
    expect($permissions)->not->toContain('event.manage');
});

it('gives pole-employabilite job offer management and admin access', function () {
    $permissions = UserPermission::forRole(UserRole::PoleEmployabilite);

    expect($permissions)->toContain('job.offer.manage');
    expect($permissions)->toContain('job.offer.publish');
    expect($permissions)->toContain('admin.access');
    expect($permissions)->not->toContain('blog.article.publish');
});

it('gives pole-partenariat only company view, export and admin access', function () {
    $permissions = UserPermission::forRole(UserRole::PolePartenariat);

    expect($permissions)->toContain('company.view');
    expect($permissions)->toContain('company.export');
    expect($permissions)->toContain('admin.access');
    expect($permissions)->not->toContain('event.manage');
    expect($permissions)->not->toContain('blog.article.publish');
    expect($permissions)->not->toContain('job.offer.publish');
    expect($permissions)->toHaveCount(3);
});

it('does not grant any pole role admin settings or user ban', function () {
    foreach (UserRole::poleRoles() as $role) {
        $permissions = UserPermission::forRole($role);

        expect($permissions)->not->toContain('admin.settings', "Role {$role->value} should not have admin.settings");
        expect($permissions)->not->toContain('admin.user.ban', "Role {$role->value} should not have admin.user.ban");
    }
});
