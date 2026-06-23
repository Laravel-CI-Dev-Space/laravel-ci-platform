<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemberSearchController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));

        if (strlen($q) < 1) {
            return response()->json([]);
        }

        $members = User::query()
            ->where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('github_username', 'like', $q . '%')
                      ->orWhere('name', 'like', '%' . $q . '%');
            })
            ->select('id', 'name', 'github_username', 'avatar')
            ->orderBy('github_username')
            ->limit(8)
            ->get()
            ->map(fn (User $u) => [
                'username' => $u->github_username,
                'name'     => $u->name,
                'avatar'   => $u->avatar,
            ]);

        return response()->json($members);
    }
}
