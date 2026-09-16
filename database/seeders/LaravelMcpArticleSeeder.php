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

        if (Article::where('slug', $slug)->exists()) {
            $this->command->info("Article already exists, skipping.");
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
            'body_html'    => $body,   // already HTML
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
<p class="lead">
  Trois milliards de messages par jour. C'est le volume que traitent aujourd'hui les principaux assistants IA comme ChatGPT, Claude et Gemini. Pendant ce temps, vos applications Laravel restent des îlots : riches en données, mais inaccessibles depuis ces nouveaux points d'entrée. <strong>Laravel MCP</strong> change cela.
</p>

<h2>Qu'est-ce que le Model Context Protocol ?</h2>

<p>
  Le <strong>Model Context Protocol (MCP)</strong> est un standard ouvert lancé par Anthropic en novembre 2024. Son objectif : définir un protocole universel permettant aux assistants IA de se connecter à n'importe quelle source de données ou service applicatif, de manière structurée et sécurisée.
</p>

<p>
  Pensez à MCP comme au <em>HTTP des intégrations IA</em>. Tout comme HTTP a normalisé la communication entre navigateurs et serveurs web, MCP normalise la communication entre agents IA et vos services. Un client MCP (Claude Desktop, Cursor, GitHub Copilot…) peut invoquer n'importe quel serveur MCP sans code d'intégration spécifique — la même façon dont un navigateur peut ouvrir n'importe quel site web.
</p>

<p>
  Le protocole définit trois primitives fondamentales :
</p>

<ul>
  <li><strong>Tools</strong> — Actions que l'IA peut déclencher (créer une facture, envoyer un email, interroger une API).</li>
  <li><strong>Resources</strong> — Contenus que l'utilisateur peut partager avec l'IA (documents, fiches produit, données utilisateur).</li>
  <li><strong>Prompts</strong> — Templates de conversation réutilisables (revue de code, analyse de contenu, résumé standardisé).</li>
</ul>

<h2>Laravel MCP : l'implémentation officielle</h2>

<p>
  Le package <code>laravel/mcp</code> est l'implémentation officielle du MCP pour le framework Laravel, développée et maintenue par l'équipe Laravel. Il est compatible Laravel 10, 11 et 12, requiert PHP 8.1+, et s'intègre nativement avec Passport (OAuth 2.1) et Sanctum (tokens API).
</p>

<h3>Installation</h3>

<pre><code class="language-bash">composer require laravel/mcp
php artisan vendor:publish --tag=ai-routes</code></pre>

<p>
  La commande <code>vendor:publish</code> crée le fichier <code>routes/ai.php</code>, l'équivalent de <code>routes/api.php</code> pour vos serveurs MCP.
</p>

<h2>Anatomie d'un serveur MCP</h2>

<p>
  Un serveur MCP est une classe PHP qui regroupe des tools, resources et prompts sous une même identité. Créez-en un :
</p>

<pre><code class="language-bash">php artisan make:mcp-server FlightServer</code></pre>

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
#[Instructions('Allows searching and booking flights, and accessing airline policies.')]
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

<p>Enregistrez ce serveur dans <code>routes/ai.php</code> :</p>

<pre><code class="language-php">use App\Mcp\Servers\FlightServer;
use Laravel\Mcp\Facades\Mcp;

// Serveur HTTP (POST)
Mcp::web('/mcp/flights', FlightServer::class);

// Avec middleware
Mcp::web('/mcp/flights', FlightServer::class)
    -&gt;middleware(['auth:sanctum', 'throttle:mcp']);</code></pre>

<h2>Les Tools : l'action au cœur de l'IA</h2>

<p>
  Les Tools sont la pièce maîtresse de MCP. Ils définissent ce que l'IA peut <em>faire</em> dans votre application. Chaque Tool expose un schéma JSON décrivant ses paramètres, un système de validation Laravel, et une méthode <code>handle()</code> qui exécute l'action.
</p>

<pre><code class="language-bash">php artisan make:mcp-tool SearchFlightsTool</code></pre>

<pre><code class="language-php">&lt;?php

namespace App\Mcp\Tools;

use App\Services\FlightService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\IsReadOnly;
use Laravel\Mcp\Server\Tool;

#[Description('Searches available flights between two airports for a given date.')]
#[IsReadOnly]
class SearchFlightsTool extends Tool
{
    public function __construct(
        protected FlightService $flights,
    ) {}

    public function handle(Request $request): Response
    {
        $validated = $request-&gt;validate([
            'origin'      =&gt; 'required|string|size:3',
            'destination' =&gt; 'required|string|size:3',
            'date'        =&gt; 'required|date|after:today',
            'passengers'  =&gt; 'integer|min:1|max:9',
        ]);

        $results = $this-&gt;flights-&gt;search(
            origin:      $validated['origin'],
            destination: $validated['destination'],
            date:        $validated['date'],
            passengers:  $validated['passengers'] ?? 1,
        );

        return Response::structured([
            'count'   =&gt; count($results),
            'flights' =&gt; $results,
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'origin' =&gt; $schema-&gt;string()
                -&gt;description('IATA code of the departure airport (e.g. ABJ for Abidjan).')
                -&gt;required(),

            'destination' =&gt; $schema-&gt;string()
                -&gt;description('IATA code of the arrival airport.')
                -&gt;required(),

            'date' =&gt; $schema-&gt;string()
                -&gt;description('Travel date in YYYY-MM-DD format.')
                -&gt;required(),

            'passengers' =&gt; $schema-&gt;integer()
                -&gt;description('Number of passengers (default: 1).')
                -&gt;default(1),
        ];
    }
}</code></pre>

<h3>Annotations de comportement</h3>

<p>
  Les annotations permettent aux clients MCP de comprendre la nature d'un Tool avant même de l'invoquer. Cela permet des optimisations (cache côté client, confirmation utilisateur, etc.) :
</p>

<ul>
  <li><code>#[IsReadOnly]</code> — Ne modifie pas l'état du système (lecture seule).</li>
  <li><code>#[IsIdempotent]</code> — Les appels répétés produisent le même résultat.</li>
  <li><code>#[IsDestructive]</code> — L'opération peut être irréversible.</li>
  <li><code>#[IsOpenWorld]</code> — Le Tool interagit avec des entités externes.</li>
</ul>

<h3>Types de réponses</h3>

<p>Un Tool peut renvoyer plusieurs formats de réponse selon le contexte :</p>

<pre><code class="language-php">// Texte simple
return Response::text('Vol trouvé : ABJ → CDG le 2026-10-15');

// Erreur métier
return Response::error('Aucun vol disponible pour cette date.');

// Réponse structurée (JSON typé)
return Response::structured(['price' =&gt; 485.00, 'currency' =&gt; 'EUR']);

// Image (radar météo, carte, etc.)
return Response::fromStorage('maps/abj-cdg.png', disk: 's3');

// Streaming (traitement de plusieurs éléments)
public function handle(Request $request): Generator
{
    foreach ($request-&gt;array('flight_ids') as $i =&gt; $id) {
        yield Response::notification('progress', [
            'current' =&gt; $i + 1,
            'total'   =&gt; count($request-&gt;array('flight_ids')),
        ]);
        yield Response::text($this-&gt;details($id));
    }
}</code></pre>

<h3>Enregistrement conditionnel</h3>

<p>
  Un Tool peut s'enregistrer uniquement pour certains utilisateurs, par exemple les abonnés premium :
</p>

<pre><code class="language-php">public function shouldRegister(Request $request): bool
{
    return $request?-&gt;user()?-&gt;hasActiveSubscription() ?? false;
}</code></pre>

<h2>Les Resources : le contexte au service de l'IA</h2>

<p>
  Les Resources permettent aux utilisateurs de partager du contenu avec l'IA. Contrairement aux Tools (déclenchés par l'IA), les Resources sont <em>sélectionnées par l'utilisateur</em> pour enrichir la conversation.
</p>

<pre><code class="language-bash">php artisan make:mcp-resource FlightPoliciesResource</code></pre>

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

<h3>Resources dynamiques (URI templates)</h3>

<p>
  Les URI templates permettent des Resources paramétrées, comme l'accès à une réservation spécifique par son ID :
</p>

<pre><code class="language-php">#[Description('Access a specific booking by its reference.')]
#[MimeType('application/json')]
class BookingResource extends Resource implements HasUriTemplate
{
    public function uriTemplate(): UriTemplate
    {
        return new UriTemplate('bookings://{reference}');
    }

    public function handle(Request $request): Response
    {
        $booking = Booking::where('reference', $request-&gt;get('reference'))
            -&gt;firstOrFail();

        return Response::text(json_encode($booking-&gt;toArray()));
    }
}</code></pre>

<h2>Les Prompts : des instructions réutilisables</h2>

<p>
  Les Prompts sont des templates de conversation que l'utilisateur peut invoquer depuis son client IA. Au lieu de retaper un prompt complexe à chaque fois, il le sélectionne dans une liste et renseigne des arguments.
</p>

<pre><code class="language-bash">php artisan make:mcp-prompt SummarizeItineraryPrompt</code></pre>

<pre><code class="language-php">&lt;?php

namespace App\Mcp\Prompts;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Prompts\Argument;
use Laravel\Mcp\Server\Attributes\Description;

#[Description('Generates a concise summary of a flight itinerary in the chosen language.')]
class SummarizeItineraryPrompt extends Prompt
{
    public function arguments(): array
    {
        return [
            new Argument(
                name: 'language',
                description: 'Summary language (e.g. "French", "English", "Spanish").',
                required: true,
            ),
            new Argument(
                name: 'style',
                description: 'Tone: formal or casual.',
                required: false,
            ),
        ];
    }

    public function handle(Request $request): array
    {
        $language = $request-&gt;string('language');
        $style    = $request-&gt;string('style', 'formal');

        return [
            Response::text(
                "You are a travel assistant. Summarize the following flight itinerary " .
                "in {$language} using a {$style} tone. Include departure times, durations, " .
                "stopovers, and total travel time."
            )-&gt;asAssistant(),
            Response::text("Here is the itinerary to summarize:"),
        ];
    }
}</code></pre>

<h2>Authentification : OAuth 2.1 et Sanctum</h2>

<p>
  Laravel MCP supporte deux mécanismes d'authentification out of the box.
</p>

<h3>Sanctum (tokens Bearer)</h3>

<p>La solution la plus simple pour une API interne ou des intégrations directes :</p>

<pre><code class="language-php">// routes/ai.php
Mcp::web('/mcp/flights', FlightServer::class)
    -&gt;middleware('auth:sanctum');
</code></pre>

<p>Le client fournit alors un header <code>Authorization: Bearer &lt;token&gt;</code>.</p>

<h3>OAuth 2.1 via Laravel Passport</h3>

<p>
  Pour les intégrations tierces (Claude Desktop, Cursor, applications clientes MCP), OAuth 2.1 est le standard recommandé. Laravel MCP génère automatiquement les routes d'autorisation nécessaires :
</p>

<pre><code class="language-php">// routes/ai.php
Mcp::oauthRoutes();

Mcp::web('/mcp/flights', FlightServer::class)
    -&gt;middleware('auth:api');</code></pre>

<pre><code class="language-php">// AppServiceProvider
use Laravel\Passport\Passport;

public function boot(): void
{
    Passport::authorizationView(function ($parameters) {
        return view('mcp.authorize', $parameters);
    });
}</code></pre>

<p>
  Le client MCP est ensuite redirigé vers votre page d'autorisation customisée, où l'utilisateur accepte les permissions demandées — exactement comme un flow OAuth classique.
</p>

<h3>Vérifications d'autorisation dans les primitives</h3>

<p>
  Quelle que soit la méthode d'authentification, vous pouvez vérifier les permissions directement dans vos Tools et Resources via <code>$request->user()</code> :
</p>

<pre><code class="language-php">public function handle(Request $request): Response
{
    if (! $request-&gt;user()-&gt;can('book-flights')) {
        return Response::error('Insufficient permissions to book flights.');
    }

    // ...
}</code></pre>

<h2>Mode Web vs Mode Local</h2>

<p>
  Laravel MCP supporte deux modes de transport.
</p>

<h3>Mode Web (HTTP + SSE)</h3>

<p>
  Le mode par défaut. Le serveur tourne sur votre infrastructure HTTP normale. Les clients se connectent via <code>HTTP POST</code> pour les requêtes et via <strong>Server-Sent Events (SSE)</strong> pour le streaming. Idéal pour les intégrations en production, les SaaS, les APIs publiques.
</p>

<pre><code class="language-php">Mcp::web('/mcp/flights', FlightServer::class);</code></pre>

<h3>Mode Local (stdio)</h3>

<p>
  Le serveur est exposé comme une commande Artisan, communiquant via stdin/stdout. Parfait pour les outils de développement locaux, les IDE comme Cursor et VS Code, ou pour tester vos intégrations en local.
</p>

<pre><code class="language-php">// routes/ai.php
Mcp::local('flights', FlightServer::class);</code></pre>

<pre><code class="language-bash"># Démarrage du serveur local
php artisan mcp:start flights</code></pre>

<p>
  Dans <code>.mcp.json</code> d'un projet Cursor, cela donne :
</p>

<pre><code class="language-json">{
  "mcpServers": {
    "flights": {
      "command": "php",
      "args": ["artisan", "mcp:start", "flights"]
    }
  }
}</code></pre>

<h2>Le Client MCP : consommer des serveurs externes</h2>

<p>
  Laravel MCP inclut également un <strong>client</strong> pour consommer des serveurs MCP tiers depuis votre application. Vous pouvez ainsi orchestrer plusieurs serveurs MCP — le vôtre, ceux de partenaires, ou ceux de services externes.
</p>

<pre><code class="language-php">use Laravel\Mcp\Client;
use Laravel\Mcp\Facades\Mcp;

// Enregistrement
Mcp::registerClient('weather', fn () =&gt; Client::web('https://weather.example.com/mcp'));

// Utilisation
$client = Mcp::client('weather');

// Lister les tools disponibles
$tools = $client-&gt;tools();

// Appeler un tool
$result = $client-&gt;callTool('get-forecast', ['city' =&gt; 'Abidjan']);
echo $result-&gt;text(); // "Partly cloudy, 31°C"

// Lire une resource
$result = $client-&gt;readResource('weather://guidelines');

// Obtenir un prompt
$result = $client-&gt;getPrompt('weather-summary', ['tone' =&gt; 'formal']);</code></pre>

<p>
  Pour les serveurs protégés, le client supporte les tokens Bearer et OAuth 2.1 :
</p>

<pre><code class="language-php">Mcp::registerClient('partner', fn () =&gt;
    Client::web('https://partner.com/mcp')
        -&gt;withToken(fn () =&gt; auth()-&gt;user()-&gt;partnerToken())
);</code></pre>

<h2>Cache et performances</h2>

<p>
  Laravel MCP expose un système de cache hints conforme à la spec MCP. Ces indices permettent aux clients de cacher intelligemment les listes de Tools et Resources côté client, réduisant la latence des premières interactions :
</p>

<pre><code class="language-php">use Laravel\Mcp\Enums\CacheScope;
use Laravel\Mcp\Server\Attributes\Cacheable;

#[Cacheable(ttlMs: 60_000, scope: CacheScope::Public)]
class FlightServer extends Server
{
    protected function cacheHints(): array
    {
        return [
            'tools/list' =&gt; new Cacheable(ttlMs: 30_000, scope: CacheScope::Public),
        ];
    }
}</code></pre>

<h2>MCP Apps : des interfaces interactives dans l'IA</h2>

<p>
  Une des fonctionnalités les plus innovantes de Laravel MCP est le support des <strong>MCP Apps</strong> : des mini-applications HTML rendues directement dans le client IA. L'utilisateur peut interagir avec une vraie UI sans quitter son assistant.
</p>

<pre><code class="language-bash">php artisan make:mcp-app-resource FlightDashboardApp</code></pre>

<pre><code class="language-php">#[Description('Interactive flight search dashboard.')]
#[AppMeta(
    connectDomains: ['https://api.flightio.com'],
    libraries: [Library::Tailwind, Library::Alpine],
)]
class FlightDashboardApp extends AppResource
{
    public function handle(Request $request): Response
    {
        return Response::view('mcp.flight-dashboard', [
            'title' =&gt; $this-&gt;title(),
        ]);
    }
}</code></pre>

<p>La vue Blade associée peut appeler directement les Tools du serveur via JavaScript :</p>

<pre><code class="language-html">&lt;x-mcp::app :title="$title"&gt;
    &lt;x-slot:head&gt;
        &lt;script type="module"&gt;
        createMcpApp(async (app) =&gt; {
            document.getElementById('search-btn').addEventListener('click', async () =&gt; {
                const result = await app.callServerTool('search-flights', {
                    origin: document.getElementById('origin').value,
                    destination: document.getElementById('destination').value,
                    date: document.getElementById('date').value,
                });
                renderResults(JSON.parse(result.content[0]?.text ?? '{}'));
            });
        });
        &lt;/script&gt;
    &lt;/x-slot:head&gt;

    &lt;div id="app"&gt;
        &lt;input id="origin" placeholder="Départ (ex: ABJ)" /&gt;
        &lt;input id="destination" placeholder="Arrivée (ex: CDG)" /&gt;
        &lt;input id="date" type="date" /&gt;
        &lt;button id="search-btn"&gt;Rechercher&lt;/button&gt;
        &lt;div id="results"&gt;&lt;/div&gt;
    &lt;/div&gt;
&lt;/x-mcp::app&gt;</code></pre>

<h2>Tests</h2>

<h3>MCP Inspector</h3>

<p>
  L'Inspector est un outil de debug intégré qui permet d'explorer votre serveur MCP en temps réel, de lister ses Tools/Resources/Prompts, et d'exécuter des appels manuels depuis le terminal :
</p>

<pre><code class="language-bash"># Inspecter un serveur web
php artisan mcp:inspector mcp/flights

# Inspecter un serveur local
php artisan mcp:inspector flights</code></pre>

<h3>Tests unitaires avec Pest ou PHPUnit</h3>

<p>
  Laravel MCP expose une API de test fluide directement sur la classe du serveur :
</p>

<pre><code class="language-php">// Pest
test('search flights returns structured results', function () {
    $response = FlightServer::tool(SearchFlightsTool::class, [
        'origin'      =&gt; 'ABJ',
        'destination' =&gt; 'CDG',
        'date'        =&gt; now()-&gt;addDays(30)-&gt;format('Y-m-d'),
    ]);

    $response
        -&gt;assertOk()
        -&gt;assertSee('ABJ');
});

// Utilisateur authentifié
test('booking tool requires auth', function () {
    $response = FlightServer::actingAs(User::factory()-&gt;create())
        -&gt;tool(BookFlightTool::class, [...]);

    $response-&gt;assertOk();
});</code></pre>

<h2>Cas d'usage concrets</h2>

<p>Voici quelques exemples concrets d'applications Laravel qui peuvent tirer parti de MCP :</p>

<ul>
  <li>
    <strong>SaaS de gestion de projet</strong> : exposer des Tools pour créer des tickets, assigner des tâches, générer des rapports de sprint. L'IA peut ainsi gérer le projet directement depuis Claude ou Cursor.
  </li>
  <li>
    <strong>E-commerce</strong> : Tools pour rechercher des produits, passer des commandes, suivre des livraisons. Resources pour les conditions générales de vente, les politiques de retour.
  </li>
  <li>
    <strong>CRM</strong> : Tools pour créer des contacts, envoyer des emails, consulter l'historique client. Prompts pour générer des emails de relance ou des résumés d'appels.
  </li>
  <li>
    <strong>Plateforme communautaire (comme Laravel CI)</strong> : Tools pour créer des articles, répondre à des questions du forum, s'inscrire à des événements. Resources pour accéder aux ressources éducatives.
  </li>
  <li>
    <strong>DevOps / Infrastructure</strong> : Tools pour déclencher des déploiements, lire des logs, scaler des instances. L'IA devient un copilote d'infrastructure.
  </li>
</ul>

<h2>Laravel Boost : la preuve par l'exemple</h2>

<p>
  Le package a été field-tested en powering <strong>Laravel Boost</strong>, le service d'optimisation et de conseil proposé par l'équipe Laravel. Cela démontre qu'il est production-ready et conçu pour des cas d'usage réels, pas seulement des démos.
</p>

<p>
  L'équipe Laravel a également publié <strong>Locket</strong>, une application de démonstration open-source qui expose les trois interfaces modernes d'une app : web, JSON API, et serveur MCP. Le code source est disponible sur GitHub (<a href="https://github.com/laravel/locket" target="_blank" rel="noopener">github.com/laravel/locket</a>) et constitue une excellente référence d'architecture.
</p>

<h2>Conclusion</h2>

<p>
  MCP n'est pas une mode. C'est une infrastructure. De la même façon que les APIs REST ont ouvert les applications web à des intégrations infinies dans les années 2010, MCP ouvre vos applications aux agents IA en 2025.
</p>

<p>
  La différence ? Le rythme d'adoption est bien plus rapide. Avec plus d'un milliard d'utilisateurs envoyant plus de 20 milliards de messages par semaine aux assistants IA, chaque semaine passée sans MCP est une semaine où vos utilisateurs ne peuvent pas accéder à votre application depuis leur interface préférée.
</p>

<p>
  Laravel MCP donne aux développeurs du framework les outils pour prendre ce train en marche — avec l'élégance et la productivité auxquelles l'écosystème Laravel nous a habitués. <strong>Tools, Resources, Prompts, OAuth 2.1, streaming, testing</strong> : tout y est, prêt à l'emploi.
</p>

<pre><code class="language-bash">composer require laravel/mcp</code></pre>

<p>
  C'est tout ce qu'il faut pour commencer. Le reste, c'est votre imagination.
</p>
HTML;
    }
}
