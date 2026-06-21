# 🐘 Plateforme Laravel Côte d'Ivoire

Hub communautaire officiel — Forum · Blog · Événements · Job Board · Admin

> Construit par des développeurs ivoiriens, pour des développeurs ivoiriens.

---

## 📋 Table des matières

- [Stack technique](#-stack-technique)
- [Équipe et branches](#-équipe-et-branches)
- [Architecture du projet](#-architecture-du-projet)
- [Workflow Git — Lead (Wilson)](#-workflow-git--lead-wilson)
- [Workflow Git — Autres développeurs](#-workflow-git--autres-développeurs)
- [Ouvrir une Pull Request](#-ouvrir-une-pull-request)
- [Review et merge — rôle du Lead](#-review-et-merge--rôle-du-lead)
- [Droits et protections des branches](#-droits-et-protections-des-branches)
- [Nomenclature et conventions de code](#-nomenclature-et-conventions-de-code)
- [Pattern : Service · Controller · Form Request](#-pattern--service--controller--form-request)
- [Installation locale](#-installation-locale)
- [Standards de qualité](#-standards-de-qualité)

---

## 🛠 Stack technique

| Composant | Technologie | Version |
|---|---|---|
| Back-end | Laravel | ^13.0 |
| PHP | PHP | ^8.3 |
| Front-end | Livewire | ^4.0 |
| Admin | Filament | ^5.0 |
| Base de données | MySQL | 8+ |
| Auth | Laravel Socialite (GitHub) | ^5.0 |
| Permissions | Spatie Laravel Permission | ^6.0 |
| Temps réel | Laravel Reverb + Echo | — |
| Recherche | Laravel Scout + Meilisearch | — |
| Queue | Laravel Horizon | — |
| CSS | TailwindCSS | ^4.0 |
| Tests | Pest | ^3.0 |
| CI/CD | GitHub Actions | — |

---

## 👥 Équipe et branches

Chaque développeur travaille sur sa propre branche. **Aucun commit direct sur `main` ou `develop`.**

| Développeur | Module(s) | Branche |
|---|---|---|
| **Wilson Kouassi** (Lead) | Auth & Users — Architecture — Code Review | `feature/wilson` |
| Abdoul Rahim | Forum | `feature/abdoul` |
| Roger DA SIE | Événements + Job Board | `feature/roger` |
| Brindou Junior | Vitrine (pages publiques) | `feature/brindou` |
| Stéphane KONAN | Dashboard Membre | `feature/stephane` |
| Emmanuel KACOU | Blog & Ressources + Notifications | `feature/emmanuel` |
| Ibrahima DIARRA | Admin Filament + QA | `feature/ibrahima` |

### Branches principales

```
main        → production uniquement (déployé automatiquement)
develop     → branche d'intégration (tous les merges passent ici)
feature/*   → branches de développement par développeur
```

---

## 🏗 Architecture du projet

```
laravel-ci/
│
├── app/
│   │
│   ├── Actions/                        # Actions atomiques (1 action = 1 responsabilité)
│   │   ├── Auth/
│   │   │   └── CreateUserFromGithub.php    # Créer un user depuis le callback GitHub
│   │   ├── Forum/
│   │   │   └── ValidateAnswer.php          # Marquer une réponse comme solution
│   │   └── JobBoard/
│   │       └── ExpireOldOffers.php         # Expirer les offres après 30j
│   │
│   ├── Http/
│   │   ├── Controllers/                # Contrôleurs HTTP classiques (légers, délèguent aux Services)
│   │   │   ├── Auth/
│   │   │   │   └── SocialiteController.php
│   │   │   ├── Forum/
│   │   │   │   └── QuestionController.php
│   │   │   ├── Blog/
│   │   │   │   └── ArticleController.php
│   │   │   ├── Events/
│   │   │   │   └── EventController.php
│   │   │   └── JobBoard/
│   │   │       └── JobOfferController.php
│   │   │
│   │   ├── Requests/                   # Form Requests (validation des entrées)
│   │   │   ├── Forum/
│   │   │   │   └── StoreQuestionRequest.php
│   │   │   ├── Blog/
│   │   │   │   └── StoreArticleRequest.php
│   │   │   └── JobBoard/
│   │   │       └── StoreJobOfferRequest.php
│   │   │
│   │   └── Middleware/
│   │       ├── CheckMemberActive.php       # Vérifie que le membre n'est pas suspendu
│   │       └── EnsureEmailVerified.php
│   │
│   ├── Livewire/                       # Composants Livewire (UI réactive, organisés par feature)
│   │   ├── Auth/
│   │   │   └── Login.php
│   │   ├── Forum/
│   │   │   ├── QuestionList.php            # Liste des questions avec filtres
│   │   │   ├── QuestionDetail.php          # Détail + réponses + votes
│   │   │   └── AskQuestion.php             # Formulaire de création
│   │   ├── Blog/
│   │   │   ├── ArticleList.php
│   │   │   └── ArticleDetail.php
│   │   ├── Events/
│   │   │   ├── EventList.php
│   │   │   └── EventRegistration.php
│   │   ├── JobBoard/
│   │   │   ├── JobList.php
│   │   │   └── JobApplication.php
│   │   ├── Dashboard/
│   │   │   ├── Overview.php                # Vue d'ensemble membre
│   │   │   ├── EditProfile.php
│   │   │   └── MyApplications.php
│   │   └── Notifications/
│   │       └── NotificationBell.php        # Cloche temps réel (Reverb)
│   │
│   ├── Services/                       # Logique métier (appelée par Controllers et Livewire)
│   │   ├── Auth/
│   │   │   └── SocialiteService.php        # Logique OAuth GitHub
│   │   ├── Forum/
│   │   │   ├── QuestionService.php
│   │   │   └── VoteService.php
│   │   ├── Blog/
│   │   │   └── ArticleService.php
│   │   ├── Events/
│   │   │   └── EventService.php
│   │   ├── JobBoard/
│   │   │   └── JobOfferService.php
│   │   ├── Notifications/
│   │   │   └── NotificationService.php
│   │   └── User/
│   │       └── UserService.php
│   │
│   ├── Filament/                       # Admin Panel Filament (séparé du reste)
│   │   ├── Resources/
│   │   │   ├── UserResource.php
│   │   │   ├── QuestionResource.php
│   │   │   ├── ArticleResource.php
│   │   │   ├── EventResource.php
│   │   │   └── JobOfferResource.php
│   │   ├── Pages/
│   │   │   └── Dashboard.php
│   │   └── Widgets/
│   │       ├── StatsOverview.php
│   │       └── MemberGrowthChart.php
│   │
│   ├── Models/                         # Modèles Eloquent
│   │   ├── User.php
│   │   ├── Question.php
│   │   ├── Answer.php
│   │   ├── Article.php
│   │   ├── Event.php
│   │   ├── EventRegistration.php
│   │   ├── JobOffer.php
│   │   ├── JobApplication.php
│   │   └── Tag.php
│   │
│   └── Providers/
│       ├── AppServiceProvider.php
│       └── Filament/
│           └── AdminPanelProvider.php
│
├── resources/
│   └── views/
│       └── livewire/                   # Vues Blade des composants Livewire (séparées du PHP)
│           ├── auth/
│           │   └── login.blade.php
│           ├── forum/
│           │   ├── question-list.blade.php
│           │   ├── question-detail.blade.php
│           │   └── ask-question.blade.php
│           ├── blog/
│           ├── events/
│           ├── job-board/
│           ├── dashboard/
│           └── notifications/
│               └── notification-bell.blade.php
│
├── database/
│   ├── migrations/
│   └── seeders/
│
├── routes/
│   ├── web.php                         # Routes publiques et membres
│   └── auth.php                        # Routes auth (GitHub OAuth)
│
└── tests/
    ├── Feature/                        # Tests fonctionnels (un dossier par module)
    │   ├── Auth/
    │   ├── Forum/
    │   ├── Blog/
    │   ├── Events/
    │   └── JobBoard/
    └── Unit/                           # Tests unitaires (Services, Actions)
        ├── Services/
        └── Actions/
```

### Rôle de chaque couche

| Couche | Rôle | Règle |
|---|---|---|
| **Controller** | Reçoit la requête HTTP, valide via Form Request, appelle le Service, retourne la réponse | Pas de logique métier ici |
| **Form Request** | Valide et autorise les données entrantes | Une Request par action (Store, Update) |
| **Service** | Contient toute la logique métier | Peut appeler plusieurs Actions et Models |
| **Action** | Une seule responsabilité atomique et réutilisable | Ex: `CreateUserFromGithub` |
| **Livewire** | Gère l'état UI et les interactions côté front | Appelle les Services, pas les Models directement |
| **Filament Resource** | Interface admin CRUD | Séparé de la logique front membre |
| **Model** | Représentation Eloquent + relations | Pas de logique métier dans les modèles |

---

## 🔀 Workflow Git — Lead (Wilson)

Le Lead initialise le repo et gère les branches principales.

### Mise en place initiale (une seule fois)

```bash
# Créer la branche develop depuis main
git checkout -b develop
git push origin develop

# Créer sa propre branche de travail
git checkout -b feature/wilson
git push origin feature/wilson
```

### Workflow quotidien du Lead

```bash
# 1. Se mettre à jour sur develop
git checkout develop
git pull origin develop

# 2. Aller sur sa branche
git checkout feature/wilson

# 3. Rebase pour rester à jour avec le travail des autres
git rebase develop

# 4. Coder, committer, pusher
git add .
git commit -m "feat(auth): add github oauth callback handler"
git push origin feature/wilson

# 5. Ouvrir une PR feature/wilson → develop sur GitHub
#    Puis merger sa propre PR après vérification
```

### Récupérer un merge après validation

```bash
# Après avoir mergé une PR sur develop
git checkout develop
git pull origin develop

# Retourner sur sa branche et se rebaser
git checkout feature/wilson
git rebase develop
```

---

## 👨‍💻 Workflow Git — Autres développeurs

Les autres devs sont invités comme **collaborateurs** sur le repo GitHub par le Lead.

> **Wilson** → Settings → Collaborators → Add people → entrer le username GitHub de chaque dev

### Mise en place initiale (une seule fois par dev)

```bash
# 1. Cloner le repo
git clone https://github.com/Laravel-CI-Dev-Space/laravel-ci.git
cd laravel-ci

# 2. Créer sa branche depuis develop
git checkout develop
git checkout -b feature/abdoul    # chacun met son prénom
git push origin feature/abdoul

# 3. Installer le projet
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
```

### Workflow quotidien de chaque dev

```bash
# Chaque matin avant de coder — récupérer le travail des autres
git checkout develop
git pull origin develop
git checkout feature/abdoul
git rebase develop

# Coder...

# Formater le code avant de committer
./vendor/bin/pint

# Committer et pusher
git add .
git commit -m "feat(forum): add question list with pagination"
git push origin feature/abdoul

# Ouvrir une PR sur GitHub et attendre la review du Lead
```

### Règles strictes — tout le monde

| Règle | Détail |
|---|---|
| ❌ Jamais de commit direct sur `main` | Branche de production uniquement |
| ❌ Jamais de commit direct sur `develop` | Passe obligatoirement par une PR |
| ❌ Jamais de merge sans PR validée | Le Lead doit approuver |
| ✅ Rebase sur `develop` avant chaque PR | Évite les conflits au merge |
| ✅ Tests obligatoires sur tout nouveau code | Pas de PR acceptée sans tests |
| ✅ Pint lancé avant chaque commit | Code PSR-12 obligatoire |

---

## 📬 Ouvrir une Pull Request

Après un `git push origin feature/ton-nom`, GitHub affiche une bannière :

```
feature/abdoul had recent pushes — Compare & pull request
```

Clique sur **"Compare & pull request"** ou va dans l'onglet **Pull requests → New pull request**.

### Configurer la PR

```
base:    develop          ← TOUJOURS develop, jamais main
compare: feature/abdoul   ← ta branche
```

> ⚠️ GitHub propose `base: main` par défaut — **change-le en `develop` à chaque fois.**

### Remplir le formulaire

```
Titre : feat(forum): add question list with pagination

Description :

## Ce qui a été fait
- Composant Livewire QuestionList avec pagination
- Filtres : récent / populaire / sans réponse
- Service ForumService::getQuestions()

## Comment tester
1. Aller sur /forum
2. Tester les filtres
3. php artisan test --filter=QuestionTest

## Screenshots
(joindre une capture si changement UI)
```

Clique **"Create pull request"** et attends la review du Lead. **Ne merge pas toi-même.**

---

## 🔍 Review et merge — rôle du Lead

Wilson reçoit une notification GitHub à chaque nouvelle PR. Processus :

**1. Lire le code** dans l'onglet **"Files changed"**

**2. Laisser des commentaires** si des corrections sont nécessaires → le dev corrige et re-push sur sa branche, la PR se met à jour automatiquement

**3. Approuver** avec **"Approve"** si tout est bon

**4. Merger** avec **"Merge pull request" → "Confirm merge"**

**5. Ne pas supprimer la branche** du dev — c'est sa branche permanente de travail

---

## 🔒 Droits et protections des branches

### Tableau des droits

| Action | Dev | Lead (Wilson) |
|---|---|---|
| Push sur `feature/son-nom` | ✅ | ✅ |
| Push direct sur `develop` | ❌ | ✅ |
| Push direct sur `main` | ❌ | ✅ |
| Ouvrir une PR | ✅ | ✅ |
| Approuver une PR | ❌ | ✅ |
| Merger une PR | ❌ | ✅ |
| Déployer en production | ❌ | ✅ |

### Configurer la protection des branches sur GitHub

Aller dans **Settings → Branches → Add branch protection rule** et appliquer ces règles sur `main` ET `develop` :

```
✅ Require a pull request before merging
✅ Require approvals (1 minimum)
✅ Require status checks to pass (GitHub Actions)
✅ Do not allow bypassing the above settings
```

---

## ✍️ Nomenclature et conventions de code

### Format PHP — PSR-12 obligatoire (Laravel Pint)

```bash
# Formater avant chaque commit
./vendor/bin/pint
```

### Nommage

| Élément | Convention | Exemple |
|---|---|---|
| Classes | PascalCase | `QuestionService` |
| Méthodes | camelCase | `createQuestion()` |
| Variables | camelCase | `$questionList` |
| Routes | kebab-case | `/forum/ask-question` |
| Vues Blade | kebab-case | `question-list.blade.php` |
| Composants Livewire | PascalCase (classe) / kebab (vue) | `QuestionList.php` / `question-list.blade.php` |
| Tables DB | snake_case pluriel | `job_offers`, `event_registrations` |
| Colonnes DB | snake_case | `github_username`, `is_active` |
| Migrations | timestamp + description | `2026_05_04_create_questions_table` |

### Langue

- **Code** : anglais (classes, méthodes, variables, routes)
- **Commentaires** : français accepté
- **Messages de commit** : anglais
- **Noms de branches** : anglais (`feature/wilson`)

### Messages de commit — Convention Conventional Commits

```
type(scope): description courte en anglais

Types :
  feat      → nouvelle fonctionnalité
  fix       → correction de bug
  refactor  → refactoring sans changement de comportement
  test      → ajout ou modification de tests
  docs      → documentation
  chore     → tâche technique (config, dépendances)
  style     → formatage, espaces (pas de changement logique)

Exemples :
  feat(forum): add upvote/downvote system
  fix(auth): handle github oauth token expiry
  feat(blog): add article submission workflow
  test(events): add registration confirmation test
  refactor(jobs): extract offer expiry to action class
  docs(readme): update architecture section
```

---

## 🔧 Pattern : Service · Controller · Form Request

Voici un exemple complet sur le module **Forum > Créer une question**.

### 1. Form Request — validation des données

```php
// app/Http/Requests/Forum/StoreQuestionRequest.php
<?php

namespace App\Http\Requests\Forum;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Seuls les membres actifs peuvent poser des questions
        return $this->user()?->hasRole('membre-actif');
    }

    public function rules(): array
    {
        return [
            'title'   => ['required', 'string', 'min:10', 'max:255'],
            'body'    => ['required', 'string', 'min:30'],
            'tags'    => ['required', 'array', 'min:1', 'max:5'],
            'tags.*'  => ['exists:tags,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.min'  => 'Le titre doit contenir au moins 10 caractères.',
            'tags.max'   => 'Vous ne pouvez pas ajouter plus de 5 tags.',
        ];
    }
}
```

### 2. Service — logique métier

```php
// app/Services/Forum/QuestionService.php
<?php

namespace App\Services\Forum;

use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Str;

class QuestionService
{
    /**
     * Crée une nouvelle question et attache les tags.
     */
    public function createQuestion(User $user, array $data): Question
    {
        $question = Question::create([
            'user_id' => $user->id,
            'title'   => $data['title'],
            'slug'    => Str::slug($data['title']),
            'body'    => $data['body'],
        ]);

        $question->tags()->sync($data['tags']);

        return $question;
    }

    /**
     * Récupère les questions paginées avec filtres.
     */
    public function getQuestions(string $sort = 'recent', ?int $tagId = null)
    {
        $query = Question::with(['user', 'tags'])
            ->withCount('answers');

        if ($tagId) {
            $query->whereHas('tags', fn ($q) => $q->where('id', $tagId));
        }

        return match ($sort) {
            'popular'      => $query->orderByDesc('votes_count')->paginate(20),
            'unanswered'   => $query->having('answers_count', 0)->paginate(20),
            default        => $query->latest()->paginate(20),
        };
    }
}
```

### 3. Controller — reçoit, délègue, retourne

```php
// app/Http/Controllers/Forum/QuestionController.php
<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Http\Requests\Forum\StoreQuestionRequest;
use App\Services\Forum\QuestionService;

class QuestionController extends Controller
{
    public function __construct(
        private readonly QuestionService $questionService
    ) {}

    public function store(StoreQuestionRequest $request)
    {
        // Le Controller ne contient aucune logique métier
        // Il valide (Form Request) et délègue (Service)
        $question = $this->questionService->createQuestion(
            user: $request->user(),
            data: $request->validated()
        );

        return redirect()
            ->route('forum.questions.show', $question)
            ->with('success', 'Votre question a été publiée.');
    }
}
```

### 4. Composant Livewire — UI réactive (PHP séparé de la vue)

```php
// app/Livewire/Forum/QuestionList.php
<?php

namespace App\Livewire\Forum;

use App\Services\Forum\QuestionService;
use Livewire\Component;
use Livewire\WithPagination;

class QuestionList extends Component
{
    use WithPagination;

    public string $sort = 'recent';
    public ?int $tagId = null;

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function render(QuestionService $questionService)
    {
        return view('livewire.forum.question-list', [
            'questions' => $questionService->getQuestions($this->sort, $this->tagId),
        ]);
    }
}
```

```blade
{{-- resources/views/livewire/forum/question-list.blade.php --}}
<div>
    {{-- Filtres --}}
    <div class="flex gap-2 mb-6">
        <button wire:click="$set('sort', 'recent')" class="btn">Récents</button>
        <button wire:click="$set('sort', 'popular')" class="btn">Populaires</button>
        <button wire:click="$set('sort', 'unanswered')" class="btn">Sans réponse</button>
    </div>

    {{-- Liste --}}
    @foreach($questions as $question)
        <div class="question-card">
            <h3>{{ $question->title }}</h3>
            <span>{{ $question->answers_count }} réponse(s)</span>
        </div>
    @endforeach

    {{ $questions->links() }}
</div>
```

---

## 🚀 Installation locale

### Prérequis

| Outil | Version minimale | Vérification |
|---|---|---|
| PHP | 8.3+ | `php --version` |
| Composer | 2.x | `composer --version` |
| Node.js | 18+ | `node --version` |
| MySQL | 8.0+ | `mysql --version` |
| Redis | 6+ (optionnel en local) | `redis-cli ping` |
| Git | — | `git --version` |

> **Redis** est configuré par défaut pour le cache et les sessions. En local vous pouvez le remplacer par `file` dans le `.env` (voir étape 3).

---

### Étape 1 — Cloner le dépôt

```bash
git clone git@github.com:Laravel-CI-Dev-Space/laravel-ci-platform.git
cd laravel-ci-platform

# Créer votre branche de travail depuis la branche principale
git checkout main
git checkout -b solo/votre-prenom
```

---

### Étape 2 — Installer les dépendances

```bash
composer install
npm install
```

---

### Étape 3 — Configurer l'environnement

```bash
cp .env.example .env
php artisan key:generate
```

Ouvrir `.env` et remplir les valeurs suivantes :

```dotenv
APP_NAME=LaravelCi
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# ─── Base de données ───────────────────────────────────────
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_ci        # créer cette base au préalable
DB_USERNAME=root
DB_PASSWORD=

# ─── Sessions & Cache ──────────────────────────────────────
# Si Redis n'est pas installé, utiliser file à la place
SESSION_DRIVER=file           # ou redis
CACHE_STORE=file              # ou redis
QUEUE_CONNECTION=sync         # sync = pas de worker nécessaire en local

# ─── GitHub OAuth ──────────────────────────────────────────
# Créer une OAuth App sur https://github.com/settings/developers
# Homepage URL    : http://localhost:8000
# Callback URL    : http://localhost:8000/auth/github/callback
GITHUB_CLIENT_ID=votre_client_id
GITHUB_CLIENT_SECRET=votre_client_secret
GITHUB_REDIRECT_URI=http://localhost:8000/auth/github/callback

# ─── Mail (optionnel, array = aucun envoi réel) ────────────
MAIL_MAILER=array

# ─── Scout / Meilisearch (optionnel) ──────────────────────
SCOUT_DRIVER=null             # désactiver si Meilisearch n'est pas installé
```

> **GitHub OAuth est obligatoire** : l'authentification est 100 % GitHub. Sans les clés, aucun login n'est possible.

---

### Étape 4 — Créer la base de données

```sql
-- Dans MySQL
CREATE DATABASE laravel_ci CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

### Étape 5 — Migrations

```bash
php artisan migrate
```

Cela exécute les **58 migrations** dans l'ordre : users, profiles, questions, answers, articles, events, jobs, analytics, etc.

---

### Étape 6 — Seeders (données de démo)

```bash
php artisan db:seed
```

Le `DatabaseSeeder` exécute **15 seeders dans l'ordre** :

| # | Seeder | Contenu |
|---|---|---|
| 1 | `RoleSeeder` | Rôles (super-admin, admin, moderator, member) + permissions |
| 2 | `TagSeeder` | Tags forum & blog |
| 3 | `JobOfferCategorySeeder` | Catégories d'offres d'emploi |
| 4 | `JobSkillSeeder` | Compétences techniques |
| 5 | `UserSeeder` | Comptes de test (admin, modérateur, membres) |
| 6 | `SiteSettingSeeder` | Paramètres du site (nom, réseaux sociaux, limites…) |
| 7 | `HomeStatSeeder` | Statistiques affichées en page d'accueil |
| 8 | `PartnerSeeder` | Logos partenaires |
| 9 | `TeamMemberSeeder` | Membres fondateurs (page À propos) |
| 10 | `CommunityValueSeeder` | Valeurs communautaires |
| 11 | `TimelineEventSeeder` | Historique de la communauté |
| 12 | `AboutOriginSectionSeeder` | Section "Notre naissance" |
| 13 | `EventSeeder` | 2 événements (meetup gratuit + workshop payant) |
| 14 | `GradeSeeder` | Grades de réputation |
| 15 | `DemoSeeder` | 12 membres, 6 articles, 6 questions, inscriptions, 2 entreprises + offres d'emploi |

**Comptes créés après le seed :**

| Rôle | Email | Connexion |
|---|---|---|
| Super Admin | yanne.kouassi@epitech.eu | GitHub OAuth (compte réel) |
| Admin | admin@laravelci.com | GitHub OAuth |
| Modérateur | moderator@laravelci.com | GitHub OAuth |
| Membre | member@laravelci.com | GitHub OAuth |
| Membre suspendu | suspended@laravelci.com | — |

> Les comptes de test nécessitent que leur `github_username` soit associé à un vrai compte GitHub pour pouvoir se connecter en local. Pour les tests, utilisez l'endpoint `/_e2e/login/{email}` (disponible en env `local`).

---

### Étape 7 — Lier le stockage

```bash
php artisan storage:link
```

Crée le lien symbolique `public/storage → storage/app/public` (avatars, couvertures d'offres, logos équipe).

---

### Étape 8 — Compiler les assets

```bash
# Développement (avec HMR)
npm run dev

# Production
npm run build
```

---

### Étape 9 — Lancer le serveur

```bash
# Tout en un (serveur PHP + Vite HMR)
composer run dev

# Ou séparément
php artisan serve       # http://localhost:8000
npm run dev             # Vite HMR sur port 5173
```

---

### Résumé en une ligne (après config .env)

```bash
composer install && npm install && php artisan migrate --seed && php artisan storage:link && npm run build && php artisan serve
```

---

### Commandes utiles en développement

```bash
# Vider tous les caches
php artisan optimize:clear

# Re-seeder proprement (remet la DB à zéro)
php artisan migrate:fresh --seed

# Voir toutes les routes
php artisan route:list

# Mettre en cache les routes (prod)
php artisan route:cache

# Formater le code avant un commit
./vendor/bin/pint
```

---

## 🧪 Tests

Les tests utilisent **Pest v3** avec SQLite en mémoire — aucune connexion MySQL requise.

```bash
# Lancer tous les tests
php artisan test

# Tests d'un module uniquement
php artisan test --filter=Forum
php artisan test --filter=Blog
php artisan test --filter=Events
php artisan test --filter=Jobs
php artisan test --filter=Company

# Stopper au premier échec
php artisan test --stop-on-failure

# Avec couverture de code
php artisan test --coverage
```

**État actuel de la suite de tests : 265 passent / 301 au total.**

Les 35 tests en échec sont des routes POST sans token CSRF dans leur requête de test (`419 Page Expired`). C'est un problème connu dans la suite existante, non lié à votre installation. Les modules couverts et fonctionnels : Auth, Blog (GET), Events (GET), Forum (GET), Company (GET), Models, Services, Mail.

```bash
# Lancer uniquement les tests qui passent (modules stables)
php artisan test --testsuite=Unit
```

---

## ✅ Standards de qualité

### Tests obligatoires

Tout nouveau module ou feature doit avoir ses tests. Pas de PR acceptée sans tests.

```bash
# Lancer tous les tests
php artisan test

# Lancer les tests d'un module
php artisan test --filter=ForumTest

# Coverage
php artisan test --coverage
```

### Exemple de test Pest

```php
// tests/Feature/Forum/QuestionTest.php
<?php

use App\Models\User;
use App\Models\Tag;

test('un membre actif peut poser une question', function () {
    $user = User::factory()->create();
    $user->assignRole('membre-actif');
    $tags = Tag::factory(2)->create();

    $response = $this->actingAs($user)->post('/forum/questions', [
        'title' => 'Comment utiliser Eloquent avec des relations polymorphiques ?',
        'body'  => 'Je cherche à comprendre comment fonctionne morphTo dans Laravel...',
        'tags'  => $tags->pluck('id')->toArray(),
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('questions', [
        'title'   => 'Comment utiliser Eloquent avec des relations polymorphiques ?',
        'user_id' => $user->id,
    ]);
});

test('un visiteur ne peut pas poser de question', function () {
    $response = $this->post('/forum/questions', [
        'title' => 'Test',
        'body'  => 'Test body content here minimum length',
        'tags'  => [1],
    ]);

    $response->assertRedirect('/login');
});
```

### Checklist avant chaque Pull Request

```
[ ] php artisan test → tous les tests passent
[ ] ./vendor/bin/pint → code formaté PSR-12
[ ] PHPDoc sur toutes les méthodes publiques
[ ] Pas de dd(), var_dump(), console.log() oublié
[ ] .env.example mis à jour si nouvelle variable d'environnement
[ ] Migration créée si changement de base de données
[ ] Rebase sur develop fait avant la PR
[ ] Description de la PR complète
```

---

> 🇨🇮 **Laravel Côte d'Ivoire** — Open Source MIT — 2026
> Construit avec ❤️ par l'équipe Laravel CI
