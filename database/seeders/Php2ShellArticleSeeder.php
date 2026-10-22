<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

class Php2ShellArticleSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::where('github_id', '167759591')->first() ?? User::first();

        if ($author === null) {
            $this->command->warn('Php2ShellArticleSeeder : aucun utilisateur trouvé, abandon.');
            return;
        }

        $body = $this->articleMarkdown();
        $html = $this->compileHtml($body);
        $slug = 'php2shell-cve-2026-109618-109619-faille-critique-php';

        $article = Article::updateOrCreate(
            ['slug' => $slug],
            [
                'user_id'      => $author->id,
                'reviewed_by'  => $author->id,
                'author_label' => 'Communauté Laravel CI',
                'title'        => 'PHP2Shell — la faille critique PHP qui expose vos serveurs (CVE-2026-109618)',
                'slug'         => $slug,
                'excerpt'      => 'Deux vulnérabilités critiques (CVSS 9.2) ont été patchées le 21 octobre 2026 dans le cœur de PHP. Elles permettent à un attaquant non authentifié d\'exécuter du code arbitraire sur votre serveur. Voici tout ce que les devs Laravel doivent savoir et faire maintenant.',
                'body'         => $body,
                'body_html'    => $html,
                'cover_image'  => null,
                'level'        => 'intermediate',
                'status'       => 'published',
                'published_at' => now(),
                'reviewed_at'  => now(),
            ]
        );

        $tagNames = [
            ['name' => 'PHP',        'color' => '#7c3aed'],
            ['name' => 'Sécurité',   'color' => '#dc2626'],
            ['name' => 'CVE',        'color' => '#b91c1c'],
            ['name' => 'Laravel',    'color' => '#FF6600'],
            ['name' => 'DevOps',     'color' => '#0284c7'],
        ];

        $tagIds = [];
        foreach ($tagNames as $t) {
            $tag = Tag::firstOrCreate(
                ['slug' => Str::slug($t['name'])],
                [
                    'name'  => $t['name'],
                    'scope' => 'blog',
                    'color' => $t['color'],
                ]
            );
            $tagIds[] = $tag->id;
        }

        $article->tags()->sync($tagIds);

        $this->command->info("Php2ShellArticleSeeder : article '{$article->title}' OK (id={$article->id}).");
    }

    private function compileHtml(string $markdown): string
    {
        $env = new Environment([
            'html_input'         => 'strip',
            'allow_unsafe_links' => false,
        ]);
        $env->addExtension(new CommonMarkCoreExtension);
        $env->addExtension(new GithubFlavoredMarkdownExtension);

        return (new MarkdownConverter($env))->convert($markdown)->getContent();
    }

    private function articleMarkdown(): string
    {
        return <<<'MARKDOWN'
> **⚠ Alerte critique — 21 octobre 2026.** Deux failles d'exécution de code à distance dans PHP ont été patchées hier. Si ton app Laravel est en production, lis cet article avant de faire autre chose.

---

## TL;DR — les 30 secondes qui comptent

- Deux CVEs critiques dans le cœur de PHP : **CVE-2026-109618** et **CVE-2026-109619** (nom collectif : **PHP2Shell**)
- Score CVSS v4.0 : **9.2 / 10** pour chacune — classification *Critique*
- Versions corrigées : **PHP 8.5.12, 8.4.27, 8.3.36, 8.2.35**
- PHP 8.1 et inférieur : **aucun correctif officiel** (versions EOL)
- Premières tentatives d'exploitation observées **4 jours avant** le patch
- Action requise : **mettre à jour PHP maintenant**

---

## 1. C'est quoi PHP2Shell ?

PHP2Shell est le nom collectif donné à deux vulnérabilités critiques découvertes dans le moteur PHP lui-même — pas dans un framework, pas dans un package, mais dans le **runtime** qui fait tourner toute application PHP.

Les deux failles permettent à un attaquant **non authentifié** d'exécuter du code arbitraire sur le serveur cible. C'est ce qu'on appelle une **RCE** (Remote Code Execution) — la catégorie la plus grave des vulnérabilités web.

Le nom est intentionnellement évocateur : PHP → Shell. Un attaquant qui exploite PHP2Shell n'a pas besoin d'un compte sur ton serveur. Il a juste besoin que PHP tourne.

### Timeline de la divulgation

| Date | Événement |
|------|-----------|
| ~ oct. 2026 | Wiz Security découvre les deux vulnérabilités, entame une disclosure coordonnée avec l'équipe PHP |
| **17 octobre 2026** | Amazon Threat Intelligence observe la **première tentative d'exploitation**, avant toute divulgation publique — les informations avaient fuité |
| **21 octobre 2026** | PHP publie des correctifs d'urgence. Les CVEs sont rendus publics. |
| 21 oct. (soir) | Tenable confirme une **exploitation active**. Cloudflare signale des tentatives à large échelle. NCSC UK publie une alerte urgente. |

> **Ce n'est pas une alerte théorique.** Des acteurs malveillants tentaient d'exploiter ces failles 4 jours avant que les patches existent. Si tu n'as pas encore mis à jour, ta fenêtre d'exposition est ouverte depuis plusieurs jours.

---

## 2. Les deux failles expliquées simplement

### CVE-2026-109618 — La gestion hors-mémoire (OOM)

Quand PHP manque de mémoire au milieu du traitement d'une requête, il tente de nettoyer. Mais dans certaines situations, les données de la requête sont **partiellement initialisées** — et le code de nettoyage les traite comme valides.

Ce désalignement peut permettre à un attaquant de contrôler ce que PHP "nettoie", jusqu'à exécuter du code arbitraire.

**L'analogie :** Imagine un chef cuisinier qui perd connaissance à mi-préparation. Son commis, croyant que tout est prêt, sert les plats tels quels — dont certains contiennent des ingrédients crus. Si un attaquant peut provoquer cet "évanouissement" (en envoyant une requête conçue pour épuiser la mémoire), il peut potentiellement contrôler ce que PHP "sert" ensuite.

### CVE-2026-109619 — L'état qui survit à la mémoire

L'état interne d'une requête peut persister en mémoire après sa libération officielle. Une requête **ultérieure sur le même worker PHP** peut accéder à ces données périmées et les exploiter.

**L'analogie :** C'est comme si dans un open-space, un employé laissait ses dossiers confidentiels ouverts en partant. Le collègue qui s'installe ensuite à la même place peut lire et modifier les informations du précédent. Dans PHP, le "bureau", c'est la mémoire d'un worker partagé.

> **Note :** Il existe des divergences mineures entre les rapports de Sansec, Huntress et Datadog sur l'attribution exacte CVE→vecteur. Les mécanismes décrits ici sont ceux sur lesquels la majorité des sources concordent. Consulte les advisories officielles PHP (GHSA-w8hw-w9fx-j79w et GHSA-6wmf-4rv5-p3rp) pour les détails définitifs.

---

## 3. Mon app Laravel est-elle vulnérable ?

### Vérifier sa version PHP

```bash
php -v
```

| Branche | Vulnérable si | Version corrigée | Statut |
|---------|--------------|-----------------|--------|
| PHP 8.5 | ≤ 8.5.11 | **8.5.12** | ✅ Patch disponible |
| PHP 8.4 | ≤ 8.4.26 | **8.4.27** | ✅ Patch disponible |
| PHP 8.3 | ≤ 8.3.35 | **8.3.36** | ✅ Patch disponible |
| PHP 8.2 | ≤ 8.2.34 | **8.2.35** | ✅ Patch disponible |
| PHP 8.1 | Toutes | — | ❌ EOL — aucun fix |
| PHP 8.0 / 7.x | Toutes | — | ❌ EOL — aucun fix |

### Check-list d'exposition

- **Application accessible depuis internet ?** → Priorité maximale, patcher dans l'heure.
- **Mode d'exécution PHP :** `mod_php` (Apache) = risque maximal selon Tenable. PHP-FPM = exposé. CLI uniquement = non exposé.
- **PHP 8.1 ou inférieur en production ?** → Aucun patch disponible. Migrer vers 8.2+ est la seule vraie solution.
- **Hébergeur géré (Laravel Cloud, Forge, Render…) ?** → Vérifier le dashboard ou contacter le support — certains ont déjà patché automatiquement.
- **Docker avec image PHP officielle ?** → Les images `php:8.X-fpm` et `php:8.X-apache` sont déjà mises à jour sur Docker Hub.

---

## 4. Comment patcher — commandes prêtes à l'emploi

### Ubuntu / Debian

```bash
# Mettre à jour les dépôts et PHP
sudo apt update
sudo apt upgrade php8.4 php8.4-fpm   # adapter selon votre version

# Vérifier que le patch est appliqué
php -v

# Redémarrer PHP-FPM
sudo systemctl restart php8.4-fpm
```

### Red Hat / Fedora / Rocky Linux

```bash
sudo dnf update php php-fpm
sudo systemctl restart php-fpm
```

### macOS (développement local)

```bash
brew upgrade php
# ou pour une version spécifique :
brew upgrade php@8.4
```

### Docker

```dockerfile
# Remplacer l'image de base par une version patchée
FROM php:8.4.27-fpm-alpine
# ou
FROM php:8.3.36-apache
```

### Laravel Forge (via SSH)

```bash
sudo apt update && sudo apt upgrade -y php8.4 php8.4-fpm
sudo systemctl restart php8.4-fpm nginx
```

**Après le patch :** vérifier que l'app démarre correctement, puis :

```bash
php artisan config:cache
php artisan route:cache
```

---

## 5. Si je ne peux pas patcher maintenant

> ⚠ Ces mitigations **ne remplacent pas le patch**. Applique-les uniquement si tu es bloqué par un freeze de déploiement.

1. **Désactiver `session.upload_progress`** dans `php.ini` : `session.upload_progress.enabled = Off` — réduit la surface d'attaque sans corriger la faille.

2. **Réduire `post_max_size` et `upload_max_filesize`** à des valeurs strictement nécessaires — limite les vecteurs OOM (CVE-2026-109618).

3. **Activer les règles PHP RCE sur ton WAF** (Cloudflare, AWS WAF) — filtre les patterns d'exploitation connus, mais ne corrige pas la faille sous-jacente.

4. **Monitorer les logs** Apache/Nginx pour détecter des requêtes anormalement longues, des erreurs 500 en rafale, ou des payloads inhabituels.

> **PHP 8.1 et inférieur :** aucune mitigation ne compense l'absence de patch. La seule vraie solution est de migrer vers PHP 8.2+.

---

## 6. Ce qu'on retient

### Les failles core-runtime sont les plus dangereuses

La plupart des vulnérabilités web touchent une librairie ou une app. PHP2Shell touche PHP lui-même — **avant même que ton code Laravel tourne**. Il n'existe aucune défense applicative contre ça. La seule solution : garder PHP à jour.

### La fenêtre d'exploitation précède parfois le patch

PHP2Shell a été tenté 4 jours avant la publication des correctifs. Ce n'est pas un cas unique : des CVEs fuient régulièrement avant leur date de divulgation officielle. Abonne-toi aux bulletins de sécurité PHP et aux alertes de l'ANSSI/CERT.

### Les versions EOL sont un risque opérationnel permanent

PHP 8.1 est sorti de support actif. PHP 7.x, depuis longtemps. Ces versions existent encore massivement en production — et elles resteront vulnérables à PHP2Shell indéfiniment. **Migrer n'est plus une option, c'est une obligation de sécurité.**

---

## Sources

1. [The Hacker News — PHP Issues Emergency Patches for Two Critical PHP2Shell RCE Flaws](https://thehackernews.com/2026/10/php-issues-emergency-patches-for-two.html)
2. [Tenable — PHP2Shell FAQ (CVE-2026-109618 & CVE-2026-109619)](https://www.tenable.com/blog/php2shell-cve-2026-109618-cve-2026-109619-frequently-asked-questions-about-critical-php-remote)
3. [Rapid7 — Critical Unauthenticated RCE in PHP](https://www.rapid7.com/blog/post/etr-cve-2026-109618-and-cve-2026-109619-critical-unauthenticated-remote-code-execution-in-php/)
4. [JFrog — PHP2Shell: All You Need to Know](https://research.jfrog.com/post/php2shell-all-you-need-to-know-about-cve-2026-109618-and-cve-2026-109619/)
5. [Huntress — Rapid Response: PHP2Shell](https://www.huntress.com/blog/php2shell-leaked-critical-php-vulnerabilities)
6. [Datadog Security Labs — PHP2Shell analysis](https://securitylabs.datadoghq.com/articles/php2shell-critical-vulnerabilities-in-php/)
7. [Help Net Security — PHP2Shell vulnerabilities patched](https://www.helpnetsecurity.com/2026/10/21/php2shell-php-vulnerabilities-patched-cve-2026-109618-cve-2026-109619/)
8. [CERT-EU Advisory 2026-018](https://cert.europa.eu/publications/security-advisories/2026-018/)
9. [NCSC UK — Critical security issue affecting PHP](https://www.ncsc.gov.uk/news/critical-security-issue-affecting-php)
10. [Sansec Research — PHP2Shell](https://sansec.io/research/php2shell)
11. [Laravel News — PHP Security Releases Fix Two Critical RCE Vulnerabilities](https://laravel-news.com/php-critical-rce-security-releases)
12. [PHP.net — Annonces de versions officielles](https://www.php.net/)
MARKDOWN;
    }
}
