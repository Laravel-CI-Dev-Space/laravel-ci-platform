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
            'excerpt'      => 'Laravel vient de lancer laravel/mcp en beta publique. À travers cet article, Laravel CI vous donne les bases pour comprendre le Model Context Protocol et l\'intégrer dans vos applications dès aujourd\'hui.',
            'body'         => $body,
            'body_html'    => $body,
            'cover_image'  => null,
            'level'        => ArticleLevel::Advanced,
            'status'       => ArticleStatus::Published,
            'published_at' => now(),
            'reviewed_at'  => now(),
        ]);

        $this->command->info("Article publie avec succes.");
    }

    private function body(): string
    {
        return <<<'HTML'
<style>
.mcp-widget {
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #30363d;
    margin: 2rem 0;
    background: #0d1117;
    box-shadow: 0 8px 32px rgba(0,0,0,.2);
}
.mcp-cat-nav {
    display: flex;
    gap: 4px;
    padding: 12px 14px 0;
    background: #0d1117;
    border-bottom: 1px solid #21262d;
    overflow-x: auto;
    scrollbar-width: none;
}
.mcp-cat-nav::-webkit-scrollbar { display: none; }
.mcp-cat {
    padding: 7px 16px;
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
    border-bottom: 2px solid transparent;
}
.mcp-cat:hover { color: #c9d1d9; }
.mcp-cat.active { background: #161b22; color: #e8590c; border-bottom-color: #e8590c; }
.mcp-panel { display: none; }
.mcp-panel.active { display: block; }
.mcp-file-nav {
    display: flex;
    background: #161b22;
    border-bottom: 1px solid #30363d;
    padding: 0 12px;
    overflow-x: auto;
    scrollbar-width: none;
}
.mcp-file-nav::-webkit-scrollbar { display: none; }
.mcp-file {
    padding: 8px 14px;
    border: none;
    background: transparent;
    color: #6e7681;
    font-size: .78rem;
    cursor: pointer;
    white-space: nowrap;
    border-bottom: 2px solid transparent;
    font-family: 'JetBrains Mono', monospace;
    transition: color .15s;
}
.mcp-file:hover { color: #c9d1d9; }
.mcp-file.active { color: #e8590c; border-bottom-color: #e8590c; }
.mcp-pane { display: none; position: relative; }
.mcp-pane.active { display: block; }
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
.mcp-widget pre {
    margin: 0 !important;
    border: none !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    background: transparent !important;
}
.mcp-widget pre::before,
.mcp-widget pre::after { display: none !important; }
.mcp-widget pre code,
.mcp-widget pre code.hljs {
    display: block;
    padding: 1.2rem 1.4rem !important;
    font-size: .8rem !important;
    background: transparent !important;
    color: #c9d1d9;
}
.article-body .pre-wrap { position: relative; }
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
.mcp-launch-box {
    background: #fff7ed;
    border: 1px solid #fed7aa;
    border-radius: 8px;
    padding: 14px 20px;
    margin: 1.5rem 0;
    font-size: .92rem;
    line-height: 1.7;
}
</style>

<p class="lead">La semaine dernière, l'équipe Laravel a discrètement poussé quelque chose dans l'écosystème qui pourrait bien changer la façon dont vous pensez vos applications. Un package baptisé <code>laravel/mcp</code>. Pas de grand discours. Juste une commande composer et des API propres. Mais derrière cette simplicité se cache quelque chose d'important.</p>

<p>Nous sommes en 2025 et les assistants IA comme Claude, ChatGPT, Cursor et GitHub Copilot traitent plus de trois milliards de messages par jour. Ce sont devenus des points d'entrée à part entière, au même titre que le navigateur ou l'API REST. Vos applications Laravel sont riches en données, en logique métier, en actions, mais inaccessibles depuis ces nouveaux points d'entrée. Laravel MCP change cela.</p>

<p>À travers cet article, Laravel CI vous donne les bases pour comprendre le Model Context Protocol, pourquoi ça compte pour la communauté Laravel en Côte d'Ivoire et en Afrique francophone, et comment le mettre en place dans votre application dès aujourd'hui.</p>

<h2>C'est quoi le Model Context Protocol ?</h2>

<p>Le <strong>Model Context Protocol (MCP)</strong> est un standard ouvert lancé par Anthropic. Son objectif est simple : définir un protocole universel permettant aux assistants IA de se connecter à n'importe quelle source de données ou service applicatif de manière structurée et sécurisée.</p>

<p>Pensez à MCP comme au <em>HTTP des intégrations IA</em>. De la même façon que HTTP a normalisé la communication entre navigateurs et serveurs web, MCP normalise la communication entre agents IA et vos services. Un client MCP peut invoquer n'importe quel serveur MCP sans code d'intégration spécifique, exactement comme un navigateur peut ouvrir n'importe quel site web.</p>

<p>Il repose sur trois primitives fondamentales.</p>

<ul>
  <li><strong>Tools.</strong> Ce que l'IA peut faire dans votre application : créer une facture, envoyer un email, déclencher un déploiement. Tout ce que vous pouvez coder peut devenir un Tool.</li>
  <li><strong>Resources.</strong> Ce que l'utilisateur partage avec l'IA comme contexte : un document, une fiche produit, un historique client. L'utilisateur choisit ce qu'il donne à lire à l'assistant.</li>
  <li><strong>Prompts.</strong> Des templates de conversation réutilisables que l'utilisateur peut invoquer d'un clic : "Résume cet itinéraire", "Fais une revue de code", "Génère un rapport de sprint".</li>
</ul>

<h2>Installation</h2>

<p>Le package est compatible Laravel 10, 11 et 12, et requiert PHP 8.1+.</p>

<div class="pre-wrap">
<button class="std-copy" onclick="cpCode(this)">Copier</button>
<pre><code class="language-bash">composer require laravel/mcp
php artisan vendor:publish --tag=ai-routes</code></pre>
</div>

<p>La commande <code>vendor:publish</code> crée <code>routes/ai.php</code>, l'équivalent de <code>routes/api.php</code> pour vos serveurs MCP. C'est là que vous déclarez quels serveurs exposer et avec quelle protection.</p>

<h2>Les quatre piliers en pratique</h2>

<p>Voici les quatre concepts fondamentaux côte à côte. L'exemple utilise un serveur de réservation de vols, identique à celui de la documentation officielle.</p>

<div class="mcp-widget" id="mcp-main">
  <div class="mcp-cat-nav">
    <button class="mcp-cat active" onclick="mcpCat(this,'servers')">Servers</button>
    <button class="mcp-cat" onclick="mcpCat(this,'tools')">Tools</button>
    <button class="mcp-cat" onclick="mcpCat(this,'resources')">Resources</button>
    <button class="mcp-cat" onclick="mcpCat(this,'prompts')">Prompts</button>
    <button class="mcp-cat" onclick="mcpCat(this,'testing')">Testing</button>
  </div>

  <div class="mcp-panel active" id="mcp-p-servers">
    <div class="mcp-file-nav">
      <button class="mcp-file active" onclick="mcpFile(this,'srv-server')">FlightServer.php</button>
      <button class="mcp-file" onclick="mcpFile(this,'srv-routes')">routes/ai.php</button>
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
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;
use Laravel\Mcp\Server\Attributes\Instructions;

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

// Serveur HTTP accessible par n'importe quel client MCP
Mcp::web('/mcp/flights', FlightServer::class)
    -&gt;middleware(['auth:sanctum', 'throttle:mcp']);

// Serveur local en stdio, pour les IDE comme Cursor ou VS Code
Mcp::local('flights', FlightServer::class);</code></pre>
    </div>
  </div>

  <div class="mcp-panel" id="mcp-p-tools">
    <div class="mcp-file-nav">
      <button class="mcp-file active" onclick="mcpFile(this,'tool-search')">SearchFlightsTool.php</button>
      <button class="mcp-file" onclick="mcpFile(this,'tool-book')">BookFlightTool.php</button>
    </div>
    <div class="mcp-pane active" id="tool-search">
      <button class="mcp-copy" onclick="cpCode(this)">Copier</button>
<pre><code class="language-php">&lt;?php
namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\IsReadOnly;
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
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\IsDestructive;
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

  <div class="mcp-panel" id="mcp-p-resources">
    <div class="mcp-file-nav">
      <button class="mcp-file active" onclick="mcpFile(this,'res-static')">FlightPoliciesResource.php</button>
      <button class="mcp-file" onclick="mcpFile(this,'res-dynamic')">BookingResource.php (dynamique)</button>
    </div>
    <div class="mcp-pane active" id="res-static">
      <button class="mcp-copy" onclick="cpCode(this)">Copier</button>
<pre><code class="language-php">&lt;?php
namespace App\Mcp\Resources;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Uri;
use Laravel\Mcp\Server\Attributes\MimeType;
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
            "CANCELLATION\n" .
            "Free cancellation up to 24h before departure."
        );
    }
}</code></pre>
    </div>
    <div class="mcp-pane" id="res-dynamic">
      <button class="mcp-copy" onclick="cpCode(this)">Copier</button>
<pre><code class="language-php">&lt;?php
namespace App\Mcp\Resources;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\MimeType;
use Laravel\Mcp\Server\Contracts\HasUriTemplate;
use Laravel\Mcp\Server\Resource;
use Laravel\Mcp\Support\UriTemplate;

// URI parametree : bookings://{reference}
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

  <div class="mcp-panel" id="mcp-p-prompts">
    <div class="mcp-file-nav">
      <button class="mcp-file active" onclick="mcpFile(this,'pmt-main')">SummarizeItineraryPrompt.php</button>
    </div>
    <div class="mcp-pane active" id="pmt-main">
      <button class="mcp-copy" onclick="cpCode(this)">Copier</button>
<pre><code class="language-php">&lt;?php
namespace App\Mcp\Prompts;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
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

  <div class="mcp-panel" id="mcp-p-testing">
    <div class="mcp-file-nav">
      <button class="mcp-file active" onclick="mcpFile(this,'tst-pest')">FlightServerTest.php (Pest)</button>
      <button class="mcp-file" onclick="mcpFile(this,'tst-inspector')">Inspector (terminal)</button>
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
<pre><code class="language-bash"># Explorer votre serveur MCP en temps reel
php artisan mcp:inspector mcp/flights

# Version locale (stdio)
php artisan mcp:inspector flights

# Demarrer le serveur local
php artisan mcp:start flights</code></pre>
    </div>
  </div>
</div>

<h2>Authentification</h2>

<p>Laravel MCP s'intègre nativement avec Sanctum pour les tokens Bearer et Passport pour OAuth 2.1. La deuxième option est recommandée quand vous voulez que des clients tiers comme Claude Desktop ou Cursor se connectent à votre serveur de façon sécurisée.</p>

<h3>Avec Sanctum</h3>

<div class="pre-wrap">
<button class="std-copy" onclick="cpCode(this)">Copier</button>
<pre><code class="language-php">Mcp::web('/mcp/flights', FlightServer::class)
    -&gt;middleware('auth:sanctum');</code></pre>
</div>

<h3>Avec OAuth 2.1 via Passport</h3>

<div class="pre-wrap">
<button class="std-copy" onclick="cpCode(this)">Copier</button>
<pre><code class="language-php">// routes/ai.php
Mcp::oauthRoutes();

Mcp::web('/mcp/flights', FlightServer::class)
    -&gt;middleware('auth:api');</code></pre>
</div>

<h2>Mode Local pour les IDE</h2>

<p>En mode local, le serveur tourne en stdio et se branche directement dans Cursor, VS Code ou Claude Desktop. Il suffit d'un fichier <code>.mcp.json</code> à la racine du projet.</p>

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

<p>L'assistant de votre IDE peut désormais appeler vos Tools directement, sans quitter l'éditeur.</p>

<h2>Consommer des serveurs MCP tiers</h2>

<p>Laravel MCP inclut aussi un client pour appeler des serveurs MCP externes depuis votre propre application. Vous pouvez ainsi orchestrer des services de partenaires ou des APIs tierces qui exposent un serveur MCP.</p>

<div class="pre-wrap">
<button class="std-copy" onclick="cpCode(this)">Copier</button>
<pre><code class="language-php">use Laravel\Mcp\Client;
use Laravel\Mcp\Facades\Mcp;

Mcp::registerClient('weather', fn () =&gt;
    Client::web('https://weather.example.com/mcp')
        -&gt;withToken(fn () =&gt; auth()-&gt;user()-&gt;weatherToken())
);

$result = Mcp::client('weather')-&gt;callTool('get-forecast', ['city' =&gt; 'Abidjan']);

echo $result-&gt;text();</code></pre>
</div>

<h2>Ce que ca change concrètement</h2>

<p>Pour vous donner une idée du champ des possibles, voici quelques cas que vous pourriez implémenter cette semaine dans vos projets.</p>

<ul>
  <li><strong>Un SaaS de gestion de projet</strong> qui expose des Tools pour créer des tickets, assigner des tâches et générer des rapports de sprint. Votre client peut désormais demander à Claude de faire tout cela en langage naturel.</li>
  <li><strong>Une plateforme e-commerce</strong> avec des Tools pour rechercher des produits, passer des commandes et suivre des livraisons. Plus besoin d'application mobile dédiée pour les utilisateurs power-users.</li>
  <li><strong>Un CRM</strong> avec des Prompts préconçus pour générer des emails de relance ou résumer un historique client en quelques secondes.</li>
  <li><strong>Des outils DevOps</strong> où l'IA devient un copilote d'infrastructure, capable de déclencher des déploiements, lire des logs et scaler des instances depuis une conversation.</li>
</ul>

<h2>Voir MCP en action</h2>

<p>L'équipe Laravel a publié une introduction de 3 minutes pour montrer concrètement l'impact de MCP sur une app Laravel.</p>

<div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:10px;margin-bottom:24px;">
  <iframe src="https://www.youtube.com/embed/ldqusTDx4K0" title="Laravel MCP en 3 minutes" style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;" allowfullscreen loading="lazy"></iframe>
</div>

<p>Et pour aller jusqu'au bout, le tutoriel complet de construction d'une vraie app MCP avec Laravel.</p>

<div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:10px;margin-bottom:24px;">
  <iframe src="https://www.youtube.com/embed/QiyKIkCUCcU" title="Building a Real Laravel MCP App" style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;" allowfullscreen loading="lazy"></iframe>
</div>

<h2>Pour aller plus loin</h2>

<div class="mcp-launch-box">
  <strong>Beta publique disponible depuis septembre 2025.</strong> L'équipe Laravel a field-teste le package en production sur Laravel Boost avant le lancement public. La documentation complete est disponible sur <a href="https://laravel.com/framework/docs/mcp" target="_blank" rel="noopener">laravel.com/framework/docs/mcp</a>, et l'application de demonstration <a href="https://github.com/laravel/locket" target="_blank" rel="noopener">Locket</a> expose les trois interfaces d'une app moderne : web, API et serveur MCP.
</div>

<p>MCP n'est pas une mode. C'est une infrastructure. De la meme facon que les APIs REST ont ouvert les applications web a des integrations infinies dans les annees 2010, MCP ouvre vos applications aux agents IA aujourd'hui. La difference, c'est que le rythme d'adoption est bien plus rapide.</p>

<p>Avec plus d'un milliard d'utilisateurs envoyant plus de 20 milliards de messages par semaine aux assistants IA, chaque semaine passee sans MCP est une semaine ou vos utilisateurs ne peuvent pas acceder a votre application depuis leur interface preferee. Laravel vous donne maintenant les outils pour changer ca.</p>

<script>
(function() {
    window.mcpCat = function(btn, id) {
        var widget = btn.closest('.mcp-widget');
        widget.querySelectorAll('.mcp-cat').forEach(function(b) { b.classList.remove('active'); });
        widget.querySelectorAll('.mcp-panel').forEach(function(p) { p.classList.remove('active'); });
        btn.classList.add('active');
        var panel = widget.querySelector('#mcp-p-' + id);
        if (panel) {
            panel.classList.add('active');
            panel.querySelectorAll('pre code').forEach(function(el) {
                if (window.hljs && !el.classList.contains('hljs')) hljs.highlightElement(el);
            });
        }
    };

    window.mcpFile = function(btn, id) {
        var panel = btn.closest('.mcp-panel');
        panel.querySelectorAll('.mcp-file').forEach(function(b) { b.classList.remove('active'); });
        panel.querySelectorAll('.mcp-pane').forEach(function(p) { p.classList.remove('active'); });
        btn.classList.add('active');
        var pane = document.getElementById(id);
        if (pane) {
            pane.classList.add('active');
            pane.querySelectorAll('pre code').forEach(function(el) {
                if (window.hljs && !el.classList.contains('hljs')) hljs.highlightElement(el);
            });
        }
    };

    window.cpCode = function(btn) {
        var container = btn.parentElement;
        var pre = container.querySelector('pre') || btn.nextElementSibling;
        if (!pre) return;
        navigator.clipboard.writeText((pre.innerText || pre.textContent).trim()).then(function() {
            var orig = btn.textContent;
            btn.textContent = 'Copie';
            btn.classList.add('copied');
            setTimeout(function() { btn.textContent = orig; btn.classList.remove('copied'); }, 2000);
        });
    };
})();
</script>
HTML;
    }
}
