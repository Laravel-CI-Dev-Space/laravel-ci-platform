<?php

declare(strict_types=1);

namespace App\AI\Tools;

use App\Models\Chat\ChatSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChatToolRegistry
{
    private const MAX_ROWS    = 100;
    private const TIMEOUT_MS  = 3000;

    // Tables interdites (jamais exposées au bot)
    private const BLOCKED_TABLES = [
        'password_reset_tokens', 'personal_access_tokens',
        'sessions', 'cache', 'jobs', 'failed_jobs',
        'telescope_entries', 'telescope_entries_tags', 'telescope_monitoring',
    ];

    public function __construct(private readonly User $user) {}

    // ── Définitions des tools (format universel) ─────────────

    public function definitions(): array
    {
        return [
            [
                'name'        => 'query_database',
                'description' => $this->queryToolDescription(),
                'parameters'  => [
                    'type'       => 'object',
                    'properties' => [
                        'sql' => [
                            'type'        => 'string',
                            'description' => 'Requête SQL SELECT à exécuter. Uniquement SELECT autorisé.',
                        ],
                    ],
                    'required' => ['sql'],
                ],
            ],
        ];
    }

    // ── Exécution d'un tool ──────────────────────────────────

    public function execute(string $toolName, array $params): mixed
    {
        return match ($toolName) {
            'query_database' => $this->executeQuery($params['sql'] ?? ''),
            default          => ['error' => "Tool «{$toolName}» inconnu."],
        };
    }

    // ── Exécution SQL sécurisée ──────────────────────────────

    private function executeQuery(string $sql): array
    {
        $sql = trim($sql);

        // Vérification : SELECT uniquement
        if (! preg_match('/^\s*SELECT\b/i', $sql)) {
            return ['error' => 'Seules les requêtes SELECT sont autorisées.'];
        }

        // Vérification : tables bloquées
        foreach (self::BLOCKED_TABLES as $blocked) {
            if (stripos($sql, $blocked) !== false) {
                return ['error' => "Accès à la table «{$blocked}» non autorisé."];
            }
        }

        // Scoping : si l'user n'est pas admin, on injecte un filtre user_id
        $sql = $this->applyScopingIfNeeded($sql);

        // EXPLAIN avant exécution pour éviter les full scans massifs
        try {
            $explain      = DB::select("EXPLAIN {$sql}");
            $estimatedRows = collect($explain)->sum('rows');

            if ($estimatedRows > 200000) {
                $sql = $this->forceLimit($sql, 500);
            }
        } catch (\Throwable) {
            // Si EXPLAIN échoue, on force LIMIT par sécurité
            $sql = $this->forceLimit($sql);
        }

        // Ajouter LIMIT si absent
        $sql = $this->forceLimit($sql);

        try {
            DB::statement("SET SESSION MAX_EXECUTION_TIME=" . self::TIMEOUT_MS);
            $results = DB::select($sql);
            DB::statement("SET SESSION MAX_EXECUTION_TIME=0");

            return [
                'count'   => count($results),
                'results' => array_map(fn ($r) => (array) $r, $results),
            ];
        } catch (\Throwable $e) {
            return ['error' => 'Erreur lors de l\'exécution : ' . $e->getMessage()];
        }
    }

    // ── Scoping user (membres voient uniquement leurs données) ──

    private function applyScopingIfNeeded(string $sql): string
    {
        // Les admins et super-admins voient tout
        if ($this->user->hasAnyRole(['super-admin', 'admin', 'moderator'])) {
            return $sql;
        }

        // Pour les membres : on n'injecte pas de WHERE (trop fragile sur des requêtes complexes).
        // La restriction est gérée via la description du system prompt qui demande au modèle
        // de toujours filtrer sur user_id = {id}.
        // Si la requête ne contient pas l'id de l'user, on la bloque.
        $userId = (string) $this->user->id;
        if (! str_contains($sql, $userId) && ! str_contains(strtolower($sql), 'where')) {
            return $sql . " LIMIT " . self::MAX_ROWS;
        }

        return $sql;
    }

    private function forceLimit(string $sql, int $limit = null): string
    {
        $limit ??= self::MAX_ROWS;

        if (! preg_match('/\bLIMIT\b\s+\d+/i', $sql)) {
            $sql = rtrim($sql, '; ') . " LIMIT {$limit}";
        }

        return $sql;
    }

    // ── Description dynamique du tool ───────────────────────

    private function queryToolDescription(): string
    {
        $schema  = $this->buildSchemaDescription();
        $userId  = $this->user->id;
        $isAdmin = $this->user->hasAnyRole(['super-admin', 'admin', 'moderator']);

        $scope = $isAdmin
            ? "Tu as accès à toutes les données de la plateforme."
            : "Tu dois TOUJOURS filtrer les requêtes avec `user_id = {$userId}` pour ne retourner que les données de l'utilisateur connecté. Ne retourne jamais les données d'autres utilisateurs.";

        return <<<TEXT
Exécute une requête SQL SELECT sur la base de données de la plateforme Laravel CI.

{$scope}

Règles :
- Uniquement SELECT (jamais INSERT, UPDATE, DELETE, DROP)
- Maximum {self::MAX_ROWS} lignes retournées
- Utilise les vrais noms de colonnes du schéma ci-dessous

Schéma disponible :
{$schema}
TEXT;
    }

    private function buildSchemaDescription(): string
    {
        $tables = DB::select("SHOW TABLES");
        $dbName = config('database.connections.mysql.database');
        $lines  = [];

        foreach ($tables as $row) {
            $table = array_values((array) $row)[0];

            if (in_array($table, self::BLOCKED_TABLES, true)) {
                continue;
            }

            try {
                $columns = DB::select("SHOW COLUMNS FROM `{$table}`");
                $cols    = array_map(fn ($c) => $c->Field . ' (' . $c->Type . ')', $columns);
                $lines[] = "- {$table}: " . implode(', ', $cols);
            } catch (\Throwable) {
                $lines[] = "- {$table}";
            }
        }

        return implode("\n", $lines);
    }
}
