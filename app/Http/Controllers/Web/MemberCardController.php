<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MemberCard;
use App\Models\User;
use App\Services\MemberCardExporter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MemberCardController extends Controller
{
    public function __construct(private MemberCardExporter $exporter) {}

    /**
     * Page de prévisualisation intégrée au layout du site.
     * GET /members/{username}/card/{level?}
     */
    public function preview(string $username, int $level = 0): \Illuminate\View\View
    {
        $user = User::where('github_username', $username)->firstOrFail();

        $card = $this->resolveCard($user, $level);

        abort_if(! $card, 404, 'Aucune carte active pour ce membre.');

        $allCards = $user->memberCards()->active()->orderBy('level')->get();

        return view('web.member-card.preview', compact('user', 'card', 'level', 'allCards'));
    }

    /**
     * HTML brut de la carte (utilisé par l'iframe + Puppeteer).
     * GET /members/{username}/card/{level?}/embed
     */
    public function embed(string $username, int $level = 0): Response
    {
        $user = User::where('github_username', $username)->firstOrFail();

        $card = $this->resolveCard($user, $level);

        abort_if(! $card, 404, 'Aucune carte active pour ce membre.');

        $html = view($card->templateView(), compact('card'))->render();

        return response($html, 200, ['Content-Type' => 'text/html']);
    }

    /**
     * Télécharge le PNG de la carte.
     * GET /members/{username}/card/{level?}/download
     */
    public function download(string $username, int $level = 0): \Symfony\Component\HttpFoundation\Response
    {
        $user = User::where('github_username', $username)->firstOrFail();

        $card = $this->resolveCard($user, $level);

        abort_if(! $card, 404, 'Aucune carte active pour ce membre.');

        $png      = $this->exporter->toPng($card);
        $filename = "carte-membre-{$username}-niveau{$card->level}.png";

        return response($png, 200, [
            'Content-Type'        => 'image/png',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Content-Length'      => strlen($png),
        ]);
    }

    /**
     * Met à jour les champs personnalisables de la carte (avatar + poste).
     * PATCH /dashboard/member/card/{card}
     */
    public function update(Request $request, MemberCard $card): \Illuminate\Http\RedirectResponse
    {
        abort_unless($card->user_id === auth()->id(), 403);

        $data = $request->validate([
            'poste'       => ['nullable', 'string', 'max:120'],
            'card_avatar' => ['nullable', 'url', 'max:500'],
        ]);

        $card->update($data);

        return back()->with('card_updated', true);
    }

    /** Retourne la carte active du niveau demandé (ou la plus élevée si level=0). */
    private function resolveCard(User $user, int $level): ?MemberCard
    {
        $query = $user->memberCards()->active()->with(['user.profile.grade', 'user']);

        if ($level > 0) {
            return $query->where('level', $level)->first();
        }

        return $query->orderByDesc('level')->first();
    }
}
