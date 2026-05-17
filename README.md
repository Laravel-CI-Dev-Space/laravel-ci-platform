# laravel-ci-platform
# 🐘 Plateforme Laravel Côte d'Ivoire

Hub communautaire officiel — Forum · Blog · Événements · Job Board · Admin

> Construit par des développeurs ivoiriens, pour des développeurs ivoiriens.

---

## 📋 Table des matières

- [Stack technique](#-stack-technique)
- [Équipe et branches](#-équipe-et-branches)
- [Architecture du projet](#-architecture-du-projet)
- [Règles Git et workflow](#-règles-git-et-workflow)
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

## 🔀 Règles Git et workflow

### Workflow quotidien

```bash
# 1. Toujours partir d'un develop à jour
git checkout develop
git pull origin develop

# 2. Aller sur sa branche
git checkout feature/wilson   # ou ton prénom

# 3. Rebase régulièrement pour rester à jour
git rebase develop

# 4. Travailler, committer, pusher
git add .
git commit -m "feat(auth): add github oauth callback handler"
git push origin feature/wilson

# 5. Ouvrir une Pull Request vers develop (jamais vers main)
```

### Règles strictes

- ❌ **Jamais** de commit direct sur `main` ou `develop`
- ❌ **Jamais** de merge sans Pull Request validée
- ✅ **Toujours** rebase sur `develop` avant d'ouvrir une PR
- ✅ **Toujours** attendre la review du Lead avant merge
- ✅ **Toujours** écrire des tests pour tout nouveau code

### Pull Requests

- Titre clair : `[MODULE] Description courte`
- Description : ce qui a été fait, comment tester, screenshots si UI
- Au moins **1 review** du Lead (Wilson) avant merge sur `develop`
- Les tests doivent passer (GitHub Actions)

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

```bash
# 1. Cloner le repo
git clone https://github.com/Laravel-CI-Dev-Space/laravel-ci.git
cd laravel-ci

# 2. Installer les dépendances PHP
composer install

# 3. Copier et configurer l'environnement
cp .env.example .env
php artisan key:generate

# 4. Configurer la base de données dans .env
# DB_DATABASE=laravel_ci
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Configurer GitHub OAuth dans .env
# GITHUB_CLIENT_ID=xxxxxxxxxxxx
# GITHUB_CLIENT_SECRET=xxxxxxxxxxxx
# GITHUB_REDIRECT_URI=http://localhost:8000/auth/github/callback

# 6. Migrer et seeder
php artisan migrate --seed

# 7. Installer les dépendances front
npm install && npm run build

# 8. Lancer le serveur
composer run dev
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
