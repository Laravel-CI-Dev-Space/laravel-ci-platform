# Module Chatbot IA — SmartPME

Documentation technique complète du module assistant IA intégré à SmartPME.

---

## Table des matières

1. [Vue d'ensemble](#1-vue-densemble)
2. [Fournisseurs IA et clés API](#2-fournisseurs-ia-et-clés-api)
3. [Architecture technique](#3-architecture-technique)
4. [Tool unifié query_boutique](#4-tool-unifié-query_boutique)
5. [Templates de réponse HTML](#5-templates-de-réponse-html)
6. [Vision et reconnaissance d'image](#6-vision-et-reconnaissance-dimage)
7. [Fichiers de connaissance](#7-fichiers-de-connaissance)
8. [Isolation des données par boutique](#8-isolation-des-données-par-boutique)
9. [Gestion des tokens et quotas](#9-gestion-des-tokens-et-quotas)
10. [Configuration super-admin](#10-configuration-super-admin)
11. [Variables d'environnement](#11-variables-denvironnement)
12. [Migrations et base de données](#12-migrations-et-base-de-données)
13. [Routes](#13-routes)

---

## 1. Vue d'ensemble

Le module chatbot IA permet à chaque boutique SmartPME de disposer d'un assistant conversationnel capable de répondre à toutes les questions de gestion : ventes, stock, dépenses, factures et résumés de performance.

**Caractéristiques principales :**
- Fournisseur par défaut : **DeepSeek** (remplace OpenAI, supprimé)
- Fournisseur alternatif : **Groq**
- Tool unifié à 16 intents couvrant tous les domaines métier
- Réponses tabulaires HTML injectées automatiquement
- Reconnaissance d'images (vision multimodale)
- Fichiers de connaissance personnalisables par boutique
- Isolation stricte des données par team (aucune fuite cross-boutique)
- Fenêtre glissante de 10 messages + auto-résumé à 20 messages
- Cache Redis/DB de 3 minutes par résultat d'outil
- Quota mensuel de tokens configurable par boutique

---

## 2. Fournisseurs IA et clés API

### DeepSeek (fournisseur par défaut)

| Paramètre | Valeur |
|---|---|
| **Clé API** | `sk-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx` |
| **Base URL** | `https://api.deepseek.com` |
| **Modèle par défaut** | `deepseek-v4-flash` |
| **Modèle puissant** | `deepseek-v4-pro` |
| **Vision** | ✅ Les deux modèles supportent l'analyse d'images |
| **Documentation** | https://api-docs.deepseek.com/ |

### Groq (fournisseur alternatif)

| Paramètre | Valeur |
|---|---|
| **Clé API** | `gsk_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx` |
| **Base URL** | `https://api.groq.com/openai/v1` |
| **Modèle par défaut** | `llama-3.3-70b-versatile` |
| **Modèle rapide** | `llama-3.1-8b-instant` |
| **Modèles vision** | `llama-3.2-11b-vision-preview`, `llama-3.2-90b-vision-preview` |
| **Vision** | ✅ Uniquement les modèles `-vision-preview` |

> **Note :** Les deux fournisseurs utilisent l'OpenAI PHP SDK avec `base_url` personnalisée (APIs compatibles OpenAI). OpenAI a été entièrement supprimé du projet.

---

## 3. Architecture technique

### Fichiers principaux

```
app/
├── Services/Chatbot/
│   ├── ChatbotService.php       — Orchestration principale (loop tool, contexte, résumé)
│   └── ChatbotTools.php         — Tool unifié query_boutique (16 intents, queries SQL)
├── Models/
│   ├── ChatbotConfig.php        — Config par boutique (provider, model, quota…)
│   ├── ChatbotConversation.php  — Conversations actives par user/team
│   ├── ChatbotMessage.php       — Messages (user/assistant/tool/system)
│   ├── ChatbotTokenUsage.php    — Suivi quotidien tokens + coût estimé
│   └── ChatbotResponseTemplate.php — Templates HTML tabulaires
├── Http/Controllers/
│   ├── Teams/ChatbotController.php        — API frontend (send, history, upload, capabilities)
│   └── SuperAdmin/ChatbotConfigController.php — Config admin + fichiers de connaissance
database/
├── migrations/
│   ├── ..._create_chatbot_configs_table.php
│   ├── ..._create_chatbot_conversations_table.php
│   ├── ..._create_chatbot_messages_table.php
│   ├── ..._create_chatbot_token_usages_table.php
│   ├── ..._create_chatbot_response_templates_table.php
│   ├── ..._add_deepseek_to_chatbot_configs_table.php
│   ├── ..._cleanup_provider_enum_chatbot_configs.php
│   └── ..._add_image_path_to_chatbot_messages.php
├── seeders/
│   └── ChatbotResponseTemplateSeeder.php  — 10 templates HTML pré-configurés
resources/views/partials/
│   └── chatbot-widget.blade.php           — Widget flottant (JS + CSS complet)
resources/views/super-admin/chatbot/
    ├── index.blade.php                    — Liste des boutiques + usage tokens
    ├── edit.blade.php                     — 5 onglets config (IA, Règles, Contexte, Format, Usage)
    └── _knowledge-tab.blade.php           — Partial éditeur fichier de connaissance
```

### Flux d'une requête utilisateur

```
Utilisateur saisit un message
        ↓
ChatbotController::send()
  → Vérifie quota mensuel tokens
  → Valide image_path (sécurité team-scoped)
        ↓
ChatbotService::chat()
  → buildContext() : système + knowledge files + historique sliding window + message courant
        ↓
Boucle API (max 5 rounds)
  → Appel DeepSeek/Groq avec tool query_boutique
  → Si tool call → ChatbotTools::execute() → SQL → cache 3 min → résultat
  → Si pas de tool call → réponse finale
        ↓
injectTableTemplate() si intent dans INTENTS_WITH_TABLE
  → Récupère template HTML depuis DB
  → Remplace {{variables}}, {{#rows}}...{{/rows}}
  → Injecte le texte du LM comme {{INTRO}}
        ↓
Sauvegarde ChatbotMessage (user + assistant)
Incrémente ChatbotTokenUsage
maybeSummarize() si > 20 messages
        ↓
JSON {reply: "..."} → widget frontend
```

---

## 4. Tool unifié query_boutique

Un seul tool avec 16 intents (au lieu de 15+ tools séparés). Le `team_id` est injecté côté serveur et n'est jamais exposé au modèle.

### Intents disponibles

| Intent | Domaine | Description |
|---|---|---|
| `ventes_kpis` | Ventes | Chiffre d'affaires, transactions, panier moyen, évolution |
| `ventes_top_produits` | Ventes | Top N produits par CA ou quantité |
| `ventes_liste` | Ventes | Liste des transactions avec détail |
| `stock_inventaire` | Stock | État complet du stock avec seuils |
| `stock_alertes` | Stock | Ruptures (stock=0) et stocks bas (< seuil) |
| `stock_valorisation` | Stock | Valeur totale au prix d'achat |
| `depenses_resume` | Dépenses | Totaux et évolution des dépenses |
| `depenses_ventilation` | Dépenses | Répartition par catégorie |
| `depenses_liste` | Dépenses | Liste des dépenses avec détail |
| `factures_resume` | Factures | Vue globale (nb, montants, statuts) |
| `factures_liste` | Factures | Liste des factures |
| `factures_impayees` | Factures | Impayées avec calcul de retard |
| `resume_performance` | Résumé | Résumés jour par jour (DailySummary) |
| `resume_tendance` | Résumé | Évolution et tendance sur période |
| `comparaison_periodes` | Résumé | Deux périodes côte à côte |
| `vue_globale` | Global | Snapshot complet de la boutique |

### Paramètres du tool

```json
{
  "intent": "ventes_kpis",
  "period": "this_month",
  "date_from": "2026-06-01",
  "date_to": "2026-06-30",
  "filter": "Alimentation",
  "search": "farine",
  "metric": "revenue",
  "limit": 10,
  "compare_previous": true,
  "period_b_from": "2026-05-01",
  "period_b_to": "2026-05-31"
}
```

**Valeurs de `period`** : `today`, `yesterday`, `this_week`, `last_week`, `this_month`, `last_month`, `this_year`, `last_year`

---

## 5. Templates de réponse HTML

Quand un intent retourne des données listables, un template HTML est automatiquement injecté dans la réponse. Le texte du LM devient l'introduction au-dessus du tableau.

### Intents avec template (INTENTS_WITH_TABLE)

```
ventes_liste, ventes_top_produits, depenses_liste, depenses_ventilation,
stock_inventaire, stock_alertes, stock_valorisation,
factures_liste, factures_impayees, resume_performance
```

### Syntaxe des templates (Mustache-like)

```html
{{INTRO}}                          <!-- texte généré par le LM -->
{{#rows}}
  <tr><td>{{nom}}</td></tr>        <!-- itération sur les lignes -->
{{/rows}}
{{#has_ruptures}}...{{/has_ruptures}} <!-- bloc conditionnel -->
{{total_ventes}}                   <!-- variable simple -->
```

### Seeder

```bash
php artisan db:seed --class=ChatbotResponseTemplateSeeder
```

10 templates pré-configurés. Modifiables directement en base de données (`chatbot_response_templates`).

---

## 6. Vision et reconnaissance d'image

### Modèles vision supportés

| Provider | Modèle | Vision |
|---|---|---|
| DeepSeek | `deepseek-v4-flash` | ✅ |
| DeepSeek | `deepseek-v4-pro` | ✅ |
| Groq | `llama-3.2-11b-vision-preview` | ✅ |
| Groq | `llama-3.2-90b-vision-preview` | ✅ |
| Groq | `llama-3.3-70b-versatile` | ❌ |
| Groq | `llama-3.1-8b-instant` | ❌ |

### Flux upload image

```
POST /chatbot/upload-image (multipart/form-data, champ: image)
  → Validation : jpg/jpeg/png/webp, max 5MB
  → Vérification vision support du modèle actif
  → Stockage : storage/app/chatbot/images/{team_id}/{uuid}.ext
  → Retourne : { image_path: "chatbot/images/1/abc123.jpg" }

POST /chatbot/send { message: "...", image_path: "chatbot/images/1/abc123.jpg" }
  → Validation sécurité : image_path doit commencer par chatbot/images/{team_id}/
  → Encodage base64 + envoi multimodal à l'API
  → Suppression du fichier après usage (image temporaire)
```

### Sécurité image

- Le chemin est validé côté serveur : `str_starts_with($path, 'chatbot/images/' . $team->id . '/')`
- Impossible d'accéder à l'image d'une autre boutique
- Les images ne sont PAS conservées dans l'historique des messages (trop coûteux en tokens)

### API capabilities

```
GET /chatbot/capabilities
→ { "vision": true }   // si le modèle actif supporte la vision
→ { "vision": false }  // sinon — le bouton image est masqué dans le widget
```

---

## 7. Fichiers de connaissance

Trois fichiers Markdown injectés automatiquement dans chaque conversation, après le prompt système.

### Emplacement

```
storage/app/chatbot/knowledge/{team_id}/
  ├── rules.md             — Règles de comportement de l'IA
  ├── business_context.md  — Contexte métier de la boutique
  └── response_format.md   — Format et style des réponses
```

### Comportement

- Si les fichiers n'existent pas → les valeurs par défaut définies dans `ChatbotConfigController::KNOWLEDGE_DEFAULTS` sont utilisées automatiquement
- Les 3 fichiers sont fusionnés en un seul bloc système (pour réduire l'overhead et éviter les échecs de tool-call DeepSeek)
- Modifiables depuis l'interface super-admin : `/super-admin/chatbot/{team}` → onglets Règles / Contexte métier / Format réponses
- Bouton "Réinitialiser tout" → remet les fichiers aux valeurs par défaut

### Routes knowledge

```
POST /super-admin/chatbot/{team}/knowledge        → saveKnowledge()
POST /super-admin/chatbot/{team}/knowledge/reset  → resetKnowledge()
```

---

## 8. Isolation des données par boutique

**Règle fondamentale :** le `team_id` n'est jamais un paramètre du tool. Il est injecté dans le constructeur de `ChatbotTools` depuis la session auth PHP.

```php
// ChatbotService::chat()
$tools = new ChatbotTools($conversation->team_id);  // ← team_id injecté ici

// ChatbotTools — toutes les queries filtrent par $this->teamId
Transaction::where('team_id', $this->teamId)->...
Product::where('team_id', $this->teamId)->...
```

La description du tool inclut explicitement :
> "TOUTES les données retournées appartiennent EXCLUSIVEMENT à cette boutique — il est impossible d'accéder aux données d'une autre boutique ou d'un autre utilisateur."

---

## 9. Gestion des tokens et quotas

### Suivi quotidien

Table `chatbot_token_usages` : enregistrement par (team_id, user_id, date) avec :
- `prompt_tokens`, `completion_tokens`, `total_tokens`
- `messages_count`
- `estimated_cost` (USD)

### Quota mensuel

Configurable par boutique (`chatbot_configs.monthly_token_limit`, défaut : 100 000 tokens/mois).
Vérifié avant chaque appel dans `ChatbotController::send()`.

### Fenêtre glissante

- **SLIDING_WINDOW = 10** : seuls les 10 derniers messages sont inclus dans le contexte
- **SUMMARY_TRIGGER = 20** : à 20 messages, les anciens sont résumés automatiquement en 3-5 phrases via l'API, puis supprimés. Le résumé est stocké dans `chatbot_conversations.context_summary`

### KPIs usage

Page accessible sur `/chatbot/kpis` : usage mensuel total, usage personnel, historique 30 jours, liste des conversations.

---

## 10. Configuration super-admin

Accessible sur `/super-admin/chatbot` → `/super-admin/chatbot/{team}`.

### Onglets disponibles

| Onglet | Contenu |
|---|---|
| **Paramètres IA** | Provider, modèle, température, tokens max, quota mensuel, prompt système |
| **Règles** | Éditeur `rules.md` — périmètre, ton, confidentialité |
| **Contexte métier** | Éditeur `business_context.md` — activité, modules, devise |
| **Format réponses** | Éditeur `response_format.md` — FCFA, dates, structure réponses |
| **Usage** | Tableau usage tokens 30 derniers jours |

### Modèles disponibles

```
DeepSeek : deepseek-v4-flash (défaut, vision), deepseek-v4-pro (vision)
Groq     : llama-3.3-70b-versatile (défaut), llama-3.1-8b-instant,
           llama-3.2-11b-vision-preview, llama-3.2-90b-vision-preview
```

---

## 11. Variables d'environnement

```env
# DeepSeek (fournisseur par défaut)
DEEPSEEK_API_KEY=sk-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
DEEPSEEK_DEFAULT_MODEL=deepseek-chat
DEEPSEEK_BASE_URL=https://api.deepseek.com

# Groq (fournisseur alternatif)
GROQ_API_KEY=gsk_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
GROQ_DEFAULT_MODEL=llama-3.3-70b-versatile
GROQ_BASE_URL=https://api.groq.com/openai/v1
```

---

## 12. Migrations et base de données

### Tables créées / modifiées

| Migration | Action |
|---|---|
| `create_chatbot_configs_table` | Config IA par boutique |
| `create_chatbot_conversations_table` | Conversations actives |
| `create_chatbot_messages_table` | Messages (rôle, contenu, tokens) |
| `create_chatbot_token_usages_table` | Suivi quotidien tokens |
| `add_deepseek_to_chatbot_configs_table` | Ajout enum deepseek, default deepseek |
| `cleanup_provider_enum_chatbot_configs` | Suppression openai de l'enum |
| `create_chatbot_response_templates_table` | Templates HTML tabulaires |
| `add_image_path_to_chatbot_messages` | Colonne `image_path` nullable |

### Commandes

```bash
# Appliquer les migrations
php artisan migrate

# Peupler les templates HTML
php artisan db:seed --class=ChatbotResponseTemplateSeeder
```

---

## 13. Routes

### Frontend (utilisateur)

```
POST   /chatbot/send            → Envoyer un message (+ image_path optionnel)
POST   /chatbot/upload-image    → Uploader une image (retourne image_path)
GET    /chatbot/capabilities    → Capacités du modèle actif (vision: bool)
GET    /chatbot/history         → Historique de la conversation active
POST   /chatbot/new             → Démarrer une nouvelle conversation
GET    /chatbot/kpis            → Page usage tokens
```

### Super-admin

```
GET    /super-admin/chatbot                      → Liste boutiques + usage
GET    /super-admin/chatbot/{team}               → Formulaire config (5 onglets)
PUT    /super-admin/chatbot/{team}               → Sauvegarder config IA
POST   /super-admin/chatbot/{team}/knowledge     → Sauvegarder fichiers connaissance
POST   /super-admin/chatbot/{team}/knowledge/reset → Réinitialiser aux defaults
```

---

## Notes importantes

- **OpenAI entièrement supprimé** — plus aucune référence à OpenAI dans le code métier. Le SDK `openai-php/client` est conservé uniquement comme adaptateur HTTP pour DeepSeek et Groq (APIs compatibles).
- **Erreur DeepSeek "Failed to call a function"** — gérée par un catch avec retry sans tools si le contexte est trop long (premier round uniquement).
- **Le `.env` ne doit pas être commité** — les clés API ci-dessus sont dans ce fichier de documentation uniquement à titre de référence interne.
