<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Forum;

use App\Enums\ReportStatus;
use App\Models\Question;
use App\Models\User;
use App\Services\Forum\ReportService;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

uses(TestCase::class);

it('creates a report for a reportable model', function () {
    $user     = User::factory()->create();
    $question = Question::factory()->create();
    $service  = new ReportService;

    $report = $service->createReport($user, $question, [
        'reason'      => 'spam',
        'description' => 'Ce contenu est du spam.',
    ]);

    expect($report->reporter_id)->toBe($user->id);
    expect($report->reportable_id)->toBe($question->id);
    expect($report->status)->toBe(ReportStatus::Pending);
});

it('rejects a duplicate report from the same user', function () {
    $user     = User::factory()->create();
    $question = Question::factory()->create();
    $service  = new ReportService;

    $service->createReport($user, $question, ['reason' => 'spam']);

    expect(fn () => $service->createReport($user, $question, ['reason' => 'spam']))
        ->toThrow(HttpException::class);
});
