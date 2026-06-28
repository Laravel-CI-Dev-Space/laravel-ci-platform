# Design Brief — Cartes Membre Laravel CI
**Destinataire :** Claude Design  
**Projet :** Laravel CI — La communauté Laravel de Côte d'Ivoire  
**Format de livraison souhaité :** 3 templates SVG ou HTML/CSS statiques, format carte de visite numérique

---

## 1. Contexte

Laravel CI est la première communauté structurée de développeurs Laravel en Côte d'Ivoire.
Chaque membre actif débloque automatiquement une **carte membre numérique** selon son score de réputation.
Ces cartes peuvent être partagées sur LinkedIn, Twitter/X, WhatsApp ou téléchargées en PNG.

**3 niveaux de carte, débloqués par paliers de réputation :**

| Niveau | Seuil | Nom du niveau | Statut perçu |
|--------|-------|---------------|--------------|
| 1 | 300 pts | **Initié** | Membre actif reconnu |
| 2 | 600 pts | **Bâtisseur** | Contributeur régulier |
| 3 | 900 pts | **Maître Artisan** | Pilier de la communauté |

---

## 2. Dimensions & format

```
Largeur  : 800 px  (ratio paysage 3:2, adapté pour partage réseaux sociaux)
Hauteur  : 450 px
Radius   : 22 px
Export   : PNG @2x (1600 × 900 px pour qualité print/retina)
```

---

## 3. Identité visuelle — Design System

### 3.1 Palette de couleurs

```
/* Couleurs principales */
--orange       : #FF6600    ← couleur signature de la marque
--orange-600   : #E65C00    ← hover / ombre orange
--navy         : #1C1C2E    ← fond sombre principal
--navy-700     : #2A2A42    ← fond sombre secondaire
--navy-500     : #3A3A58    ← surface card sombre

/* Couleurs fonctionnelles */
--green        : #2ECC71    ← succès, validation, badge actif
--white        : #FFFFFF
--light        : #F8F9FA    ← fond clair
--text         : #2D2D2D
--muted        : #6C757D
--border       : #E9ECEF

/* Niveaux (utilisés pour les grades) */
--level-beginner     : #2ECC71  ← vert
--level-intermediate : #FF9F1C  ← ambre
--level-advanced     : #E63946  ← rouge
```

### 3.2 Typographie

```
Police principale : Outfit (Google Fonts)
  — weights utilisés : 400 (regular), 500 (medium), 600 (semibold), 700 (bold)
  — letter-spacing titres : -0.02em à -0.035em
  — line-height : 1.04 à 1.15 pour les titres

Police secondaire (code / technique) : JetBrains Mono
  — utilisée pour les tags techniques, les identifiants, les slugs
  — weight 400 à 700
```

### 3.3 Style graphique général

Le site a un style **tech artisanal avec influence brutaliste légère** :
- Ombres portées dures (hard shadows) : `7px 7px 0 #1C1C2E` sur navy, `6px 6px 0 #FF6600` sur orange
- Bordures nettes, pas d'effets flou excessif
- Grille de fond subtile (lines croisées grises opacity 0.5) sur les sections héros
- Texture grain sur certains fonds (via SVG noise filter)
- Touches de bruit/grain sur les fonds sombres pour donner de la profondeur
- Icônes : Font Awesome 6 Free (classes `fa-solid`, `fa-brands`)
- Coins arrondis : `--radius: 14px`, `--radius-lg: 22px`

### 3.4 Assets disponibles

```
Logo mark (icône seule)  : logo-mark.png  — fond transparent, format carré
Logo complet             : logo.png       — texte + icône
Mascotte                 : mascot.png     — personnage artisan (optionnel sur carte niveau 3)
```

---

## 4. Contenu de la carte (données dynamiques)

Chaque carte affiche les informations suivantes :

```
[1] Nom complet du membre          ex. "Wilson Kouassi"
[2] Nom d'utilisateur (@handle)    ex. "@ky-wilson"  ← font mono
[3] Avatar circulaire              photo de profil GitHub (64px × 64px)
[4] Niveau / grade                 ex. "Maître Artisan"
[5] Score de réputation            ex. "924 pts"
[6] Membre depuis                  ex. "Membre depuis juin 2026"
[7] Logo + nom communauté          "Laravel CI"  (coin bas-gauche ou bas-droite)
[8] QR code                        lien vers le profil public /members/@handle
[9] Badge niveau (icône + couleur) icône Font Awesome du grade correspondant
```

Zones à prévoir (placeholder rectangulaires) pour les données dynamiques.

---

## 5. Les 3 templates de carte

---

### CARTE NIVEAU 1 — "Initié" (300 pts)

**Concept :** Carte lumineuse, propre, tech. Sentiment de premier accomplissement.

**Palette :**
```
Fond principal    : #FFFFFF ou #F8F9FA (blanc / blanc cassé)
Accent principal  : #FF6600 (orange) — bande latérale gauche ou header coloré
Texte principal   : #1C1C2E (navy)
Texte secondaire  : #6C757D (muted)
Bordure           : 2px solid #1C1C2E
Ombre             : 6px 6px 0 #1C1C2E (hard shadow brutaliste)
```

**Structure suggérée :**
```
┌──────────────────────────────────────────────────┐
│ ░░ [bande orange 8px gauche]                      │
│                                                   │
│  [Avatar 56px]  Wilson Kouassi          [Logo CI] │
│                 @ky-wilson  (mono)                │
│                                                   │
│  ─────────────────────────────────────────────    │
│                                                   │
│  🌱 INITIÉ                        300 pts         │
│  Membre actif reconnu             ████████░░░░    │
│                                                   │
│  Membre depuis juin 2026         [QR code 56px]   │
└──────────────────────────────────────────────────┘
```

**Détails :**
- Fond blanc avec grille de points subtile (opacity 0.05) sur le fond
- Bande verticale orange à gauche (8px, hauteur complète)
- Badge grade : fond `#FFF1E6`, texte `#FF6600`, icône `fa-solid fa-seedling`
- Barre de progression points (300/900) en orange pâle → orange
- QR code en bas-droit, coins navy

---

### CARTE NIVEAU 2 — "Bâtisseur" (600 pts)

**Concept :** Carte dark, technique. Sentiment de montée en puissance.

**Palette :**
```
Fond principal    : #1C1C2E (navy profond)
Surface interne   : #2A2A42 (navy-700)
Accent principal  : #FF6600 (orange)
Accent secondaire : #2ECC71 (vert — validation, activité)
Texte principal   : #FFFFFF
Texte secondaire  : rgba(255,255,255,0.60)
Bordure           : 1px solid rgba(255,255,255,0.12)
Lueur orange      : box-shadow 0 0 40px rgba(255,102,0,0.25)
```

**Structure suggérée :**
```
┌──────────────────────────────────────────────────┐  ← fond navy + légère texture grain
│                                              ···· │  ← motif points blancs opacity 0.04
│  [Avatar 64px]  Wilson Kouassi     🟢 ACTIF       │
│   [ring orange] @ky-wilson                        │
│                                                   │
│  ════════════════════════════════════════════     │  ← divider orange
│                                                   │
│  🔨 BÂTISSEUR                       600 pts       │
│     Contributeur régulier        ██████████░░     │
│                                                   │
│ [Laravel CI logo blanc]   Juin 2026  [QR code]   │
└──────────────────────────────────────────────────┘
```

**Détails :**
- Fond `#1C1C2E` avec texture grain (SVG noise filter, opacity 0.06)
- Avatar avec anneau orange `3px solid #FF6600`
- Indicateur vert "ACTIF" (petit point `#2ECC71` + texte)
- Divider ligne orange (#FF6600, 2px, 80% largeur)
- Badge grade : fond `rgba(255,102,0,.15)`, bordure `rgba(255,102,0,.4)`, texte orange, icône `fa-solid fa-hammer`
- Logo communauté en blanc bas-gauche
- QR code à fond blanc coins arrondis bas-droit

---

### CARTE NIVEAU 3 — "Maître Artisan" (900+ pts)

**Concept :** Carte premium, prestige. Signature visuelle forte. Digne d'être partagée.

**Palette :**
```
Fond principal    : dégradé diagonal #1C1C2E → #2A1A0E (navy vers brun très sombre)
Accent or/premium : #FFD700 → #FF9F1C  (gradient gold-orange)
Accent orange     : #FF6600
Texte principal   : #FFFFFF
Texte secondaire  : rgba(255,255,255,0.70)
Bordure           : 1.5px solid rgba(255,215,0,0.40)  ← teinte dorée
Lueur dorée       : box-shadow 0 0 60px rgba(255,215,0,0.20), inset 0 0 80px rgba(255,102,0,0.08)
```

**Structure suggérée :**
```
┌──────────────────────────────────────────────────┐
│ ★ ★ ★   MAÎTRE ARTISAN     ✦ Laravel CI  ✦       │  ← header bande gradient gold
│──────────────────────────────────────────────────│
│                                                   │
│  [Avatar 72px   Wilson Kouassi                    │
│   ring gold]    @ky-wilson                  924   │
│                 Pilier de la communauté     pts   │
│                                                   │
│  ┌──────────────────────────────┐                 │
│  │  🔧 fa-screwdriver-wrench   │  [QR code]      │
│  │  Artisan Laravel             │                 │
│  └──────────────────────────────┘                 │
│                                                   │
│  laravel.ci/@ky-wilson          Membre juin 2026  │
└──────────────────────────────────────────────────┘
```

**Détails :**
- Fond dégradé diagonal navy → très sombre brun-noir
- Header strip (hauteur 36px) avec dégradé `#FFD700 → #FF9F1C → #FF6600` et les 3 étoiles + nom communauté en blanc
- Texture grain plus prononcée (opacity 0.08) pour effet premium
- Avatar 72px avec anneau double : `3px solid #FFD700`, gap 2px, `2px solid #FF6600`
- Points de réputation mis en valeur — grand chiffre blanc + "pts" en orange
- Badge grade en carte interne navy-700 avec bordure dorée
- URL profil en font mono bas-gauche en orange
- Légère lueur dorée sur les bords (glow effect)
- Optionnel : micro-illustration mascotte en arrière-plan (opacity 0.04, coin bas-droit)

---

## 6. Règles communes aux 3 cartes

| Règle | Valeur |
|-------|--------|
| Police des noms | Outfit Bold 700, letter-spacing -0.02em |
| Police des handles | JetBrains Mono 500, color orange ou muted |
| Avatar | Toujours circulaire, `border-radius: 50%` |
| QR code | Fond blanc, coins `radius 8px`, taille 64×64px sur la carte |
| Logo CI | Toujours présent, coin inférieur (gauche ou droit selon niveau) |
| Pas d'information sensible | Pas d'email, pas de téléphone |
| Responsive | Les cartes sont conçues pour export PNG, pas pour responsive web |

---

## 7. Grille de placement (référentiel 800×450)

```
Marges internes : 28px sur tous les côtés
Zone avatar     : x=28, y=28 — 56/64/72px selon niveau
Zone nom        : x=108 (après avatar), y=28
Zone badge      : y=220 environ (moitié basse de carte)
Zone bas        : y=390 — logo + QR + date
```

---

## 8. Notes pour l'implémentation future

- Les cartes seront générées côté serveur en **HTML → PNG** via un headless browser (Puppeteer ou `spatie/browsershot`)
- Les données dynamiques seront injectées via des variables Blade
- Le QR code sera généré par `simplesoftwareio/simple-qrcode`
- La détection du niveau (300/600/900) se fait sur `users.reputation_points`
- Route de téléchargement prévue : `GET /members/{username}/card.png`

---

*Document généré le 25 juin 2026 — Laravel CI Design System v1.0*
