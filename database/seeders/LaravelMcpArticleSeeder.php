<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ArticleLevel;
use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;

class LaravelMcpArticleSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::where('github_username', 'ky-wilson')
            ->orWhere('github_username', 'Ky-Wilson')
            ->orWhere('email', 'hlab.digital.center@gmail.com')
            ->first();

        if (! $author) {
            $this->command->error("No author found (tried github_username=ky-wilson and hlab.digital.center@gmail.com).");
            return;
        }

        $title = 'Laravel MCP : le nouveau standard universel pour connecter vos apps aux IA';
        $slug  = 'laravel-mcp-standard-universel-ia';

        $existing = Article::where('slug', $slug)->first();
        if ($existing) {
            $existing->update(['body' => $this->body(), 'body_html' => $this->body()]);
            $this->command->info("Article mis à jour.");
            return;
        }

        $body = $this->body();

        Article::create([
            'user_id'      => $author->id,
            'reviewed_by'  => $author->id,
            'title'        => $title,
            'slug'         => $slug,
            'excerpt'      => 'Découvrez Laravel MCP, le package officiel qui permet d\'exposer vos applications Laravel aux assistants IA comme Claude, ChatGPT et Cursor via le Model Context Protocol. Tools, Resources, Prompts, OAuth 2.1, streaming SSE — tout ce qu\'il faut savoir pour prendre le train en marche.',
            'body'         => $body,
            'body_html'    => $body,
            'cover_image'  => null,
            'level'        => ArticleLevel::Advanced,
            'status'       => ArticleStatus::Published,
            'published_at' => now(),
            'reviewed_at'  => now(),
        ]);

        $this->command->info("Article «{$title}» publié avec succès.");
    }

    private function body(): string
    {
        return <<<'HTML'
<style>
/* ═══════════════════════════════════════════════
   MCP Code Widget — style Laravel docs
   ═══════════════════════════════════════════════ */
.mcp-widget {
    border-radius: 14px;
    overflow: hidden;
    border: 1px solid #30363d;
    margin: 2rem 0;
    background: #0d1117;
    box-shadow: 0 8px 32px rgba(0,0,0,.22);
}
.mcp-cat-nav {
    display: flex;
    gap: 4px;
    padding: 14px 16px 0;
    background: #0d1117;
    border-bottom: 1px solid #21262d;
    overflow-x: auto;
    scrollbar-width: none;
}
.mcp-cat-nav::-webkit-scrollbar { display: none; }
.mcp-cat {
    padding: 7px 18px;
    border-radius: 8px 8px 0 0;
    border: none;
    background: transparent;
    color: #8b949e;
    font-size: .82rem;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
    transition: background .15s, color .15s;
    font-family: inherit;
}
.mcp-cat:hover { background: #161b22; color: #c9d1d9; }
.mcp-cat.active { background: #161b22; color: #e8590c; border-bottom: 2px solid #e8590c; }

.mcp-panel { display: none; }
.mcp-panel.active { display: block; }

.mcp-file-nav {
    display: flex;
    gap: 0;
    background: #161b22;
    border-bottom: 1px solid #30363d;
    padding: 0 12px;
    overflow-x: auto;
    scrollbar-width: none;
}
.mcp-file-nav::-webkit-scrollbar { display: none; }
.mcp-file {
    padding: 8px 16px;
    border: none;
    background: transparent;
    color: #6e7681;
    font-size: .78rem;
    cursor: pointer;
    white-space: nowrap;
    border-bottom: 2px solid transparent;
    display: flex;
    align-items: center;
    gap: 6px;
    font-family: 'JetBrains Mono', monospace;
    transition: color .15s;
}
.mcp-file:hover { color: #c9d1d9; }
.mcp-file.active { color: #e8590c; border-bottom-color: #e8590c; }
.mcp-file svg { width: 13px; height: 13px; flex-shrink: 0; }

.mcp-pane { display: none; position: relative; }
.mcp-pane.active { display: block; }

/* Copy button */
.mcp-copy {
    position: absolute;
    top: 10px;
    right: 12px;
    background: #21262d;
    border: 1px solid #30363d;
    color: #8b949e;
    border-radius: 6px;
    padding: 4px 10px;
    font-size: .72rem;
    cursor: pointer;
    font-family: inherit;
    transition: background .15s, color .15s;
    z-index: 2;
}
.mcp-copy:hover { background: #30363d; color: #c9d1d9; }
.mcp-copy.copied { color: #3fb950; border-color: #3fb950; }

/* Code inside widget — no extra chrome, hljs handles colors */
.mcp-widget pre {
    margin: 0 !important;
    border: none !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    background: transparent !important;
}
.mcp-widget pre::before,
.mcp-widget pre::after { display: none !important; }
.mcp-widget pre code.hljs {
    padding: 1.2rem 1.4rem !important;
    font-size: .8rem !important;
}

/* Copy button on standard pre blocks */
.article-body .pre-wrap {
    position: relative;
}
.article-body .pre-wrap .std-copy {
    position: absolute;
    top: 10px;
    right: 12px;
    background: #21262d;
    border: 1px solid #30363d;
    color: #8b949e;
    border-radius: 6px;
    padding: 4px 10px;
    font-size: .72rem;
    cursor: pointer;
    font-family: inherit;
    z-index: 2;
    transition: background .15s, color .15s;
}
.article-body .pre-wrap .std-copy:hover { background: #30363d; color: #c9d1d9; }
.article-body .pre-wrap .std-copy.copied { color: #3fb950; border-color: #3fb950; }
</style>

<p class="lead">
  Trois milliards de messages par jour. C'est le volume que traitent aujourd'hui ChatGPT, Claude et Gemini. Vos applications Laravel sont riches en données, en logique métier, en actions — mais inaccessibles depuis ces nouveaux points d'entrée. <strong>Laravel MCP change cela.</strong>
</p>

<h2>Qu'est-ce que le Model Context Protocol ?</h2>

<p>
  Le <strong>Model Context Protocol (MCP)</strong> est un standard ouvert lancé par Anthropic. Son objectif : définir un protocole universel permettant aux assistants IA de se connecter à n'importe quelle source de données ou service applicatif de manière structurée et sécurisée.
</p>

<p>Pensez à MCP comme au <em>HTTP des intégrations IA</em>. De la même façon que HTTP a normalisé la communication entre navigateurs et serveurs web, MCP normalise la communication entre agents IA et vos services. Il définit trois primitives :</p>

<ul>
  <li><strong>Tools</strong> — Actions que l'IA peut déclencher (créer une facture, envoyer un email, interroger une API).</li>
  <li><strong>Resources</strong> — Contenus que l'utilisateur partage avec l'IA (documents, fiches produit, données).</li>
  <li><strong>Prompts</strong> — Templates de conversation réutilisables (revue de code, résumé standardisé, analyse).</li>
</ul>

<h2>Installation</h2>

<div class="pre-wrap">
<button class="std-copy" onclick="cpCode(this)">Copier</button>
<pre><code class="language-bash">composer require laravel/mcp
php artisan vendor:publish --tag=ai-routes</code></pre>
</div>

<p>La commande <code>vendor:publish</code> crée <code>routes/ai.php</code>, l'équivalent de <code>routes/api.php</code> pour vos serveurs MCP.</p>

<h2>Vue d'ensemble — les quatre piliers</h2>

<p>Voici les quatre concepts fondamentaux de Laravel MCP côte à côte, comme dans la documentation officielle :</p>

<!-- ════════════════════════════════════════════════════
     MCP WIDGET — STYLE LARAVEL DOCS
     ════════════════════════════════════════════════════ -->
<div class="mcp-widget" id="mcp-main">

  <div class="mcp-cat-nav">
    <button class="mcp-cat active" onclick="mcpCat(this,'servers')">Servers</button>
    <button class="mcp-cat" onclick="mcpCat(this,'tools')">Tools</button>
    <button class="mcp-cat" onclick="mcpCat(this,'resources')">Resources</button>
    <button class="mcp-cat" onclick="mcpCat(this,'prompts')">Prompts</button>
    <button class="mcp-cat" onclick="mcpCat(this,'testing')">Testing</button>
  </div>

  <!-- ── SERVERS ── -->
  <div class="mcp-panel active" id="mcp-p-servers">
    <div class="mcp-file-nav">
      <button class="mcp-file active" onclick="mcpFile(this,'srv-server')">
        <svg viewBox="0 0 16 16" fill="#e8590c"><path d="M1 3.5A1.5 1.5 0 012.5 2h11A1.5 1.5 0 0115 3.5v2A1.5 1.5 0 0113.5 7h-11A1.5 1.5 0 011 5.5v-2zm0 5A1.5 1.5 0 012.5 7h11A1.5 1.5 0 0115 8.5v2A1.5 1.5 0 0113.5 12h-11A1.5 1.5 0 011 10.5v-2z"/></svg>
        FlightServer.php
      </button>
      <button class="mcp-file" onclick="mcpFile(this,'srv-routes')">
        <svg viewBox="0 0 16 16" fill="#6e7681"><path d="M1 3.5A1.5 1.5 0 012.5 2h11A1.5 1.5 0 0115 3.5v2A1.5 1.5 0 0113.5 7h-11A1.5 1.5 0 011 5.5v-2zm0 5A1.5 1.5 0 012.5 7h11A1.5 1.5 0 0115 8.5v2A1.5 1.5 0 0113.5 12h-11A1.5 1.5 0 011 10.5v-2z"/></svg>
        routes/ai.php
      </button>
    </div>
    <div class="mcp-pane active" id="srv-server">
      <button class="mcp-copy" onclick="cpCode(this)">Copier</button>
<pre><code class="language-php">&lt;?php
namespace App\Mcp\Servers;

use App\Mcp\Tools\SearchFlightsTool;
use App\Mcp\Tools\BookFlightTool;
use App\Mcp\Resources\FlightPoliciesResource;
use App\Mcp\Prompts\SummarizeItineraryPrompt;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\{Name, Version, Instructions};

#[Name('Flight Server')]
#[Version('1.0.0')]
#[Instructions('Search and book flights, access airline policies.')]
class FlightServer extends Server
{
    protected array $tools = [
        SearchFlightsTool::class,
        BookFlightTool::class,
    ];

    protected array $resources = [
        FlightPoliciesResource::class,
    ];

    protected array $prompts = [
        SummarizeItineraryPrompt::class,
    ];
}</code></pre>
    </div>
    <div class="mcp-pane" id="srv-routes">
      <button class="mcp-copy" onclick="cpCode(this)">Copier</button>
<pre><code class="language-php">&lt;?php
use App\Mcp\Servers\FlightServer;
use Laravel\Mcp\Facades\Mcp;

// Serveur HTTP — accessible par n'importe quel client MCP
Mcp::web('/mcp/flights', FlightServer::class)
    -&gt;middleware(['auth:sanctum', 'throttle:mcp']);

// Serveur local — stdio, pour les IDE (Cursor, VS Code…)
Mcp::local('flights', FlightServer::class);</code></pre>
    </div>
  </div>

  <!-- ── TOOLS ── -->
  <div class="mcp-panel" id="mcp-p-tools">
    <div class="mcp-file-nav">
      <button class="mcp-file active" onclick="mcpFile(this,'tool-search')">
        <svg viewBox="0 0 16 16" fill="#e8590c"><path d="M1 3.5A1.5 1.5 0 012.5 2h11A1.5 1.5 0 0115 3.5v2A1.5 1.5 0 0113.5 7h-11A1.5 1.5 0 011 5.5v-2zm0 5A1.5 1.5 0 012.5 7h11A1.5 1.5 0 0115 8.5v2A1.5 1.5 0 0113.5 12h-11A1.5 1.5 0 011 10.5v-2z"/></svg>
        SearchFlightsTool.php
      </button>
      <button class="mcp-file" onclick="mcpFile(this,'tool-book')">
        <svg viewBox="0 0 16 16" fill="#6e7681"><path d="M1 3.5A1.5 1.5 0 012.5 2h11A1.5 1.5 0 0115 3.5v2A1.5 1.5 0 0113.5 7h-11A1.5 1.5 0 011 5.5v-2zm0 5A1.5 1.5 0 012.5 7h11A1.5 1.5 0 0115 8.5v2A1.5 1.5 0 0113.5 12h-11A1.5 1.5 0 011 10.5v-2z"/></svg>
        BookFlightTool.php
      </button>
    </div>
    <div class="mcp-pane active" id="tool-search">
      <button class="mcp-copy" onclick="cpCode(this)">Copier</button>
<pre><code class="language-php">&lt;?php
namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\{Request, Response};
use Laravel\Mcp\Server\Attributes\{Description, IsReadOnly};
use Laravel\Mcp\Server\Tool;

#[Description('Search available flights between two airports.')]
#[IsReadOnly]
class SearchFlightsTool extends Tool
{
    public function handle(Request $request): Response
    {
        $data = $request-&gt;validate([
            'origin'      =&gt; 'required|string|size:3',
            'destination' =&gt; 'required|string|size:3',
            'date'        =&gt; 'required|date|after:today',
            'passengers'  =&gt; 'integer|min:1|max:9',
        ]);

        $results = $this-&gt;flightService-&gt;search(...$data);

        return Response::structured([
            'count'   =&gt; count($results),
            'flights' =&gt; $results,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'origin'      =&gt; $schema-&gt;string()-&gt;description('IATA departure code, e.g. ABJ')-&gt;required(),
            'destination' =&gt; $schema-&gt;string()-&gt;description('IATA arrival code')-&gt;required(),
            'date'        =&gt; $schema-&gt;string()-&gt;description('Travel date YYYY-MM-DD')-&gt;required(),
            'passengers'  =&gt; $schema-&gt;integer()-&gt;description('Number of passengers')-&gt;default(1),
        ];
    }
}</code></pre>
    </div>
    <div class="mcp-pane" id="tool-book">
      <button class="mcp-copy" onclick="cpCode(this)">Copier</button>
<pre><code class="language-php">&lt;?php
namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\{Request, Response};
use Laravel\Mcp\Server\Attributes\{Description, IsDestructive};
use Laravel\Mcp\Server\Tool;

#[Description('Book a flight for the authenticated user.')]
#[IsDestructive]
class BookFlightTool extends Tool
{
    public function handle(Request $request): Response
    {
        if (! $request-&gt;user()-&gt;can('book-flights')) {
            return Response::error('Insufficient permissions.');
        }

        $data = $request-&gt;validate([
            'flight_id'  =&gt; 'required|string',
            'seat_class' =&gt; 'required|in:economy,business,first',
        ]);

        $booking = $this-&gt;bookingService-&gt;create($request-&gt;user(), $data);

        return Response::structured([
            'reference' =&gt; $booking-&gt;reference,
            'status'    =&gt; $booking-&gt;status,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'flight_id'  =&gt; $schema-&gt;string()-&gt;description('Flight ID from search results')-&gt;required(),
            'seat_class' =&gt; $schema-&gt;string()-&gt;enum(['economy','business','first'])-&gt;required(),
        ];
    }
}</code></pre>
    </div>
  </div>

  <!-- ── RESOURCES ── -->
  <div class="mcp-panel" id="mcp-p-resources">
    <div class="mcp-file-nav">
      <button class="mcp-file active" onclick="mcpFile(this,'res-static')">
        <svg viewBox="0 0 16 16" fill="#e8590c"><path d="M1 3.5A1.5 1.5 0 012.5 2h11A1.5 1.5 0 0115 3.5v2A1.5 1.5 0 0113.5 7h-11A1.5 1.5 0 011 5.5v-2zm0 5A1.5 1.5 0 012.5 7h11A1.5 1.5 0 0115 8.5v2A1.5 1.5 0 0113.5 12h-11A1.5 1.5 0 011 10.5v-2z"/></svg>
        FlightPoliciesResource.php
      </button>
      <button class="mcp-file" onclick="mcpFile(this,'res-dynamic')">
        <svg viewBox="0 0 16 16" fill="#6e7681"><path d="M1 3.5A1.5 1.5 0 012.5 2h11A1.5 1.5 0 0115 3.5v2A1.5 1.5 0 0113.5 7h-11A1.5 1.5 0 011 5.5v-2zm0 5A1.5 1.5 0 012.5 7h11A1.5 1.5 0 0115 8.5v2A1.5 1.5 0 0113.5 12h-11A1.5 1.5 0 011 10.5v-2z"/></svg>
        BookingResource.php (dynamique)
      </button>
    </div>
    <div class="mcp-pane active" id="res-static">
      <button class="mcp-copy" onclick="cpCode(this)">Copier</button>
<pre><code class="language-php">&lt;?php
namespace App\Mcp\Resources;

use Laravel\Mcp\{Request, Response};
use Laravel\Mcp\Server\Attributes\{Description, Uri, MimeType};
use Laravel\Mcp\Server\Resource;

#[Uri('flights://resources/policies')]
#[MimeType('text/plain')]
#[Description('Airline baggage and cancellation policies.')]
class FlightPoliciesResource extends Resource
{
    public function handle(Request $request): Response
    {
        return Response::text(
            "BAGGAGE POLICY\n" .
            "Economy: 23kg checked + 10kg cabin\n" .
            "Business: 2x32kg checked + 18kg cabin\n\n" .
            "CANCELLATION\nFree up to 24h before departure."
        );
    }
}</code></pre>
    </div>
    <div class="mcp-pane" id="res-dynamic">
      <button class="mcp-copy" onclick="cpCode(this)">Copier</button>
<pre><code class="language-php">&lt;?php
namespace App\Mcp\Resources;

use Laravel\Mcp\{Request, Response};
use Laravel\Mcp\Server\Attributes\{Description, MimeType};
use Laravel\Mcp\Server\Contracts\HasUriTemplate;
use Laravel\Mcp\Server\Resource;
use Laravel\Mcp\Support\UriTemplate;

// URI dynamique : bookings://{reference}
#[Description('Access a booking by its reference number.')]
#[MimeType('application/json')]
class BookingResource extends Resource implements HasUriTemplate
{
    public function uriTemplate(): UriTemplate
    {
        return new UriTemplate('bookings://{reference}');
    }

    public function handle(Request $request): Response
    {
        $booking = Booking::where('reference', $request-&gt;get('reference'))-&gt;firstOrFail();

        return Response::text(json_encode($booking-&gt;toArray()));
    }
}</code></pre>
    </div>
  </div>

  <!-- ── PROMPTS ── -->
  <div class="mcp-panel" id="mcp-p-prompts">
    <div class="mcp-file-nav">
      <button class="mcp-file active" onclick="mcpFile(this,'pmt-main')">
        <svg viewBox="0 0 16 16" fill="#e8590c"><path d="M1 3.5A1.5 1.5 0 012.5 2h11A1.5 1.5 0 0115 3.5v2A1.5 1.5 0 0113.5 7h-11A1.5 1.5 0 011 5.5v-2zm0 5A1.5 1.5 0 012.5 7h11A1.5 1.5 0 0115 8.5v2A1.5 1.5 0 0113.5 12h-11A1.5 1.5 0 011 10.5v-2z"/></svg>
        SummarizeItineraryPrompt.php
      </button>
    </div>
    <div class="mcp-pane active" id="pmt-main">
      <button class="mcp-copy" onclick="cpCode(this)">Copier</button>
<pre><code class="language-php">&lt;?php
namespace App\Mcp\Prompts;

use Laravel\Mcp\{Request, Response};
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Prompts\Argument;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('Summarize a flight itinerary in the chosen language and tone.')]
class SummarizeItineraryPrompt extends Prompt
{
    public function arguments(): array
    {
        return [
            new Argument(name: 'language', description: 'e.g. French, English', required: true),
            new Argument(name: 'style',    description: 'formal or casual',      required: false),
        ];
    }

    public function handle(Request $request): array
    {
        $language = $request-&gt;string('language');
        $style    = $request-&gt;string('style', 'formal');

        return [
            Response::text(
                "You are a travel assistant. Summarize the following flight itinerary " .
                "in {$language} using a {$style} tone."
            )-&gt;asAssistant(),
            Response::text("Here is the itinerary to summarize:"),
        ];
    }
}</code></pre>
    </div>
  </div>

  <!-- ── TESTING ── -->
  <div class="mcp-panel" id="mcp-p-testing">
    <div class="mcp-file-nav">
      <button class="mcp-file active" onclick="mcpFile(this,'tst-pest')">
        <svg viewBox="0 0 16 16" fill="#e8590c"><path d="M1 3.5A1.5 1.5 0 012.5 2h11A1.5 1.5 0 0115 3.5v2A1.5 1.5 0 0113.5 7h-11A1.5 1.5 0 011 5.5v-2zm0 5A1.5 1.5 0 012.5 7h11A1.5 1.5 0 0115 8.5v2A1.5 1.5 0 0113.5 12h-11A1.5 1.5 0 011 10.5v-2z"/></svg>
        FlightServerTest.php (Pest)
      </button>
      <button class="mcp-file" onclick="mcpFile(this,'tst-inspector')">
        <svg viewBox="0 0 16 16" fill="#6e7681"><path d="M1 3.5A1.5 1.5 0 012.5 2h11A1.5 1.5 0 0115 3.5v2A1.5 1.5 0 0113.5 7h-11A1.5 1.5 0 011 5.5v-2zm0 5A1.5 1.5 0 012.5 7h11A1.5 1.5 0 0115 8.5v2A1.5 1.5 0 0113.5 12h-11A1.5 1.5 0 011 10.5v-2z"/></svg>
        Inspector (CLI)
      </button>
    </div>
    <div class="mcp-pane active" id="tst-pest">
      <button class="mcp-copy" onclick="cpCode(this)">Copier</button>
<pre><code class="language-php">&lt;?php
use App\Mcp\Servers\FlightServer;
use App\Mcp\Tools\SearchFlightsTool;
use App\Models\User;

test('search returns structured results', function () {
    $response = FlightServer::tool(SearchFlightsTool::class, [
        'origin'      =&gt; 'ABJ',
        'destination' =&gt; 'CDG',
        'date'        =&gt; now()-&gt;addDays(30)-&gt;format('Y-m-d'),
    ]);

    $response-&gt;assertOk()-&gt;assertSee('ABJ');
});

test('booking requires authentication', function () {
    $user = User::factory()-&gt;create();

    $response = FlightServer::actingAs($user)
        -&gt;tool(BookFlightTool::class, [
            'flight_id'  =&gt; 'FL-001',
            'seat_class' =&gt; 'economy',
        ]);

    $response-&gt;assertOk();
});</code></pre>
    </div>
    <div class="mcp-pane" id="tst-inspector">
      <button class="mcp-copy" onclick="cpCode(this)">Copier</button>
<pre><code class="language-bash"># Inspecter un serveur web (HTTP)
php artisan mcp:inspector mcp/flights

# Inspecter un serveur local (stdio)
php artisan mcp:inspector flights

# Lancer le serveur local
php artisan mcp:start flights</code></pre>
    </div>
  </div>

</div>
<!-- ════ FIN WIDGET ════ -->

<h2>Authentification</h2>

<p>Laravel MCP s'intègre nativement avec Sanctum et Passport.</p>

<h3>Sanctum — tokens Bearer</h3>

<div class="pre-wrap">
<button class="std-copy" onclick="cpCode(this)">Copier</button>
<pre><code class="language-php">// routes/ai.php
Mcp::web('/mcp/flights', FlightServer::class)
    -&gt;middleware('auth:sanctum');</code></pre>
</div>

<h3>OAuth 2.1 via Passport</h3>

<div class="pre-wrap">
<button class="std-copy" onclick="cpCode(this)">Copier</button>
<pre><code class="language-php">// routes/ai.php
Mcp::oauthRoutes();

Mcp::web('/mcp/flights', FlightServer::class)
    -&gt;middleware('auth:api');</code></pre>
</div>

<h2>Mode Local — intégration IDE</h2>

<p>En mode local, le serveur tourne en stdio et peut être branché directement dans Cursor, VS Code ou Claude Desktop via un fichier <code>.mcp.json</code> à la racine du projet :</p>

<div class="pre-wrap">
<button class="std-copy" onclick="cpCode(this)">Copier</button>
<pre><code class="language-json">{
  "mcpServers": {
    "flights": {
      "command": "php",
      "args": ["artisan", "mcp:start", "flights"]
    }
  }
}</code></pre>
</div>

<h2>Le Client MCP — consommer des serveurs tiers</h2>

<p>Laravel MCP inclut également un client pour appeler des serveurs MCP externes depuis votre propre app :</p>

<div class="pre-wrap">
<button class="std-copy" onclick="cpCode(this)">Copier</button>
<pre><code class="language-php">use Laravel\Mcp\Client;
use Laravel\Mcp\Facades\Mcp;

// Enregistrement
Mcp::registerClient('weather', fn () =&gt;
    Client::web('https://weather.example.com/mcp')
        -&gt;withToken(fn () =&gt; auth()-&gt;user()-&gt;weatherToken())
);

// Utilisation
$tools  = Mcp::client('weather')-&gt;tools();
$result = Mcp::client('weather')-&gt;callTool('get-forecast', ['city' =&gt; 'Abidjan']);

echo $result-&gt;text(); // "Partly cloudy, 31°C"</code></pre>
</div>

<h2>Cas d'usage concrets</h2>

<ul>
  <li><strong>SaaS de gestion de projet</strong> — créer des tickets, assigner des tâches, générer des rapports de sprint directement depuis Claude ou Cursor.</li>
  <li><strong>E-commerce</strong> — rechercher des produits, passer des commandes, suivre des livraisons en langage naturel.</li>
  <li><strong>CRM</strong> — créer des contacts, envoyer des emails, consulter l'historique client ; Prompts pour les emails de relance.</li>
  <li><strong>Plateforme communautaire</strong> — créer des articles, répondre au forum, s'inscrire à des événements via l'assistant IA.</li>
  <li><strong>DevOps</strong> — déclencher des déploiements, lire des logs, scaler des instances. L'IA devient un copilote d'infrastructure.</li>
</ul>

<h2>Voir MCP en action</h2>

<p>L'équipe Laravel a publié deux vidéos pour accompagner le lancement. Une intro de 3 minutes pour commencer :</p>

<div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:10px;margin-bottom:24px;">
  <iframe src="https://www.youtube.com/embed/ldqusTDx4K0" title="Laravel MCP — Introduction (3 min)" style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;" allowfullscreen loading="lazy"></iframe>
</div>

<p>Et le tutoriel complet pour construire une vraie app :</p>

<div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:10px;margin-bottom:24px;">
  <iframe src="https://www.youtube.com/embed/QiyKIkCUCcU" title="Building a Real Laravel MCP App — Full Tutorial" style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;" allowfullscreen loading="lazy"></iframe>
</div>

<h2>Conclusion</h2>

<p>
  MCP n'est pas une mode. C'est une infrastructure. De la même façon que les APIs REST ont ouvert les applications web à des intégrations infinies dans les années 2010, MCP ouvre vos applications aux agents IA aujourd'hui.
</p>

<blockquote>
  Avec plus d'un milliard d'utilisateurs envoyant plus de 20 milliards de messages par semaine aux assistants IA, chaque semaine passée sans MCP est une semaine où vos utilisateurs ne peuvent pas accéder à votre application depuis leur interface préférée.
</blockquote>

<div style="display:inline-flex;align-items:center;gap:8px;background:#fff7ed;border:1px solid #fdba74;border-radius:8px;padding:10px 18px;margin:1.5rem 0;font-size:.9rem;">
  <span>🚀</span>
  <span><strong>Beta publique disponible depuis septembre 2025.</strong> L'équipe Laravel l'a field-testé en production sur <a href="https://blog.laravel.com/announcing-laravel-boost" target="_blank" rel="noopener">Laravel Boost</a> avant le lancement.</span>
</div>

<p>
  <strong>Ressources officielles :</strong><br>
  📖 <a href="https://laravel.com/framework/docs/mcp" target="_blank" rel="noopener">Documentation Laravel MCP</a><br>
  🧪 <a href="https://github.com/laravel/locket" target="_blank" rel="noopener">Locket — l'app de démo officielle</a><br>
  📦 <a href="https://github.com/laravel/mcp" target="_blank" rel="noopener">Package GitHub</a>
</p>

<script>
(function() {
  // ── Onglets catégories ──────────────────────────────
  window.mcpCat = function(btn, id) {
    var widget = btn.closest('.mcp-widget');
    widget.querySelectorAll('.mcp-cat').forEach(function(b) { b.classList.remove('active'); });
    widget.querySelectorAll('.mcp-panel').forEach(function(p) { p.classList.remove('active'); });
    btn.classList.add('active');
    var panel = widget.querySelector('#mcp-p-' + id);
    if (panel) {
      panel.classList.add('active');
      // re-highlight code in this panel (in case hljs hasn't run yet)
      panel.querySelectorAll('pre code:not(.hljs)').forEach(function(el) {
        if (window.hljs) hljs.highlightElement(el);
      });
    }
  };

  // ── Onglets fichiers ────────────────────────────────
  window.mcpFile = function(btn, id) {
    var panel = btn.closest('.mcp-panel');
    panel.querySelectorAll('.mcp-file').forEach(function(b) { b.classList.remove('active'); });
    panel.querySelectorAll('.mcp-pane').forEach(function(p) { p.classList.remove('active'); });
    btn.classList.add('active');
    var pane = document.getElementById(id);
    if (pane) {
      pane.classList.add('active');
      pane.querySelectorAll('pre code:not(.hljs)').forEach(function(el) {
        if (window.hljs) hljs.highlightElement(el);
      });
    }
  };

  // ── Bouton copier ───────────────────────────────────
  window.cpCode = function(btn) {
    var pre = btn.nextElementSibling || btn.parentElement.querySelector('pre');
    if (!pre) return;
    var text = pre.innerText || pre.textContent;
    navigator.clipboard.writeText(text.trim()).then(function() {
      var orig = btn.textContent;
      btn.textContent = '✓ Copié';
      btn.classList.add('copied');
      setTimeout(function() {
        btn.textContent = orig;
        btn.classList.remove('copied');
      }, 2000);
    });
  };
})();
</script>
HTML;
    }
}
