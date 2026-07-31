<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Filament\Pole\Resources\ArticleResource;
use App\Filament\Pole\Resources\CompanyAccountResource;
use App\Filament\Pole\Resources\EventResource;
use App\Filament\Pole\Resources\JobOfferResource;
use App\Filament\Pole\Resources\PoleResourceResource;
use App\Filament\Pole\Resources\QuestionResource;
use App\Models\User;

// ─── Helper ──────────────────────────────────────────────────────────────────

function loginAs(UserRole $role): void
{
    $user = User::factory()->create();
    $user->assignRole($role->value);
    test()->actingAs($user);
}

// ─── ArticleResource (Pôle Communication) ────────────────────────────────────

it('lets pole-communication access ArticleResource', function () {
    loginAs(UserRole::PoleCommunication);

    expect(ArticleResource::canViewAny())->toBeTrue();
});

it('blocks every other pole role from ArticleResource', function (UserRole $role) {
    loginAs($role);

    expect(ArticleResource::canViewAny())->toBeFalse();
})->with([
    'pole-evenements'      => UserRole::PoleEvenements,
    'pole-tech-formation'  => UserRole::PoleTechFormation,
    'pole-employabilite'   => UserRole::PoleEmployabilite,
    'pole-partenariat'     => UserRole::PolePartenariat,
]);

// ─── EventResource (Pôle Événements) ─────────────────────────────────────────

it('lets pole-evenements access EventResource', function () {
    loginAs(UserRole::PoleEvenements);

    expect(EventResource::canViewAny())->toBeTrue();
});

it('blocks every other pole role from EventResource', function (UserRole $role) {
    loginAs($role);

    expect(EventResource::canViewAny())->toBeFalse();
})->with([
    'pole-communication'   => UserRole::PoleCommunication,
    'pole-tech-formation'  => UserRole::PoleTechFormation,
    'pole-employabilite'   => UserRole::PoleEmployabilite,
    'pole-partenariat'     => UserRole::PolePartenariat,
]);

// ─── QuestionResource (Pôle Tech & Formation) ────────────────────────────────

it('lets pole-tech-formation access QuestionResource', function () {
    loginAs(UserRole::PoleTechFormation);

    expect(QuestionResource::canViewAny())->toBeTrue();
});

it('blocks every other pole role from QuestionResource', function (UserRole $role) {
    loginAs($role);

    expect(QuestionResource::canViewAny())->toBeFalse();
})->with([
    'pole-communication'  => UserRole::PoleCommunication,
    'pole-evenements'     => UserRole::PoleEvenements,
    'pole-employabilite'  => UserRole::PoleEmployabilite,
    'pole-partenariat'    => UserRole::PolePartenariat,
]);

it('prevents creating forum questions via QuestionResource regardless of role', function () {
    loginAs(UserRole::PoleTechFormation);

    expect(QuestionResource::canCreate())->toBeFalse();
});

// ─── PoleResourceResource (Pôle Tech & Formation) ────────────────────────────

it('lets pole-tech-formation access PoleResourceResource', function () {
    loginAs(UserRole::PoleTechFormation);

    expect(PoleResourceResource::canViewAny())->toBeTrue();
});

it('blocks every other pole role from PoleResourceResource', function (UserRole $role) {
    loginAs($role);

    expect(PoleResourceResource::canViewAny())->toBeFalse();
})->with([
    'pole-communication'  => UserRole::PoleCommunication,
    'pole-evenements'     => UserRole::PoleEvenements,
    'pole-employabilite'  => UserRole::PoleEmployabilite,
    'pole-partenariat'    => UserRole::PolePartenariat,
]);

// ─── JobOfferResource (Pôle Employabilité) ───────────────────────────────────

it('lets pole-employabilite access JobOfferResource', function () {
    loginAs(UserRole::PoleEmployabilite);

    expect(JobOfferResource::canViewAny())->toBeTrue();
});

it('blocks every other pole role from JobOfferResource', function (UserRole $role) {
    loginAs($role);

    expect(JobOfferResource::canViewAny())->toBeFalse();
})->with([
    'pole-communication'   => UserRole::PoleCommunication,
    'pole-evenements'      => UserRole::PoleEvenements,
    'pole-tech-formation'  => UserRole::PoleTechFormation,
    'pole-partenariat'     => UserRole::PolePartenariat,
]);

// ─── CompanyAccountResource (Pôle Partenariat — read-only) ───────────────────

it('lets pole-partenariat access CompanyAccountResource', function () {
    loginAs(UserRole::PolePartenariat);

    expect(CompanyAccountResource::canViewAny())->toBeTrue();
});

it('blocks every other pole role from CompanyAccountResource', function (UserRole $role) {
    loginAs($role);

    expect(CompanyAccountResource::canViewAny())->toBeFalse();
})->with([
    'pole-communication'   => UserRole::PoleCommunication,
    'pole-evenements'      => UserRole::PoleEvenements,
    'pole-tech-formation'  => UserRole::PoleTechFormation,
    'pole-employabilite'   => UserRole::PoleEmployabilite,
]);

it('prevents pole-partenariat from creating company accounts', function () {
    loginAs(UserRole::PolePartenariat);

    expect(CompanyAccountResource::canCreate())->toBeFalse();
});

it('prevents pole-partenariat from editing company accounts', function () {
    loginAs(UserRole::PolePartenariat);

    expect(CompanyAccountResource::canEdit(null))->toBeFalse();
});

it('prevents pole-partenariat from deleting company accounts', function () {
    loginAs(UserRole::PolePartenariat);

    expect(CompanyAccountResource::canDelete(null))->toBeFalse();
});

it('prevents any pole role from creating, editing, or deleting company accounts', function (UserRole $role) {
    loginAs($role);

    expect(CompanyAccountResource::canCreate())->toBeFalse();
    expect(CompanyAccountResource::canEdit(null))->toBeFalse();
    expect(CompanyAccountResource::canDelete(null))->toBeFalse();
})->with([
    'pole-communication'   => UserRole::PoleCommunication,
    'pole-evenements'      => UserRole::PoleEvenements,
    'pole-tech-formation'  => UserRole::PoleTechFormation,
    'pole-employabilite'   => UserRole::PoleEmployabilite,
    'pole-partenariat'     => UserRole::PolePartenariat,
]);
