<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use App\Services\Events\EventMediaService;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class LaravelCiMeet01Seeder extends Seeder
{
    // ── IDs Google Drive des photos de l'événement ─────────────────────────
    private const DRIVE_PHOTO_IDS = [
        '1hY7Yh58lDcHgv0Rw8BwK17b9TUnHSpnk',
        '1WEcs2MoOsK0qv2EuvtWmTWkGvWlMHWzC',
        '1kG9fY9Prp8_zUeS93PKuPL9D8HFHbJZW',
        '15FLQ0AqSIWlWW6csN-K4ER3t6r6Y1ynV',
        '1skVk__OcUFtueskTaLsaaDw4EfZ9k4r4',
        '1Cq-Uur-VzHtSYfLAiqjEr1t6T_kzpIBS',
        '1rzHapflXyBkE3ERgk4L64gVchVmuXyp3',
        '1NbHaNz0vk4A3FQy6ByLZ4TPggFQtsCCN',
        '17aueHaLF0fB_zBhwQTJ5mcnN-peqXDIw',
        '1IT1BV-bs6GzL9KBdN_pkY2c-N-zQrObm',
        '1gEvlS7r5jDZT33ozDF6ZyBI149ciZRso',
        '1zSojlxtSj81V-KZpX_1Y9h5kwoCzQ6Ey',
        '1-zMg3VczeHW2daK9tOBKDttU3LwkAE1i',
        '1TMo3egab-ejhJWfpTwIlImTiSqlSjuy_',
    ];

    public function run(EventMediaService $mediaService): void
    {
        $creator = User::where('github_id', '167759591')->first()
            ?? User::first();

        if ($creator === null) {
            $this->command->warn('LaravelCiMeet01Seeder : aucun utilisateur trouvé, abandon.');
            return;
        }

        // ── 1. Sync cover + document vers le disk assets ───────────────────
        $coverPath  = null;
        $docPath    = $this->syncDocumentToAssets('documents/events/Design-program.pdf');

        // ── 2. Créer / mettre à jour l'événement ──────────────────────────
        $event = Event::updateOrCreate(
            ['slug' => 'laravel-ci-meet-01'],
            [
                'created_by'   => $creator->id,
                'title'        => 'Laravel CI Meet #01',
                'description'  => <<<'HTML'
                    <h2>La première grande rencontre de la communauté Laravel CI</h2>
                    <p>
                        Le <strong>12 septembre 2026</strong>, Epitech Abidjan accueille la toute première édition
                        du <strong>Laravel CI Meet</strong> — un événement inédit en Côte d'Ivoire dédié à l'écosystème
                        Laravel et à l'intelligence artificielle.
                    </p>
                    <p>
                        Autour du thème <em>« L'IA au service des développeurs Laravel »</em>, des speakers de renom
                        partagent leurs expériences, démos live et retours terrain devant une salle de passionnés.
                    </p>
                    <h3>Pourquoi participer ?</h3>
                    <ul>
                        <li>Découvrir comment intégrer l'IA dans vos projets Laravel concrets</li>
                        <li>Rencontrer d'autres développeurs ivoiriens passionnés par Laravel</li>
                        <li>Assister à des démos live sur des projets réels</li>
                        <li>Participer à une table ronde sur l'avenir du développement augmenté</li>
                        <li>Rejoindre officiellement la communauté Laravel CI</li>
                    </ul>
                    <p><strong>Entrée gratuite — inscription obligatoire, places limitées.</strong></p>
                    HTML,

                'program' => <<<'HTML'
                    <ul>
                        <li><strong>09h30</strong> — Accueil des participants</li>
                        <li><strong>10h15</strong> — <em>Wilson Kouassi</em> · Le Hub Laravel CI : vision, communauté et avenir</li>
                        <li><strong>10h30</strong> — Ouverture officielle de l'événement</li>
                        <li><strong>11h00</strong> — <em>Boubacar LY</em> · Votre API a un nouveau client : l'agent IA</li>
                        <li><strong>11h45</strong> — <em>Rygel Louv</em> · Du développeur assisté à l'équipe augmentée</li>
                        <li><strong>12h30</strong> — Pause déjeuner &amp; networking</li>
                        <li><strong>14h00</strong> — <em>Mahamadou Diaby (KEPSON)</em> · IA et productivité Laravel</li>
                        <li><strong>14h45</strong> — <em>Doro Gueye</em> · From App to Agent — démo live</li>
                        <li><strong>15h30</strong> — Pause</li>
                        <li><strong>16h00</strong> — Table ronde : l'IA va-t-elle remplacer le développeur ?</li>
                        <li><strong>17h30</strong> — Photo de famille &amp; networking</li>
                        <li><strong>18h15</strong> — Clôture officielle</li>
                    </ul>
                    HTML,

                'type'                       => 'conference',
                'location'                   => 'Epitech Abidjan, Riviera 2, Abidjan',
                'online_url'                 => null,
                'cover_image'                => $coverPath,
                'starts_at'                  => '2026-09-12 10:30:00',
                'ends_at'                    => '2026-09-12 18:15:00',
                'capacity'                   => 100,
                'waitlist_enabled'           => false,
                'status'                     => 'completed',
                'is_paid'                    => false,
                'price'                      => null,
                'currency'                   => null,
                'promo_code'                 => null,
                'promo_discount_type'        => null,
                'promo_discount_value'       => null,
                'promo_expires_at'           => null,
                'promo_max_uses'             => null,
                'promo_uses_count'           => 0,
                'ticketing_enabled'          => true,
                'ticket_prefix'              => 'LCI',
                'guest_registration_enabled' => true,
                'reminder_7d_sent'           => true,
                'reminder_1d_sent'           => true,

                // ── Recap post-événement ───────────────────────────────────
                'recap_summary' => 'Le 12 septembre 2026, la communauté Laravel CI a tenu sa première édition du Laravel CI Meet à Epitech Abidjan. Plus de 80 développeurs réunis autour du thème « L\'IA au service des développeurs Laravel » ont exploré, débattu et expérimenté pendant une journée intense de talks, de démos live et d\'échanges en panel — marquant ainsi le lancement officiel de la communauté.',

                'recap_content' => <<<'HTML'
                    <h2>Le matin : l'effervescence avant le grand départ</h2>
                    <p>
                        Dès 9h30, les premiers participants franchissaient les portes d'Epitech Abidjan, situé en
                        Riviera 2. Le soleil de septembre filtrait à travers les baies vitrées, l'équipe d'accueil
                        distribuait les badges, et dans la salle principale les chaises se remplissaient
                        progressivement d'un public mêlant développeurs juniors, tech leads, freelances,
                        étudiants en informatique et entrepreneurs du numérique. Les retours que nous avons recueillis
                        ce matin-là disaient tous la même chose : beaucoup n'avaient jamais participé à un événement
                        tech aussi bien organisé en Côte d'Ivoire. La barre était placée haut — dès l'accueil.
                    </p>
                    <p>
                        Sur les tables, des exemplaires du programme officiel, co-conçu avec nos partenaires.
                        Les sponsors — <strong>Future AKademy</strong>, <strong>Innotec Digital</strong>,
                        <strong>Epitech</strong> et d'autres — avaient contribué à rendre la journée
                        entièrement gratuite, une décision assumée par la communauté pour abaisser toutes les
                        barrières à l'entrée.
                    </p>

                    <h2>10h15 — Wilson Kouassi : le Hub Laravel CI, vision et fondations</h2>
                    <p>
                        C'est Wilson Kouassi, co-fondateur et lead de la communauté, qui a ouvert les hostilités.
                        En quelques diapositives limpides, il a posé le contexte : pourquoi Laravel CI ? Pourquoi
                        maintenant ? Et pourquoi un Hub communautaire plutôt qu'un simple groupe Telegram de plus ?
                    </p>
                    <p>
                        Le constat de départ est clair. Laravel est le framework PHP le plus populaire au monde,
                        mais la Côte d'Ivoire n'avait pas encore de communauté structurée, de lieu de rencontre
                        régulier ni de ressources francophones adaptées aux réalités du marché local.
                        Le Hub Laravel CI ambitionne de combler ce vide en articulant trois axes :
                        <strong>apprendre</strong> (tutoriels, articles, conférences),
                        <strong>connecter</strong> (offres d'emploi, mise en relation entreprises / développeurs)
                        et <strong>construire</strong> (projets open-source, hackathons, formations).
                    </p>
                    <p>
                        La feuille de route annoncée prévoit des meetups réguliers, un podcast, des défis coding
                        mensuels et une newsletter hebdomadaire. « On construit pas juste une app, on construit
                        une infrastructure humaine », a-t-il conclu sous les applaudissements.
                    </p>

                    <h2>10h30 — Ouverture officielle</h2>
                    <p>
                        L'ouverture officielle a réuni sur scène les représentants des partenaires et l'équipe
                        organisatrice. Quelques mots sur la place du numérique dans le développement économique
                        ivoirien, sur l'importance des communautés techniques locales, et la journée était
                        officiellement lancée. Depuis les premiers rangs jusqu'au fond de la salle, la salle était
                        pleine — plus de <strong>80 participants</strong> comptabilisés.
                    </p>

                    <h2>11h00 — Boubacar LY : « Votre API a un nouveau client — l'agent IA »</h2>
                    <p>
                        Premier grand talk technique de la journée, et peut-être le plus attendu. Boubacar LY a
                        abordé une problématique concrète que peu de développeurs avaient envisagée sous cet angle :
                        comment concevoir une API Laravel pour qu'elle soit consommée non pas par un humain ou par
                        une application traditionnelle, mais par un <em>agent IA autonome</em> ?
                    </p>
                    <p>
                        La différence, explique-t-il, est fondamentale. Un humain tolère l'ambiguïté, comprend une
                        documentation incomplète, sait interpréter un message d'erreur flou. Un agent IA, lui,
                        a besoin d'une sémantique précise, de contrats stricts, de réponses prévisibles.
                        Boubacar a parcouru plusieurs patterns concrets :
                    </p>
                    <ul>
                        <li><strong>Les descriptions sémantiques enrichies</strong> dans les routes et les contrôleurs,
                            qui permettent à un agent d'inférer l'intention d'un endpoint sans lire la doc.</li>
                        <li><strong>L'authentification par clé API dédiée par agent</strong>, avec des scopes
                            limités au strict nécessaire — principe de moindre privilège appliqué aux agents.</li>
                        <li><strong>Les réponses hypermédias</strong> (style HATEOAS) qui guident l'agent vers
                            les prochaines actions possibles, réduisant le risque d'hallucination.</li>
                        <li><strong>Les webhooks bidirectionnels</strong> pour les opérations longues, évitant les
                            timeouts et les boucles de polling coûteuses.</li>
                    </ul>
                    <p>
                        La démonstration live, sur une API Laravel 12 fraîchement générée via Artisan, a convaincu
                        même les sceptiques. La salle posait des questions techniques pointues — signe d'un public
                        très engagé.
                    </p>
                    <blockquote>
                        « Le prochain client de votre API, il ne lira pas votre doc Swagger. Il va l'inférer.
                        Autant l'y aider. »
                        <cite>— Boubacar LY</cite>
                    </blockquote>

                    <h2>11h45 — Rygel Louv : « Du développeur assisté à l'équipe augmentée »</h2>
                    <p>
                        Rygel Louv, développeur PHP senior, a apporté une perspective plus humaine et organisationnelle.
                        Son talk est parti d'un constat personnel : en 2024, il utilisait GitHub Copilot comme
                        simple autocomplétion. En 2025, son équipe de 4 développeurs avait réorganisé entièrement
                        ses workflows autour de l'IA — et livrait deux fois plus vite.
                    </p>
                    <p>
                        Comment passe-t-on de « l'IA m'aide à écrire du code » à « l'IA est un membre actif de
                        l'équipe » ? Rygel a identifié trois étapes de maturité :
                    </p>
                    <ul>
                        <li><strong>Niveau 1 — L'assistant</strong> : autocomplétion, génération de snippets,
                            refactoring ponctuel. La productivité individuelle augmente de 15 à 25 %.</li>
                        <li><strong>Niveau 2 — Le copilote</strong> : revues de code automatisées, génération de
                            tests, rédaction de documentation. L'équipe économise 30 % de temps sur les tâches
                            répétitives.</li>
                        <li><strong>Niveau 3 — L'équipe augmentée</strong> : pipelines CI/CD alimentés par des
                            agents, triage automatique des issues, première implémentation de features générée
                            puis révisée par un humain. Le rôle du développeur glisse vers l'architecture et la
                            validation.</li>
                    </ul>
                    <p>
                        Il a insisté sur les pièges à éviter : la confiance aveugle dans le code généré, le
                        rétrécissement des compétences si l'on n'entretient plus les fondamentaux, et la
                        difficulté à estimer les tâches quand les LLMs rendent certaines choses triviales mais
                        d'autres mystérieusement plus longues.
                    </p>
                    <blockquote>
                        « Ce qui change le plus, c'est pas la vitesse de frappe. C'est le niveau de la
                        conversation qu'on peut avoir avec sa propre codebase. »
                        <cite>— Rygel Louv</cite>
                    </blockquote>

                    <h2>12h30 — Pause déjeuner : le réseau se tisse dans les couloirs</h2>
                    <p>
                        La pause déjeuner a duré 1h30 — volontairement longue, parce que dans ce genre d'événement,
                        les meilleures conversations ne se planifient pas. Des tablées mêlant participants et
                        speakers se sont formées naturellement. On a vu des cartes de visite s'échanger, des
                        GitHub s'ajouter mutuellement, des projets freelance naître autour d'une assiette de riz.
                    </p>
                    <p>
                        L'équipe organisatrice a aussi profité de ce moment pour recueillir les premiers retours :
                        le niveau technique des talks était jugé « très bon » ou « excellent » par la quasi-totalité
                        des personnes interrogées. Beaucoup demandaient déjà la date du prochain Meet.
                    </p>

                    <h2>14h00 — Mahamadou Diaby (KEPSON) : « IA et productivité Laravel »</h2>
                    <p>
                        Après le déjeuner, place aux cas concrets. Mahamadou Diaby, connu sous le pseudo
                        <strong>KEPSON</strong>, a présenté un retour d'expérience sur trois projets Laravel
                        réels dans lesquels il a intégré des outils IA pour gagner en productivité.
                    </p>
                    <p>
                        Premier exemple : la génération automatisée de migrations et de modèles à partir d'un
                        cahier des charges en langage naturel. En décrivant le schéma de données à un LLM
                        bien prompté, KEPSON obtient en quelques secondes un scaffold complet — migrations,
                        modèles Eloquent, factories, seeders. Gain estimé : 2 à 3 heures par entité.
                    </p>
                    <p>
                        Deuxième exemple : la détection de dette technique. Un agent analyse le code existant,
                        identifie les N+1 queries, les méthodes trop longues, les violations de principes SOLID,
                        et propose des refactorings priorisés. « C'est comme avoir un tech lead junior disponible
                        24h/24, qui ne se vexe pas quand tu ignores ses conseils. »
                    </p>
                    <p>
                        Troisième exemple, le plus impressionnant : la génération de suites de tests Pest à partir
                        de contrôleurs existants. L'agent comprend les routes, infère les cas nominaux et les
                        cas d'erreur, et produit des tests fonctionnels que KEPSON estime couvrir 70 % des
                        scénarios critiques sans intervention humaine.
                    </p>
                    <p>
                        Une mise en garde importante a clôturé le talk : ces outils ne remplacent pas la
                        compréhension du domaine métier. Ils font le scaffold ; à l'humain de valider la logique.
                    </p>

                    <h2>14h45 — Doro Gueye : « From App to Agent » — la démo qui a scotché la salle</h2>
                    <p>
                        Le talk le plus attendu de l'après-midi, et sans doute le plus spectaculaire. Doro Gueye
                        a présenté en direct son projet open-source
                        <strong><a href="https://github.com/hadjidoro/from-app-to-agent">from-app-to-agent</a></strong>,
                        une démonstration concrète de ce que peut faire un agent IA intégré dans une application
                        Laravel standard.
                    </p>
                    <p>
                        Le principe : une application Laravel classique de gestion de données (dans ce cas, un
                        système d'analyse de rapports financiers) à laquelle on greffe une couche d'IA capable
                        de comprendre des requêtes en langage naturel, d'exécuter les bonnes actions sur la base
                        de données, de synthétiser les résultats et de répondre à l'utilisateur sous forme de
                        rapport structuré.
                    </p>
                    <p>
                        En live, Doro a tapé dans une interface conversationnelle : « Montre-moi les trois
                        clients dont le chiffre d'affaires a le plus baissé ce trimestre, et suggère des
                        actions correctives. » En quelques secondes, l'agent a exécuté les requêtes Eloquent
                        correspondantes, analysé les résultats, et produit un rapport clair avec des
                        recommandations contextualisées.
                    </p>
                    <p>
                        La salle a littéralement retenu son souffle. Puis les questions ont fusé :
                        comment gérer la sécurité ? Comment éviter les injections de prompt ? Comment
                        limiter les actions de l'agent ? Doro a répondu à chaque question avec précision,
                        montrant qu'il avait anticipé ces enjeux dans son architecture.
                    </p>
                    <blockquote>
                        « Une app, ça répond à ce qu'on lui demande. Un agent, ça comprend ce qu'on
                        veut — et parfois, il le fait avant qu'on lui demande. »
                        <cite>— Doro Gueye</cite>
                    </blockquote>

                    <h2>15h30 — Pause et décompression</h2>
                    <p>
                        Une courte pause a permis à l'assistance de souffler, d'échanger leurs impressions
                        sur les talks, de saisir l'occasion de parler aux speakers en tête-à-tête. L'ambiance
                        était électrique — tout le monde sentait que la table ronde qui approchait allait
                        être mémorable.
                    </p>

                    <h2>16h00 — Table ronde : « L'IA va-t-elle remplacer le développeur ? »</h2>
                    <p>
                        La question qui tue, posée avec le sourire. Tous les speakers du jour ont pris place
                        sur scène, rejoint par plusieurs participants qui avaient demandé à intervenir.
                        Le modérateur a posé trois questions fil-conducteur :
                    </p>
                    <ol>
                        <li>En 2030, quel pourcentage du code d'une application Laravel standard sera-t-il
                            écrit par une IA ?</li>
                        <li>Quelles compétences le développeur doit-il absolument préserver ou renforcer
                            face à l'IA ?</li>
                        <li>Quelle est la responsabilité des communautés tech dans la transition ?</li>
                    </ol>
                    <p>
                        Sur la première question, les estimations ont varié de 40 % à 90 % selon les
                        intervenants — avec un consensus autour de 60 à 70 % pour le code de glue et
                        de scaffold, mais bien moins pour la logique métier complexe, les décisions
                        d'architecture et l'intégration de domaines nouveaux.
                    </p>
                    <p>
                        Sur la deuxième, l'unanimité était plus forte : la compréhension du domaine
                        métier, la capacité à poser les bonnes questions, la pensée systémique, le
                        sens de la sécurité, et — surtout — l'empathie utilisateur. « L'IA optimise
                        pour ce qu'on lui dit d'optimiser. Encore faut-il savoir quoi optimiser. »
                    </p>
                    <p>
                        Sur la troisième question, la communauté Laravel CI elle-même a été citée comme
                        exemple : créer des espaces où les développeurs peuvent apprendre à travailler
                        <em>avec</em> l'IA plutôt que de la subir, partager les expériences, documenter
                        les échecs autant que les succès, et former la prochaine génération à ces nouveaux
                        paradigmes.
                    </p>
                    <blockquote>
                        « L'IA ne va pas remplacer les développeurs. Elle va remplacer les développeurs
                        qui ne savent pas utiliser l'IA. »
                        <cite>— Citation unanimement approuvée par la salle</cite>
                    </blockquote>
                    <p>
                        Les échanges ont duré près d'une heure trente, jusqu'à 17h30, ponctués de rires,
                        de désaccords sains et de quelques révélations — dont celle de KEPSON, qui a avoué
                        que 40 % du code de son dernier projet livré en production avait été généré par IA,
                        contre 5 % l'année précédente.
                    </p>

                    <h2>17h30 — Photo de famille et networking</h2>
                    <p>
                        À 17h30, toute l'assistance s'est retrouvée pour la photo officielle de groupe —
                        un moment symbolique fort. Derrière les sourires, on sentait quelque chose de plus
                        grand : la conscience collective d'avoir participé à quelque chose d'historique
                        pour le développement numérique en Côte d'Ivoire.
                    </p>
                    <p>
                        Le networking post-photo a été dense. Des groupes se sont formés spontanément,
                        des idées de projets collaboratifs ont émergé, des contacts ont été échangés.
                        Plusieurs participants ont rejoint la communauté Laravel CI sur place, via le
                        Hub, en quelques gestes sur leur téléphone.
                    </p>

                    <h2>18h15 — Clôture officielle</h2>
                    <p>
                        Wilson Kouassi a pris le micro une dernière fois pour clôturer la journée.
                        Des remerciements sincères aux speakers, aux partenaires, aux bénévoles de
                        l'équipe organisatrice, et surtout aux participants — sans eux, une communauté
                        n'est qu'une idée. Avec eux, elle devient réelle.
                    </p>
                    <p>
                        Il a laissé la salle sur une promesse : le Laravel CI Meet #01 n'était qu'un début.
                        La prochaine édition est dans les cartons, plus grande, plus ouverte, avec encore
                        plus de speakers et de partenaires. La communauté Laravel CI est là, elle grandit,
                        et elle a montré ce soir qu'elle représente quelque chose de sérieux pour
                        l'écosystème tech ivoirien.
                    </p>

                    <h2>Ce qu'on retient de cette première édition</h2>
                    <ul>
                        <li><strong>Plus de 80 participants</strong> présents dans la salle d'Epitech Abidjan.</li>
                        <li><strong>5 talks</strong> techniques de haut niveau, tous disponibles en compte rendu
                            dans cet article.</li>
                        <li><strong>1 table ronde</strong> de 90 minutes qui a cristallisé les enjeux de la
                            transformation IA pour les développeurs francophones.</li>
                        <li><strong>Des dizaines de nouvelles connexions</strong> et de projets collaboratifs
                            nés dans les couloirs.</li>
                        <li><strong>Un consensus</strong> : la communauté Laravel CI répond à un vrai besoin,
                            et elle vient de prouver qu'elle peut livrer des événements de qualité internationale.</li>
                    </ul>
                    <p>
                        Merci à toutes et à tous d'avoir été là.
                        À bientôt pour le Laravel CI Meet #02 — inscris-toi sur le Hub pour ne pas le manquer.
                    </p>
                    HTML,

                'recap_document_name' => 'Programme officiel — Laravel CI Meet #01',
                'recap_document_path' => $docPath,
                'recap_published_at'  => now(),
                'recap_published_by'  => $creator->id,
            ]
        );

        $this->command->info("LaravelCiMeet01Seeder : événement '{$event->title}' OK.");

        // ── 3. Import des photos depuis Google Drive ───────────────────────
        $this->importDrivePhotos($event, $mediaService);
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    /**
     * Copie la cover depuis public/assets vers le disk assets.
     * Retourne le chemin stocké dans la DB (relatif au disk).
     */
    private function syncCoverToAssets(): ?string
    {
        $local  = public_path('assets/web/img/mascot.png');
        $target = 'web/img/events/laravel-ci-meet-01-cover.png';

        if (! file_exists($local)) {
            $this->command->warn('  Cover locale introuvable — cover_image laissée à null.');
            return null;
        }

        if (! Storage::disk('assets')->exists($target)) {
            Storage::disk('assets')->put($target, file_get_contents($local));
        }

        return $target;
    }

    /**
     * Sync un fichier local public/assets vers le disk assets.
     * Retourne le basename stocké dans la DB (la méthode recapDocumentUrl()
     * préfixe déjà "documents/events/").
     */
    private function syncDocumentToAssets(string $relativePath): ?string
    {
        $local  = public_path('assets/' . $relativePath);
        $target = $relativePath;                          // chemin complet sur disk assets

        if (! file_exists($local)) {
            $this->command->warn("  Document local introuvable : {$local}");
            return null;
        }

        if (! Storage::disk('assets')->exists($target)) {
            Storage::disk('assets')->put($target, file_get_contents($local));
        }

        // recapDocumentUrl() fait : 'documents/events/' . $path
        // → on stocke seulement le basename pour éviter la double-préfixation
        return basename($relativePath);
    }

    /**
     * Télécharge chaque photo depuis Drive et l'upload via EventMediaService.
     * Passe silencieusement les photos déjà présentes (idempotent).
     */
    private function importDrivePhotos(Event $event, EventMediaService $service): void
    {
        $existing = $event->media()->pluck('original_name')->toArray();
        $ok = 0; $skipped = 0; $errors = 0;

        foreach (self::DRIVE_PHOTO_IDS as $fileId) {
            $url = "https://drive.google.com/uc?export=download&id={$fileId}&confirm=1";

            $this->command->line("  → Drive {$fileId}…");

            try {
                $response = Http::timeout(90)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                    ->get($url);

                if (! $response->successful()) {
                    $this->command->warn("    ✗ HTTP {$response->status()}");
                    $errors++;
                    continue;
                }

                // Nom depuis Content-Disposition ou fallback
                preg_match('/filename[^;=\n]*=([\'"]*)(.*)\1/', $response->header('Content-Disposition') ?? '', $m);
                $originalName = isset($m[2]) && $m[2] !== ''
                    ? preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($m[2]))
                    : "{$fileId}.jpg";

                if (in_array($originalName, $existing, true)) {
                    $this->command->line("    ↷ Déjà importé : {$originalName}");
                    $skipped++;
                    continue;
                }

                $tmp  = sys_get_temp_dir() . '/' . uniqid('lci_', true) . '_' . $originalName;
                file_put_contents($tmp, $response->body());

                $mime = mime_content_type($tmp) ?: 'image/jpeg';
                $file = new UploadedFile($tmp, $originalName, $mime, null, true);

                $service->uploadPhoto($event, $file);

                @unlink($tmp);
                $ok++;
                $this->command->info("    ✓ {$originalName}");

            } catch (\Exception $e) {
                $this->command->error("    ✗ {$fileId} : {$e->getMessage()}");
                $errors++;
            }
        }

        $this->command->newLine();
        $this->command->info("Photos : {$ok} importées · {$skipped} passées · {$errors} erreurs");
    }
}
