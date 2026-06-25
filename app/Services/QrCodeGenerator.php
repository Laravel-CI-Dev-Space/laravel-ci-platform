<?php

declare(strict_types=1);

namespace App\Services;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;

class QrCodeGenerator
{
    /**
     * Génère le SVG du QR code pointant vers le profil public du membre.
     * Renvoie la chaîne SVG prête à être stockée en base ou injectée inline.
     */
    public function forMember(string $username): string
    {
        $url = route('members.show', $username);

        $qr = new QrCode($url);
        $qr->setForegroundColor(new Color(28, 28, 46));   // navy
        $qr->setBackgroundColor(new Color(255, 255, 255)); // blanc

        $writer = new SvgWriter();
        $result = $writer->write($qr);

        return $result->getString();
    }
}
