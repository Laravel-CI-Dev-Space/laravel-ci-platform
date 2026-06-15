<?php

declare(strict_types=1);

use App\Models\CompanyRegistrationRequest;
use App\Services\AssetService;
use Illuminate\Http\UploadedFile;


it('displays the company registration form', function () {
    $response = $this->get(route('company.register'));

    $response->assertOk();
    $response->assertViewIs('company.auth.register');
});

it('stores a registration request and redirects with a success message', function () {
    $response = $this->post(route('company.register.submit'), [
        'first_name'      => 'Jean',
        'last_name'       => 'Dupont',
        'company_name'    => 'Acme Corp',
        'email'           => 'jean.dupont@example.com',
        'position'        => 'CEO',
        'country'         => 'Côte d\'Ivoire',
        'business_domain' => 'Technologie',
    ]);

    $response->assertRedirect(route('company.register'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('company_registration_requests', [
        'email'        => 'jean.dupont@example.com',
        'company_name' => 'Acme Corp',
    ]);
});

it('uploads the logo via AssetService and stores the path', function () {
    $this->mock(AssetService::class, function ($mock) {
        $mock->shouldReceive('upload')->once()->andReturn('logo_0_123456_abcdef.png');
    });

    $response = $this->post(route('company.register.submit'), [
        'first_name'      => 'Jean',
        'last_name'       => 'Dupont',
        'company_name'    => 'Acme Corp',
        'email'           => 'jean.logo@example.com',
        'position'        => 'CEO',
        'country'         => 'Côte d\'Ivoire',
        'business_domain' => 'Technologie',
        'logo'            => UploadedFile::fake()->create('logo.png', 100, 'image/png'),
    ]);

    $response->assertRedirect(route('company.register'));

    $registration = CompanyRegistrationRequest::where('email', 'jean.logo@example.com')->first();

    expect($registration)->not->toBeNull();
    expect($registration->logo_path)->toBe('logo_0_123456_abcdef.png');
});

it('rejects an invalid registration submission', function () {
    $response = $this->post(route('company.register.submit'), []);

    $response->assertSessionHasErrors(['first_name', 'last_name', 'company_name', 'email', 'position', 'country', 'business_domain']);
});
