<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\MemberCard;
use App\Services\QrCodeGenerator;

class MemberCardObserver
{
    public function __construct(private QrCodeGenerator $qrCodeGenerator) {}

    /** Génère le QR code SVG dès qu'une carte est créée. */
    public function created(MemberCard $card): void
    {
        if ($card->qr_code_svg) {
            return;
        }

        $card->updateQuietly([
            'qr_code_svg' => $this->qrCodeGenerator->forMember(
                $card->user->github_username
            ),
        ]);
    }
}
