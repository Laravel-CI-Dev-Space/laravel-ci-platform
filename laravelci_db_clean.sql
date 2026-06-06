-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 06, 2026 at 10:07 PM
-- Server version: 8.0.46-0ubuntu0.24.04.2
-- PHP Version: 8.3.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laravelci_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_origin_sections`
--

CREATE TABLE `about_origin_sections` (
  `id` bigint UNSIGNED NOT NULL,
  `eyebrow` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Small label above title e.g. Notre naissance',
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Rich text / HTML content',
  `media_type` enum('image','video','youtube','none') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `media_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Image path or video path in public/assets/',
  `youtube_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'YouTube embed URL if media_type is youtube',
  `media_position` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'right' COMMENT 'left or right — image/video position relative to text',
  `caption` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Optional caption under media',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `about_origin_sections`
--

INSERT INTO `about_origin_sections` (`id`, `eyebrow`, `title`, `content`, `media_type`, `media_path`, `youtube_url`, `media_position`, `caption`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Notre naissance', 'Comment tout a commencé', '<p>Laravel Côte d\'Ivoire est né d\'une conviction simple : les développeurs ivoiriens méritent un espace structuré, en français, adapté à leur contexte local. Ce qui a commencé comme un groupe WhatsApp de quelques passionnés de Laravel à Abidjan est rapidement devenu une communauté de plus de 500 développeurs, unie par l\'amour du code propre et l\'envie de grandir ensemble.</p><p>La communauté a été fondée en 2026 avec une mission claire : créer le hub de référence pour les développeurs Laravel en Côte d\'Ivoire et dans la diaspora ivoirienne.</p>', 'none', NULL, NULL, 'right', NULL, 1, '2026-06-06 21:51:42', '2026-06-06 21:51:42');

-- --------------------------------------------------------

--
-- Table structure for table `admin_activity_logs`
--

CREATE TABLE `admin_activity_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'e.g. user.ban, article.publish, job.reject',
  `subject_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Model class name',
  `subject_id` bigint UNSIGNED DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `old_values` json DEFAULT NULL COMMENT 'Before state',
  `new_values` json DEFAULT NULL COMMENT 'After state',
  `metadata` json DEFAULT NULL COMMENT 'Extra context',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `all_event_registrations`
-- (See below for the actual view)
--
CREATE TABLE `all_event_registrations` (
`id` bigint unsigned
,`participant_type` varchar(6)
,`event_id` bigint unsigned
,`display_name` varchar(255)
,`email` varchar(255)
,`photo` varchar(255)
,`whatsapp` varchar(30)
,`status` varchar(10)
,`amount_paid` decimal(10,2)
,`promo_code_used` varchar(50)
,`discount_applied` decimal(10,2)
,`payment_status` varchar(8)
,`ticket_number` varchar(30)
,`registered_at` timestamp
,`created_at` timestamp
,`updated_at` timestamp
);

-- --------------------------------------------------------

--
-- Table structure for table `analytics_events`
--

CREATE TABLE `analytics_events` (
  `id` bigint UNSIGNED NOT NULL,
  `session_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_id` bigint UNSIGNED DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `analytics_page_views`
--

CREATE TABLE `analytics_page_views` (
  `id` bigint UNSIGNED NOT NULL,
  `session_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `query_string` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referrer` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_type` enum('desktop','mobile','tablet') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'desktop',
  `browser` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `id` bigint UNSIGNED NOT NULL,
  `question_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `body_html` longtext COLLATE utf8mb4_unicode_ci COMMENT 'Compiled Markdown HTML cached',
  `is_accepted` tinyint(1) NOT NULL DEFAULT '0',
  `votes_score` int NOT NULL DEFAULT '0',
  `comments_count` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `reviewed_by` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(350) COLLATE utf8mb4_unicode_ci NOT NULL,
  `excerpt` text COLLATE utf8mb4_unicode_ci,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `body_html` longtext COLLATE utf8mb4_unicode_ci COMMENT 'Compiled Markdown HTML cached',
  `cover_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` enum('beginner','intermediate','advanced') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('draft','pending','published','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `views_count` int UNSIGNED NOT NULL DEFAULT '0',
  `comments_count` int UNSIGNED NOT NULL DEFAULT '0',
  `newsletter_sent` tinyint(1) NOT NULL DEFAULT '0',
  `published_at` timestamp NULL DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `edited_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `article_tag`
--

CREATE TABLE `article_tag` (
  `article_id` bigint UNSIGNED NOT NULL,
  `tag_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` bigint UNSIGNED NOT NULL,
  `commentable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commentable_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_hidden` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `community_values`
--

CREATE TABLE `community_values` (
  `id` bigint UNSIGNED NOT NULL,
  `icon` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Font Awesome class',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` smallint UNSIGNED NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `community_values`
--

INSERT INTO `community_values` (`id`, `icon`, `title`, `description`, `order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'fa-solid fa-award', 'Excellence', 'Nous nous tenons mutuellement à un niveau élevé — du code propre, un vrai artisanat.', 1, 1, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(2, 'fa-solid fa-hands-holding-circle', 'Partage', 'La connaissance grandit quand on la donne. Pas de rétention ici.', 2, 1, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(3, 'fa-solid fa-people-group', 'Inclusion', 'Chaque niveau, chaque parcours, chaque question est bienvenu.', 3, 1, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(4, 'fa-solid fa-seedling', 'Impact', 'Nous construisons de la tech qui améliore la vie en Côte d\'Ivoire.', 4, 1, '2026-06-06 21:51:42', '2026-06-06 21:51:42');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` bigint UNSIGNED NOT NULL,
  `submitted_by` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Verified by admin',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_accounts`
--

CREATE TABLE `company_accounts` (
  `id` bigint UNSIGNED NOT NULL,
  `company_id` bigint UNSIGNED DEFAULT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Poste occupé dans l''entreprise',
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','active','suspended','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `password_changed_at` timestamp NULL DEFAULT NULL COMMENT 'Null = doit changer au 1er login',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_registration_requests`
--

CREATE TABLE `company_registration_requests` (
  `id` bigint UNSIGNED NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Poste du responsable',
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_domain` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Domaine d''activité',
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Logo de la company uploadé lors de la demande',
  `motivation` text COLLATE utf8mb4_unicode_ci COMMENT 'Message de présentation',
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `reviewed_by` bigint UNSIGNED DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` bigint UNSIGNED NOT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `program` longtext COLLATE utf8mb4_unicode_ci COMMENT 'Detailed program / agenda',
  `type` enum('meetup','webinar','hackathon','conference','workshop') COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Physical address',
  `online_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Zoom/Meet link',
  `cover_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `starts_at` datetime NOT NULL,
  `ends_at` datetime NOT NULL,
  `capacity` smallint UNSIGNED DEFAULT NULL COMMENT 'Max attendees, null = unlimited',
  `registrations_count` smallint UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Cached count',
  `waitlist_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  `price` decimal(10,2) DEFAULT NULL COMMENT 'Base price, null if free',
  `currency` char(3) COLLATE utf8mb4_unicode_ci DEFAULT 'XOF' COMMENT 'ISO 4217 currency code',
  `promo_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `promo_discount_type` enum('percent','fixed') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `promo_discount_value` decimal(10,2) DEFAULT NULL,
  `promo_expires_at` datetime DEFAULT NULL,
  `promo_max_uses` int UNSIGNED DEFAULT NULL COMMENT 'null = unlimited',
  `promo_uses_count` int UNSIGNED NOT NULL DEFAULT '0',
  `ticketing_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `ticket_prefix` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Prefix for ticket numbers, e.g. LCI',
  `guest_registration_enabled` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Allow non-member public registrations',
  `status` enum('draft','published','cancelled','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `cancellation_reason` text COLLATE utf8mb4_unicode_ci,
  `reminder_7d_sent` tinyint(1) NOT NULL DEFAULT '0',
  `reminder_1d_sent` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `created_by`, `title`, `slug`, `description`, `program`, `type`, `location`, `online_url`, `cover_image`, `starts_at`, `ends_at`, `capacity`, `registrations_count`, `waitlist_enabled`, `is_paid`, `price`, `currency`, `promo_code`, `promo_discount_type`, `promo_discount_value`, `promo_expires_at`, `promo_max_uses`, `promo_uses_count`, `ticketing_enabled`, `ticket_prefix`, `guest_registration_enabled`, `status`, `cancellation_reason`, `reminder_7d_sent`, `reminder_1d_sent`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Meetup Laravel CI — Abidjan Juillet 2026', 'meetup-laravel-ci-abidjan-juillet-2026', '<h2>Rejoignez-nous pour notre meetup mensuel !</h2>\n<p>\n    Ce meetup est l\'occasion parfaite pour les développeurs Laravel de Côte d\'Ivoire\n    de se retrouver, partager leurs expériences et découvrir les nouveautés de l\'écosystème Laravel.\n</p>\n<h3>Au programme</h3>\n<ul>\n    <li>Tour de table des participants</li>\n    <li>Présentation : <strong>Laravel 13 — ce qui change vraiment</strong></li>\n    <li>Retour d\'expérience : déploiement Laravel sur infrastructure africaine</li>\n    <li>Open discussion et networking</li>\n</ul>\n<p>Entrée <strong>gratuite</strong> — places limitées, inscription obligatoire.</p>', '<ul>\n    <li><strong>18h00</strong> — Accueil des participants</li>\n    <li><strong>18h30</strong> — Présentation principale (45 min)</li>\n    <li><strong>19h15</strong> — Retour d\'expérience (20 min)</li>\n    <li><strong>19h35</strong> — Questions / Réponses</li>\n    <li><strong>20h00</strong> — Networking & Rafraîchissements</li>\n    <li><strong>21h00</strong> — Fin de soirée</li>\n</ul>', 'meetup', 'Jokkolabs Abidjan, Rue des Jardins, Cocody', NULL, NULL, '2026-07-15 18:00:00', '2026-07-15 21:00:00', 60, 0, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, 'LCI', 1, 'published', NULL, 0, 0, '2026-06-06 21:51:42', '2026-06-06 21:51:42', NULL),
(2, 1, 'Workshop Filament 5 — Abidjan Août 2026', 'workshop-filament-5-abidjan-aout-2026', '<h2>Workshop intensif — Filament 5 de A à Z</h2>\n<p>\n    Une journée complète pour maîtriser <strong>Filament 5</strong> :\n    ressources, schémas, infolists, actions personnalisées et déploiement en production.\n</p>\n<h3>Ce que vous apprendrez</h3>\n<ul>\n    <li>Architecture de Filament 5 et nouveautés par rapport à la v3</li>\n    <li>Construire un back-office complet de zéro</li>\n    <li>Plugins, relation managers et composants personnalisés</li>\n    <li>Tests unitaires et d\'intégration d\'un panneau Filament</li>\n    <li>Bonnes pratiques de performance en production</li>\n</ul>\n<p>\n    <strong>Places limitées à 25 participants</strong> pour garantir un suivi personnalisé.\n    Code promo <code>EARLYBIRD</code> disponible pour les 10 premiers inscrits : <strong>-30%</strong>.\n</p>', '<ul>\n    <li><strong>08h30</strong> — Accueil & café</li>\n    <li><strong>09h00</strong> — Introduction à Filament 5 (architecture, nouveautés)</li>\n    <li><strong>10h30</strong> — Pause</li>\n    <li><strong>10h45</strong> — Atelier : construire une ressource complète</li>\n    <li><strong>13h00</strong> — Déjeuner (inclus)</li>\n    <li><strong>14h00</strong> — Plugins, actions avancées & relation managers</li>\n    <li><strong>16h00</strong> — Pause</li>\n    <li><strong>16h15</strong> — Tests & mise en production</li>\n    <li><strong>17h30</strong> — Q&R et clôture</li>\n</ul>', 'workshop', 'Hub Afrique Numérique, Plateau, Abidjan', NULL, NULL, '2026-08-22 08:30:00', '2026-08-22 18:00:00', 25, 0, 0, 1, 15000.00, 'XOF', 'EARLYBIRD', 'percent', 30.00, '2026-08-01 23:59:59', 10, 0, 1, 'LCI', 1, 'published', NULL, 0, 0, '2026-06-06 21:51:42', '2026-06-06 21:51:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `event_registrations`
--

CREATE TABLE `event_registrations` (
  `id` bigint UNSIGNED NOT NULL,
  `event_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `status` enum('pending','confirmed','waitlisted','cancelled','attended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'confirmed',
  `amount_paid` decimal(10,2) DEFAULT NULL COMMENT 'Amount charged after discount, null = free',
  `promo_code_used` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_applied` decimal(10,2) DEFAULT NULL,
  `payment_status` enum('pending','paid','free','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'free',
  `ticket_number` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ticket_qr_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reminder_sent` tinyint(1) NOT NULL DEFAULT '0',
  `ical_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Token for iCal export',
  `registered_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancellation_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guest_registrations`
--

CREATE TABLE `guest_registrations` (
  `id` bigint UNSIGNED NOT NULL,
  `event_id` bigint UNSIGNED NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `whatsapp` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Path relative to public/assets/web/img/guests/',
  `status` enum('confirmed','waitlisted','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'confirmed',
  `amount_paid` decimal(10,2) DEFAULT NULL,
  `promo_code_used` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_applied` decimal(10,2) DEFAULT NULL,
  `payment_status` enum('pending','paid','free') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'free',
  `ticket_number` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ticket_qr_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registered_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancellation_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `home_stats`
--

CREATE TABLE `home_stats` (
  `id` bigint UNSIGNED NOT NULL,
  `icon` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Font Awesome class e.g. fa-solid fa-users',
  `label` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'e.g. Members, Questions',
  `value` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Manual override value',
  `suffix` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '+' COMMENT 'e.g. + or empty',
  `auto_count` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'If true, count from DB instead of manual value',
  `model` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Model class name for auto count e.g. App\\Models\\User',
  `order` smallint UNSIGNED NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `home_stats`
--

INSERT INTO `home_stats` (`id`, `icon`, `label`, `value`, `suffix`, `auto_count`, `model`, `order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'fa-solid fa-users', 'Members', 500, '+', 1, 'App\\Models\\User', 1, 1, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(2, 'fa-solid fa-circle-question', 'Questions', 1200, '+', 1, 'App\\Models\\Question', 2, 1, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(3, 'fa-solid fa-calendar-check', 'Events', 24, '+', 1, 'App\\Models\\Event', 3, 1, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(4, 'fa-solid fa-book-open', 'Articles', 80, '+', 1, 'App\\Models\\Article', 4, 1, '2026-06-06 21:51:42', '2026-06-06 21:51:42');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_applications`
--

CREATE TABLE `job_applications` (
  `id` bigint UNSIGNED NOT NULL,
  `job_offer_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `cv_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Uploaded CV path in public/assets/cv/',
  `cover_letter` text COLLATE utf8mb4_unicode_ci,
  `portfolio_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedin_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','viewed','shortlisted','accepted','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `employer_note` text COLLATE utf8mb4_unicode_ci COMMENT 'Private note from employer',
  `viewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_favorites`
--

CREATE TABLE `job_favorites` (
  `id` bigint UNSIGNED NOT NULL,
  `job_offer_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_offers`
--

CREATE TABLE `job_offers` (
  `id` bigint UNSIGNED NOT NULL,
  `company_id` bigint UNSIGNED DEFAULT NULL,
  `posted_by` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `contract_type` enum('cdi','cdd','freelance','internship','apprenticeship') COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` enum('junior','intermediate','senior','lead','any') COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_remote` tinyint(1) NOT NULL DEFAULT '0',
  `is_hybrid` tinyint(1) NOT NULL DEFAULT '0',
  `salary_min` int UNSIGNED DEFAULT NULL COMMENT 'In local currency',
  `salary_max` int UNSIGNED DEFAULT NULL,
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'XOF',
  `salary_visible` tinyint(1) NOT NULL DEFAULT '1',
  `status` enum('draft','pending','active','expired','rejected','filled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `is_urgent` tinyint(1) NOT NULL DEFAULT '0',
  `cover_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Image de couverture de l''offre',
  `attachment_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Document joint (PDF/DOC) avec détails de l''offre',
  `attachment_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nom original du document',
  `views_count` int UNSIGNED NOT NULL DEFAULT '0',
  `applications_count` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Cached count',
  `expires_at` timestamp NULL DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_offer_categories`
--

CREATE TABLE `job_offer_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `offers_count` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_offer_categories`
--

INSERT INTO `job_offer_categories` (`id`, `name`, `slug`, `icon`, `offers_count`, `created_at`, `updated_at`) VALUES
(1, 'Backend Development', 'backend-development', 'heroicon-o-server', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(2, 'Frontend Development', 'frontend-development', 'heroicon-o-computer-desktop', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(3, 'Full Stack', 'full-stack', 'heroicon-o-code-bracket', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(4, 'Mobile Development', 'mobile-development', 'heroicon-o-device-phone-mobile', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(5, 'DevOps & Cloud', 'devops-cloud', 'heroicon-o-cloud', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(6, 'Data Science & AI', 'data-science-ai', 'heroicon-o-chart-bar', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(7, 'UI/UX Design', 'uiux-design', 'heroicon-o-paint-brush', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(8, 'Project Management', 'project-management', 'heroicon-o-clipboard-document-list', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(9, 'QA & Testing', 'qa-testing', 'heroicon-o-bug-ant', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(10, 'Cybersecurity', 'cybersecurity', 'heroicon-o-shield-check', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41');

-- --------------------------------------------------------

--
-- Table structure for table `job_offer_category`
--

CREATE TABLE `job_offer_category` (
  `job_offer_id` bigint UNSIGNED NOT NULL,
  `job_offer_category_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_offer_skill`
--

CREATE TABLE `job_offer_skill` (
  `job_offer_id` bigint UNSIGNED NOT NULL,
  `job_skill_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_skills`
--

CREATE TABLE `job_skills` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `offers_count` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_skills`
--

INSERT INTO `job_skills` (`id`, `name`, `slug`, `offers_count`, `created_at`, `updated_at`) VALUES
(1, 'Laravel', 'laravel', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(2, 'PHP', 'php', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(3, 'Livewire', 'livewire', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(4, 'Filament', 'filament', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(5, 'Vue.js', 'vuejs', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(6, 'React', 'react', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(7, 'Next.js', 'nextjs', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(8, 'Nuxt.js', 'nuxtjs', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(9, 'TypeScript', 'typescript', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(10, 'JavaScript', 'javascript', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(11, 'Python', 'python', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(12, 'MySQL', 'mysql', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(13, 'PostgreSQL', 'postgresql', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(14, 'MongoDB', 'mongodb', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(15, 'Redis', 'redis', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(16, 'Docker', 'docker', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(17, 'Linux', 'linux', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(18, 'Git', 'git', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(19, 'AWS', 'aws', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(20, 'TailwindCSS', 'tailwindcss', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(21, 'Bootstrap', 'bootstrap', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(22, 'REST API', 'rest-api', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(23, 'GraphQL', 'graphql', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(24, 'Nginx', 'nginx', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(25, 'Node.js', 'nodejs', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(26, 'Flutter', 'flutter', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(27, 'Dart', 'dart', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(28, 'Kotlin', 'kotlin', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(29, 'Swift', 'swift', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_05_17_172216_create_permission_tables', 1),
(5, '2026_05_18_115300_create_profiles_table', 1),
(6, '2026_05_28_093024_create_tags_table', 1),
(7, '2026_05_28_093025_create_questions_table', 1),
(8, '2026_05_28_093026_create_answers_table', 1),
(9, '2026_05_28_093027_add_accepted_answer_id_to_questions_table', 1),
(10, '2026_05_28_093027_create_comments_table', 1),
(11, '2026_05_28_093028_create_question_tag_table', 1),
(12, '2026_05_28_093028_create_votes_table', 1),
(13, '2026_05_28_093029_create_articles_table', 1),
(14, '2026_05_28_093030_create_article_tag_table', 1),
(15, '2026_05_28_093031_create_resources_table', 1),
(16, '2026_05_28_093032_create_events_table', 1),
(17, '2026_05_28_093033_create_companies_table', 1),
(18, '2026_05_28_093034_create_event_registrations_table', 1),
(19, '2026_05_28_093035_create_job_offers_table', 1),
(20, '2026_05_28_093036_create_job_offer_categories_table', 1),
(21, '2026_05_28_093037_create_job_skills_table', 1),
(22, '2026_05_28_093038_create_job_applications_table', 1),
(23, '2026_05_28_093039_create_job_favorites_table', 1),
(24, '2026_05_28_093040_create_reports_table', 1),
(25, '2026_05_28_093041_create_admin_activity_logs_table', 1),
(26, '2026_06_01_000001_create_site_settings_table', 1),
(27, '2026_06_01_000002_create_home_stats_table', 1),
(28, '2026_06_01_000003_create_partners_table', 1),
(29, '2026_06_01_000004_create_team_members_table', 1),
(30, '2026_06_01_000005_create_community_values_table', 1),
(31, '2026_06_01_000006_create_timeline_events_table', 1),
(32, '2026_06_01_000007_create_about_origin_sections_table', 1),
(33, '2026_06_01_181103_add_accepted_answer_id_to_questions_table', 1),
(34, '2026_06_02_161355_add_edited_at_to_articles_table', 1),
(35, '2026_06_02_161355_add_edited_at_to_questions_table', 1),
(36, '2026_06_04_135319_create_company_accounts_table', 1),
(37, '2026_06_04_135319_create_company_registration_requests_table', 1),
(38, '2026_06_04_145557_add_media_and_fix_posted_by_to_job_offers', 1),
(39, '2026_06_04_220002_add_logo_to_company_registration_requests', 1),
(40, '2026_06_06_000001_add_pricing_to_events_table', 1),
(41, '2026_06_06_000002_add_payment_to_event_registrations_table', 1),
(42, '2026_06_06_100001_add_ticketing_to_events_table', 1),
(43, '2026_06_06_100002_add_ticket_to_event_registrations_table', 1),
(44, '2026_06_06_100003_create_guest_registrations_table', 1),
(45, '2026_06_06_210001_create_analytics_page_views_table', 1),
(46, '2026_06_06_210002_create_analytics_events_table', 1),
(47, '2026_06_07_000001_create_all_event_registrations_view', 1);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(3, 'App\\Models\\User', 3),
(4, 'App\\Models\\User', 4),
(4, 'App\\Models\\User', 5),
(4, 'App\\Models\\User', 6);

-- --------------------------------------------------------

--
-- Table structure for table `partners`
--

CREATE TABLE `partners` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Path in public/assets/web/img/partners/',
  `icon` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Font Awesome class fallback if no logo',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'community' COMMENT 'community/sponsor/institutional',
  `order` smallint UNSIGNED NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `partners`
--

INSERT INTO `partners` (`id`, `name`, `logo`, `icon`, `url`, `type`, `order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Laravel France', NULL, 'fa-solid fa-hippo', NULL, 'community', 1, 1, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(2, 'Laravel Cameroun', NULL, 'fa-solid fa-hippo', NULL, 'community', 2, 1, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(3, 'Laravel Sénégal', NULL, 'fa-solid fa-hippo', NULL, 'community', 3, 1, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(4, 'Laravel Nigeria', NULL, 'fa-solid fa-hippo', NULL, 'community', 4, 1, '2026-06-06 21:51:42', '2026-06-06 21:51:42');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'forum.question.create', 'web', '2026-06-06 21:51:40', '2026-06-06 21:51:40'),
(2, 'forum.question.edit', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(3, 'forum.question.delete', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(4, 'forum.question.pin', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(5, 'forum.answer.create', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(6, 'forum.answer.delete', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(7, 'forum.answer.accept', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(8, 'forum.vote', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(9, 'forum.comment.create', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(10, 'forum.comment.delete', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(11, 'forum.report', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(12, 'blog.article.create', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(13, 'blog.article.edit', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(14, 'blog.article.delete', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(15, 'blog.article.submit', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(16, 'blog.article.publish', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(17, 'blog.article.unpublish', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(18, 'blog.comment.create', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(19, 'blog.comment.delete', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(20, 'blog.resource.upload', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(21, 'blog.resource.download', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(22, 'blog.resource.delete', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(23, 'event.register', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(24, 'event.cancel', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(25, 'event.create', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(26, 'event.manage', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(27, 'job.apply', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(28, 'job.favorite', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(29, 'job.offer.create', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(30, 'job.offer.manage', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(31, 'job.offer.publish', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(32, 'company.offer.create', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(33, 'company.offer.manage', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(34, 'company.view.applicants', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(35, 'company.download.cv', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(36, 'moderation.report.handle', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(37, 'moderation.content.hide', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(38, 'moderation.user.suspend', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(39, 'admin.access', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(40, 'admin.user.manage', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(41, 'admin.user.ban', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(42, 'admin.settings', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41');

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE `profiles` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `laravel_level` enum('debutant','intermediaire','avance','expert','maitre') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `years_experience` enum('moins_1_an','1_3_ans','3_5_ans','5_10_ans','plus_10_ans') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tech_stack` json DEFAULT NULL,
  `academic_level` enum('bts','licence','master_ingenieur','doctorat') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `job_status` enum('en_fonction','etudiant','entrepreneur','recherche_emploi','freelance') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `portfolio_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cv` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `profiles`
--

INSERT INTO `profiles` (`id`, `user_id`, `avatar`, `country`, `city`, `district`, `bio`, `laravel_level`, `years_experience`, `tech_stack`, `academic_level`, `job_status`, `portfolio_url`, `cv`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'Côte d\'Ivoire', 'Abidjan', 'Cocody', 'Lead Developer — Laravel Côte d\'Ivoire. Passionné de PHP et Laravel.', 'expert', '5_10_ans', '[\"Laravel\", \"PHP\", \"Livewire\", \"Filament\", \"Vue.js\", \"MySQL\", \"Docker\"]', 'master_ingenieur', 'en_fonction', 'https://github.com/Ky-Wilson', NULL, '2026-06-06 21:51:41', '2026-06-06 21:51:41');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(350) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `body_html` longtext COLLATE utf8mb4_unicode_ci COMMENT 'Compiled Markdown HTML cached',
  `status` enum('published','hidden','closed','deleted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'published',
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
  `accepted_answer_id` bigint UNSIGNED DEFAULT NULL,
  `views_count` int UNSIGNED NOT NULL DEFAULT '0',
  `votes_score` int NOT NULL DEFAULT '0' COMMENT 'Sum of upvotes minus downvotes',
  `answers_count` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Cached count',
  `comments_count` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Cached count',
  `last_activity_at` timestamp NULL DEFAULT NULL COMMENT 'Last answer or comment date',
  `edited_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `question_tag`
--

CREATE TABLE `question_tag` (
  `question_id` bigint UNSIGNED NOT NULL,
  `tag_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` bigint UNSIGNED NOT NULL,
  `reporter_id` bigint UNSIGNED NOT NULL,
  `reportable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reportable_id` bigint UNSIGNED NOT NULL,
  `reason` enum('spam','inappropriate','harassment','misinformation','copyright','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','reviewed','resolved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `handled_by` bigint UNSIGNED DEFAULT NULL,
  `admin_note` text COLLATE utf8mb4_unicode_ci,
  `handled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` enum('boilerplate','cheatsheet','guide','pdf','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` bigint UNSIGNED NOT NULL COMMENT 'Size in bytes',
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `downloads_count` int UNSIGNED NOT NULL DEFAULT '0',
  `is_public` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'False = members only',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'super-admin', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(2, 'admin', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(3, 'moderator', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(4, 'member', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(5, 'company', 'web', '2026-06-06 21:51:41', '2026-06-06 21:51:41');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(1, 2),
(2, 2),
(3, 2),
(4, 2),
(5, 2),
(6, 2),
(7, 2),
(8, 2),
(9, 2),
(10, 2),
(11, 2),
(12, 2),
(13, 2),
(14, 2),
(15, 2),
(16, 2),
(17, 2),
(18, 2),
(19, 2),
(20, 2),
(21, 2),
(22, 2),
(23, 2),
(24, 2),
(25, 2),
(26, 2),
(27, 2),
(28, 2),
(29, 2),
(30, 2),
(31, 2),
(32, 2),
(33, 2),
(34, 2),
(35, 2),
(36, 2),
(37, 2),
(38, 2),
(39, 2),
(40, 2),
(41, 2),
(3, 3),
(4, 3),
(6, 3),
(10, 3),
(16, 3),
(17, 3),
(19, 3),
(22, 3),
(36, 3),
(37, 3),
(38, 3),
(39, 3),
(1, 4),
(2, 4),
(3, 4),
(5, 4),
(6, 4),
(7, 4),
(8, 4),
(9, 4),
(10, 4),
(11, 4),
(12, 4),
(13, 4),
(14, 4),
(15, 4),
(18, 4),
(19, 4),
(20, 4),
(21, 4),
(22, 4),
(23, 4),
(24, 4),
(27, 4),
(28, 4),
(29, 4),
(32, 5),
(33, 5),
(34, 5),
(35, 5);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general' COMMENT 'general/home/about/seo/social',
  `value` longtext COLLATE utf8mb4_unicode_ci,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text' COMMENT 'text/textarea/image/boolean/number/color/url/video',
  `label` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Label displayed in Filament',
  `description` text COLLATE utf8mb4_unicode_ci,
  `order` smallint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `key`, `group`, `value`, `type`, `label`, `description`, `order`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'general', 'Laravel Côte d\'Ivoire', 'text', 'Nom du site', NULL, 1, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(2, 'site_tagline', 'general', 'The Laravel Community of Côte d\'Ivoire', 'text', 'Tagline', NULL, 2, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(3, 'site_description', 'general', 'La première communauté structurée dédiée aux développeurs Laravel en Côte d\'Ivoire.', 'textarea', 'Description', NULL, 3, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(4, 'site_email', 'general', 'contact@laravelci.com', 'text', 'Email de contact', NULL, 4, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(5, 'social_github', 'social', 'https://github.com/Laravel-CI-Dev-Space', 'url', 'GitHub', NULL, 1, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(6, 'social_linkedin', 'social', '', 'url', 'LinkedIn', NULL, 2, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(7, 'social_twitter', 'social', '', 'url', 'Twitter / X', NULL, 3, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(8, 'social_whatsapp', 'social', '', 'url', 'WhatsApp (lien groupe)', NULL, 4, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(9, 'home_hero_badge', 'home', 'Laravel 13 · PHP 8.3 · Open source', 'text', 'Hero badge text', NULL, 1, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(10, 'home_hero_title', 'home', 'The Laravel Community of Côte d\'Ivoire', 'text', 'Hero titre principal', NULL, 2, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(11, 'home_hero_subtitle', 'home', 'Join 500+ developers — share, learn, and grow together.', 'textarea', 'Hero sous-titre', NULL, 3, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(12, 'home_cta_primary_label', 'home', 'Join the Community', 'text', 'CTA principal label', NULL, 4, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(13, 'home_cta_secondary_label', 'home', 'Explore the Forum', 'text', 'CTA secondaire label', NULL, 5, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(14, 'home_questions_preview', 'home', '3', 'number', 'Nb questions page d\'accueil', NULL, 6, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(15, 'home_articles_preview', 'home', '3', 'number', 'Nb articles page d\'accueil', NULL, 7, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(16, 'home_events_preview', 'home', '3', 'number', 'Nb events page d\'accueil', NULL, 8, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(17, 'home_cta_banner_title', 'home', 'Ready to build the future of Ivorian tech?', 'text', 'Bannière CTA titre', NULL, 9, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(18, 'home_cta_banner_text', 'home', 'Sign in with GitHub, ask your first question, and meet 500+ developers who have your back.', 'textarea', 'Bannière CTA texte', NULL, 10, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(19, 'about_hero_title', 'about', 'We\'re building the home for Ivorian Laravel developers', 'text', 'About hero titre', NULL, 1, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(20, 'about_hero_subtitle', 'about', 'Laravel Côte d\'Ivoire is the first structured developer community dedicated to Laravel & PHP in Côte d\'Ivoire.', 'textarea', 'About hero sous-titre', NULL, 2, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(21, 'about_mission', 'about', 'Give every Ivorian developer a structured place to learn Laravel properly.', 'textarea', 'Mission', NULL, 3, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(22, 'about_vision', 'about', 'A West Africa where world-class software is built by local talent.', 'textarea', 'Vision', NULL, 4, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(23, 'about_cta_title', 'about', 'Your seat at the table is ready', 'text', 'About CTA titre', NULL, 5, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(24, 'about_cta_text', 'about', 'Join 500+ Ivorian developers building the future of African tech.', 'textarea', 'About CTA texte', NULL, 6, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(25, 'seo_home_title', 'seo', 'Laravel CI — The Laravel Community of Côte d\'Ivoire', 'text', 'SEO titre d\'accueil', NULL, 1, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(26, 'seo_home_description', 'seo', 'La première communauté structurée de développeurs Laravel en Côte d\'Ivoire.', 'textarea', 'SEO description d\'accueil', NULL, 2, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(27, 'seo_og_image', 'seo', '', 'image', 'OG Image (1200x630)', NULL, 3, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(28, 'identity_brand_name', 'identity', 'Laravel CI', 'text', 'Nom du brand (header)', NULL, 1, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(29, 'identity_logo_mark', 'identity', 'web/img/logo-mark.png', 'image', 'Logo mark (icône)', NULL, 2, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(30, 'identity_logo_full', 'identity', '', 'image', 'Logo complet (optionnel)', NULL, 3, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(31, 'identity_favicon', 'identity', 'web/img/favicon.png', 'image', 'Favicon', NULL, 4, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(32, 'identity_header_cta', 'identity', 'Sign in with GitHub', 'text', 'Bouton header (non connecté)', NULL, 5, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(33, 'footer_tagline', 'footer', 'The first structured developer community for Laravel & PHP in Côte d\'Ivoire and the Ivorian diaspora. African tech excellence, together.', 'textarea', 'Description / tagline', NULL, 1, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(34, 'footer_col1_title', 'footer', 'Quick Links', 'text', 'Colonne 2 — titre', NULL, 2, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(35, 'footer_col2_title', 'footer', 'Community', 'text', 'Colonne 3 — titre', NULL, 3, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(36, 'footer_col3_title', 'footer', 'Contact', 'text', 'Colonne 4 — titre', NULL, 4, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(37, 'footer_contact_location', 'footer', 'Abidjan, Côte d\'Ivoire', 'text', 'Localisation', NULL, 5, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(38, 'footer_contact_email', 'footer', 'hello@laravel.ci', 'text', 'Email de contact', NULL, 6, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(39, 'footer_whatsapp_label', 'footer', 'Join WhatsApp group', 'text', 'Label lien WhatsApp', NULL, 7, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(40, 'footer_github_label', 'footer', 'Contribute on GitHub', 'text', 'Label lien GitHub', NULL, 8, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(41, 'footer_code_of_conduct_url', 'footer', '', 'url', 'URL Code de conduite', NULL, 9, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(42, 'footer_copyright', 'footer', 'Laravel Côte d\'Ivoire · MIT License', 'text', 'Texte copyright', NULL, 10, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(43, 'footer_built_with', 'footer', 'Built with ♥ in Côte d\'Ivoire', 'text', 'Texte \"Built with\"', NULL, 11, '2026-06-06 21:51:42', '2026-06-06 21:51:42');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scope` enum('forum','blog','both') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'both',
  `color` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Hex color code e.g. #FF6600',
  `usage_count` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Cached count for performance',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `name`, `slug`, `scope`, `color`, `usage_count`, `created_at`, `updated_at`) VALUES
(1, 'Laravel', 'laravel', 'both', '#FF2D20', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(2, 'PHP', 'php', 'both', '#8892BE', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(3, 'Eloquent', 'eloquent', 'forum', '#F05340', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(4, 'Livewire', 'livewire', 'both', '#FB70A9', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(5, 'Filament', 'filament', 'both', '#F59E0B', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(6, 'API', 'api', 'both', '#3B82F6', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(7, 'Auth', 'auth', 'forum', '#8B5CF6', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(8, 'Deployment', 'deployment', 'forum', '#10B981', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(9, 'Testing', 'testing', 'both', '#EF4444', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(10, 'Queue', 'queue', 'forum', '#F97316', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(11, 'Database', 'database', 'forum', '#06B6D4', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(12, 'Performance', 'performance', 'forum', '#84CC16', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(13, 'Security', 'security', 'forum', '#DC2626', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(14, 'Vue.js', 'vuejs', 'both', '#42B883', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(15, 'React', 'react', 'both', '#61DAFB', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(16, 'TailwindCSS', 'tailwindcss', 'both', '#38BDF8', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(17, 'Docker', 'docker', 'forum', '#2496ED', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(18, 'Git', 'git', 'forum', '#F05032', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(19, 'MySQL', 'mysql', 'forum', '#4479A1', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(20, 'Redis', 'redis', 'forum', '#DC382D', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(21, 'Tutorial', 'tutorial', 'blog', '#FF6600', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(22, 'Tips', 'tips', 'blog', '#2ECC71', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(23, 'Architecture', 'architecture', 'blog', '#1C1C2E', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(24, 'Best Practices', 'best-practices', 'blog', '#9B59B6', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(25, 'News', 'news', 'blog', '#E74C3C', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(26, 'Career', 'career', 'blog', '#2980B9', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(27, 'Open Source', 'open-source', 'blog', '#27AE60', 0, '2026-06-06 21:51:41', '2026-06-06 21:51:41');

-- --------------------------------------------------------

--
-- Table structure for table `team_members`
--

CREATE TABLE `team_members` (
  `id` bigint UNSIGNED NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'e.g. Founder & Architect',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Path in public/assets/web/img/team/',
  `avatar_initials` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'e.g. SB — fallback if no avatar',
  `avatar_color` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'av-1' COMMENT 'CSS class for avatar color e.g. av-1 to av-6',
  `github_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedin_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `order` smallint UNSIGNED NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `team_members`
--

INSERT INTO `team_members` (`id`, `first_name`, `last_name`, `role`, `avatar`, `avatar_initials`, `avatar_color`, `github_url`, `linkedin_url`, `twitter_url`, `bio`, `order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Serge', 'Brou', 'Founder & Architect', NULL, 'SB', 'av-1', 'https://github.com/', NULL, NULL, 'Fondateur de Laravel CI et architecte de la plateforme. Passionné de Laravel depuis la version 5, il a lancé la communauté avec la conviction que les développeurs ivoiriens méritent un espace structuré.', 1, 1, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(2, 'Fatou', 'Diallo', 'Community Lead', NULL, 'FD', 'av-3', 'https://github.com/', NULL, NULL, 'Responsable communauté, elle coordonne les événements, les partenariats et l\'animation du forum.', 2, 1, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(3, 'Aïcha', 'Doumbia', 'Content & Events', NULL, 'AD', 'av-5', 'https://github.com/', NULL, NULL, 'Responsable contenu et événements, elle produit les articles, organise les meetups et webinaires.', 3, 1, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(4, 'Yao', 'Térence', 'Open Source Maintainer', NULL, 'YT', 'av-4', 'https://github.com/', NULL, NULL, 'Mainteneur open source, il supervise les contributions, les pull requests et la qualité du code de la plateforme.', 4, 1, '2026-06-06 21:51:42', '2026-06-06 21:51:42');

-- --------------------------------------------------------

--
-- Table structure for table `timeline_events`
--

CREATE TABLE `timeline_events` (
  `id` bigint UNSIGNED NOT NULL,
  `period` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'e.g. Jan 2026, Q1 2026',
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` smallint UNSIGNED NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `timeline_events`
--

INSERT INTO `timeline_events` (`id`, `period`, `title`, `description`, `order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Jan 2026', 'Le premier message', 'Une poignée de développeurs abidjanais lancent un groupe WhatsApp pour échanger des conseils Laravel. En une semaine, ils sont 40.', 1, 1, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(2, 'Fév 2026', 'Launch Hack', 'Notre premier hackathon réunit 72 développeurs pour un week-end de construction. La communauté a un rythme cardiaque.', 2, 1, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(3, 'Mar 2026', 'Passage à l\'open source', 'Nous construisons cette plateforme en public sous licence MIT — faite avec Laravel, pour les développeurs Laravel.', 3, 1, '2026-06-06 21:51:42', '2026-06-06 21:51:42'),
(4, 'Mai 2026', '500 membres', 'Sur WhatsApp, LinkedIn et GitHub, nous franchissons les 500 membres — et ce n\'est que le début.', 4, 1, '2026-06-06 21:51:42', '2026-06-06 21:51:42');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `github_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `github_username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `suspended_until` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `avatar`, `github_id`, `github_username`, `is_active`, `suspended_until`, `last_login_at`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Wilson Kouassi', 'yanne.kouassi@epitech.eu', 'https://avatars.githubusercontent.com/u/167759591', '167759591', 'Ky-Wilson', 1, NULL, '2026-06-06 21:51:41', '2026-06-06 21:51:41', NULL, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(2, 'Admin Test', 'admin@laravelci.com', 'https://ui-avatars.com/api/?name=Admin&color=fff&background=FF6600', '11111111', 'admin-laravel-ci', 1, NULL, '2026-06-06 21:51:41', '2026-06-06 21:51:41', NULL, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(3, 'Moderator Test', 'moderator@laravelci.com', 'https://ui-avatars.com/api/?name=Mod&color=fff&background=1C1C2E', '22222222', 'mod-laravel-ci', 1, NULL, '2026-06-06 21:51:41', '2026-06-06 21:51:41', NULL, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(4, 'Member Test', 'member@laravelci.com', 'https://ui-avatars.com/api/?name=Member&color=fff&background=2ECC71', '33333333', 'member-laravel-ci', 1, NULL, '2026-06-06 21:51:41', '2026-06-06 21:51:41', NULL, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(5, 'Suspended Test', 'suspended@laravelci.com', 'https://ui-avatars.com/api/?name=Suspended&color=fff&background=E74C3C', '44444444', 'suspended-laravel-ci', 1, '2026-06-13 21:51:41', NULL, '2026-06-06 21:51:41', NULL, '2026-06-06 21:51:41', '2026-06-06 21:51:41'),
(6, 'Banned Test', 'banned@laravelci.com', 'https://ui-avatars.com/api/?name=Banned&color=fff&background=7F8C8D', '55555555', 'banned-laravel-ci', 0, NULL, NULL, '2026-06-06 21:51:41', NULL, '2026-06-06 21:51:41', '2026-06-06 21:51:41');

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` bigint UNSIGNED NOT NULL,
  `votable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `votable_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `value` tinyint NOT NULL COMMENT '1 = upvote, -1 = downvote',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure for view `all_event_registrations`
--
DROP TABLE IF EXISTS `all_event_registrations`;

CREATE ALGORITHM=UNDEFINED  SQL SECURITY DEFINER VIEW `all_event_registrations`  AS SELECT `er`.`id` AS `id`, 'member' AS `participant_type`, `er`.`event_id` AS `event_id`, `u`.`name` AS `display_name`, `u`.`email` AS `email`, `u`.`avatar` AS `photo`, NULL AS `whatsapp`, `er`.`status` AS `status`, `er`.`amount_paid` AS `amount_paid`, `er`.`promo_code_used` AS `promo_code_used`, `er`.`discount_applied` AS `discount_applied`, `er`.`payment_status` AS `payment_status`, `er`.`ticket_number` AS `ticket_number`, `er`.`registered_at` AS `registered_at`, `er`.`created_at` AS `created_at`, `er`.`updated_at` AS `updated_at` FROM (`event_registrations` `er` left join `users` `u` on((`u`.`id` = `er`.`user_id`)))union all select (`gr`.`id` + 100000000) AS `id`,'guest' AS `participant_type`,`gr`.`event_id` AS `event_id`,concat(`gr`.`first_name`,' ',`gr`.`last_name`) AS `display_name`,`gr`.`email` AS `email`,`gr`.`photo` AS `photo`,`gr`.`whatsapp` AS `whatsapp`,`gr`.`status` AS `status`,`gr`.`amount_paid` AS `amount_paid`,`gr`.`promo_code_used` AS `promo_code_used`,`gr`.`discount_applied` AS `discount_applied`,`gr`.`payment_status` AS `payment_status`,`gr`.`ticket_number` AS `ticket_number`,`gr`.`registered_at` AS `registered_at`,`gr`.`created_at` AS `created_at`,`gr`.`updated_at` AS `updated_at` from `guest_registrations` `gr` where (`gr`.`deleted_at` is null)  ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_origin_sections`
--
ALTER TABLE `about_origin_sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_activity_logs`
--
ALTER TABLE `admin_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_activity_logs_user_id_index` (`user_id`),
  ADD KEY `admin_activity_logs_action_index` (`action`),
  ADD KEY `admin_activity_logs_subject_type_subject_id_index` (`subject_type`,`subject_id`),
  ADD KEY `admin_activity_logs_created_at_index` (`created_at`);

--
-- Indexes for table `analytics_events`
--
ALTER TABLE `analytics_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `analytics_events_created_at_type_index` (`created_at`,`type`),
  ADD KEY `analytics_events_user_id_type_index` (`user_id`,`type`),
  ADD KEY `analytics_events_session_id_index` (`session_id`),
  ADD KEY `analytics_events_type_index` (`type`),
  ADD KEY `analytics_events_created_at_index` (`created_at`);

--
-- Indexes for table `analytics_page_views`
--
ALTER TABLE `analytics_page_views`
  ADD PRIMARY KEY (`id`),
  ADD KEY `analytics_page_views_user_id_foreign` (`user_id`),
  ADD KEY `analytics_page_views_created_at_path_index` (`created_at`,`path`),
  ADD KEY `analytics_page_views_session_id_index` (`session_id`),
  ADD KEY `analytics_page_views_created_at_index` (`created_at`);

--
-- Indexes for table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `answers_question_id_index` (`question_id`),
  ADD KEY `answers_is_accepted_index` (`is_accepted`),
  ADD KEY `answers_votes_score_index` (`votes_score`),
  ADD KEY `answers_user_id_question_id_index` (`user_id`,`question_id`);

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `articles_slug_unique` (`slug`),
  ADD KEY `articles_reviewed_by_foreign` (`reviewed_by`),
  ADD KEY `articles_status_index` (`status`),
  ADD KEY `articles_level_index` (`level`),
  ADD KEY `articles_published_at_index` (`published_at`),
  ADD KEY `articles_user_id_status_index` (`user_id`,`status`);

--
-- Indexes for table `article_tag`
--
ALTER TABLE `article_tag`
  ADD PRIMARY KEY (`article_id`,`tag_id`),
  ADD KEY `article_tag_tag_id_foreign` (`tag_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comments_commentable_type_commentable_id_index` (`commentable_type`,`commentable_id`),
  ADD KEY `comments_user_id_index` (`user_id`),
  ADD KEY `comments_parent_id_index` (`parent_id`),
  ADD KEY `comments_is_hidden_index` (`is_hidden`);

--
-- Indexes for table `community_values`
--
ALTER TABLE `community_values`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `companies_slug_unique` (`slug`),
  ADD KEY `companies_submitted_by_foreign` (`submitted_by`),
  ADD KEY `companies_is_verified_index` (`is_verified`);

--
-- Indexes for table `company_accounts`
--
ALTER TABLE `company_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `company_accounts_email_unique` (`email`),
  ADD KEY `company_accounts_company_id_foreign` (`company_id`),
  ADD KEY `company_accounts_status_index` (`status`),
  ADD KEY `company_accounts_email_index` (`email`);

--
-- Indexes for table `company_registration_requests`
--
ALTER TABLE `company_registration_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `company_registration_requests_email_unique` (`email`),
  ADD KEY `company_registration_requests_reviewed_by_foreign` (`reviewed_by`),
  ADD KEY `company_registration_requests_status_index` (`status`),
  ADD KEY `company_registration_requests_email_index` (`email`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `events_slug_unique` (`slug`),
  ADD KEY `events_created_by_foreign` (`created_by`),
  ADD KEY `events_status_index` (`status`),
  ADD KEY `events_type_index` (`type`),
  ADD KEY `events_starts_at_index` (`starts_at`),
  ADD KEY `events_status_starts_at_index` (`status`,`starts_at`);

--
-- Indexes for table `event_registrations`
--
ALTER TABLE `event_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `event_registrations_event_id_user_id_unique` (`event_id`,`user_id`),
  ADD UNIQUE KEY `event_registrations_ical_token_unique` (`ical_token`),
  ADD UNIQUE KEY `event_registrations_ticket_number_unique` (`ticket_number`),
  ADD UNIQUE KEY `event_registrations_ticket_qr_token_unique` (`ticket_qr_token`),
  ADD KEY `event_registrations_user_id_foreign` (`user_id`),
  ADD KEY `event_registrations_status_index` (`status`),
  ADD KEY `event_registrations_event_id_status_index` (`event_id`,`status`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indexes for table `guest_registrations`
--
ALTER TABLE `guest_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `guest_registrations_event_id_email_unique` (`event_id`,`email`),
  ADD UNIQUE KEY `guest_registrations_ticket_number_unique` (`ticket_number`),
  ADD UNIQUE KEY `guest_registrations_ticket_qr_token_unique` (`ticket_qr_token`),
  ADD KEY `guest_registrations_event_id_index` (`event_id`),
  ADD KEY `guest_registrations_status_index` (`status`),
  ADD KEY `guest_registrations_email_index` (`email`);

--
-- Indexes for table `home_stats`
--
ALTER TABLE `home_stats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `job_applications_job_offer_id_user_id_unique` (`job_offer_id`,`user_id`),
  ADD KEY `job_applications_status_index` (`status`),
  ADD KEY `job_applications_user_id_status_index` (`user_id`,`status`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `job_favorites`
--
ALTER TABLE `job_favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `job_favorites_job_offer_id_user_id_unique` (`job_offer_id`,`user_id`),
  ADD KEY `job_favorites_user_id_index` (`user_id`);

--
-- Indexes for table `job_offers`
--
ALTER TABLE `job_offers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `job_offers_slug_unique` (`slug`),
  ADD KEY `job_offers_company_id_foreign` (`company_id`),
  ADD KEY `job_offers_posted_by_foreign` (`posted_by`),
  ADD KEY `job_offers_status_index` (`status`),
  ADD KEY `job_offers_contract_type_index` (`contract_type`),
  ADD KEY `job_offers_level_index` (`level`),
  ADD KEY `job_offers_is_remote_index` (`is_remote`),
  ADD KEY `job_offers_is_urgent_index` (`is_urgent`),
  ADD KEY `job_offers_expires_at_index` (`expires_at`),
  ADD KEY `job_offers_status_expires_at_index` (`status`,`expires_at`);

--
-- Indexes for table `job_offer_categories`
--
ALTER TABLE `job_offer_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `job_offer_categories_slug_unique` (`slug`);

--
-- Indexes for table `job_offer_category`
--
ALTER TABLE `job_offer_category`
  ADD PRIMARY KEY (`job_offer_id`,`job_offer_category_id`),
  ADD KEY `job_offer_category_job_offer_category_id_foreign` (`job_offer_category_id`);

--
-- Indexes for table `job_offer_skill`
--
ALTER TABLE `job_offer_skill`
  ADD PRIMARY KEY (`job_offer_id`,`job_skill_id`),
  ADD KEY `job_offer_skill_job_skill_id_foreign` (`job_skill_id`);

--
-- Indexes for table `job_skills`
--
ALTER TABLE `job_skills`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `job_skills_slug_unique` (`slug`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `partners`
--
ALTER TABLE `partners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `partners_type_index` (`type`),
  ADD KEY `partners_is_active_index` (`is_active`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profiles_user_id_foreign` (`user_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `questions_slug_unique` (`slug`),
  ADD KEY `questions_status_index` (`status`),
  ADD KEY `questions_is_pinned_index` (`is_pinned`),
  ADD KEY `questions_votes_score_index` (`votes_score`),
  ADD KEY `questions_last_activity_at_index` (`last_activity_at`),
  ADD KEY `questions_user_id_status_index` (`user_id`,`status`),
  ADD KEY `questions_accepted_answer_id_foreign` (`accepted_answer_id`);

--
-- Indexes for table `question_tag`
--
ALTER TABLE `question_tag`
  ADD PRIMARY KEY (`question_id`,`tag_id`),
  ADD KEY `question_tag_tag_id_foreign` (`tag_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reports_reportable_type_reportable_id_index` (`reportable_type`,`reportable_id`),
  ADD KEY `reports_handled_by_foreign` (`handled_by`),
  ADD KEY `reports_status_index` (`status`),
  ADD KEY `reports_reason_index` (`reason`),
  ADD KEY `reports_reporter_id_status_index` (`reporter_id`,`status`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resources_user_id_foreign` (`user_id`),
  ADD KEY `resources_type_index` (`type`),
  ADD KEY `resources_is_public_index` (`is_public`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `site_settings_key_unique` (`key`),
  ADD KEY `site_settings_group_index` (`group`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tags_slug_unique` (`slug`),
  ADD KEY `tags_scope_index` (`scope`),
  ADD KEY `tags_slug_index` (`slug`);

--
-- Indexes for table `team_members`
--
ALTER TABLE `team_members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `timeline_events`
--
ALTER TABLE `timeline_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_github_id_unique` (`github_id`),
  ADD UNIQUE KEY `users_github_username_unique` (`github_username`),
  ADD KEY `users_is_active_index` (`is_active`),
  ADD KEY `users_suspended_until_index` (`suspended_until`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `votes_unique` (`votable_id`,`votable_type`,`user_id`),
  ADD KEY `votes_votable_type_votable_id_index` (`votable_type`,`votable_id`),
  ADD KEY `votes_user_id_index` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_origin_sections`
--
ALTER TABLE `about_origin_sections`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_activity_logs`
--
ALTER TABLE `admin_activity_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `analytics_events`
--
ALTER TABLE `analytics_events`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `analytics_page_views`
--
ALTER TABLE `analytics_page_views`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `community_values`
--
ALTER TABLE `community_values`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_accounts`
--
ALTER TABLE `company_accounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_registration_requests`
--
ALTER TABLE `company_registration_requests`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `event_registrations`
--
ALTER TABLE `event_registrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guest_registrations`
--
ALTER TABLE `guest_registrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `home_stats`
--
ALTER TABLE `home_stats`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_applications`
--
ALTER TABLE `job_applications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_favorites`
--
ALTER TABLE `job_favorites`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_offers`
--
ALTER TABLE `job_offers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_offer_categories`
--
ALTER TABLE `job_offer_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `job_skills`
--
ALTER TABLE `job_skills`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `partners`
--
ALTER TABLE `partners`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `profiles`
--
ALTER TABLE `profiles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `team_members`
--
ALTER TABLE `team_members`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `timeline_events`
--
ALTER TABLE `timeline_events`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_activity_logs`
--
ALTER TABLE `admin_activity_logs`
  ADD CONSTRAINT `admin_activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `analytics_events`
--
ALTER TABLE `analytics_events`
  ADD CONSTRAINT `analytics_events_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `analytics_page_views`
--
ALTER TABLE `analytics_page_views`
  ADD CONSTRAINT `analytics_page_views_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `answers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `articles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `article_tag`
--
ALTER TABLE `article_tag`
  ADD CONSTRAINT `article_tag_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `article_tag_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `companies_submitted_by_foreign` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `company_accounts`
--
ALTER TABLE `company_accounts`
  ADD CONSTRAINT `company_accounts_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `company_registration_requests`
--
ALTER TABLE `company_registration_requests`
  ADD CONSTRAINT `company_registration_requests_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `event_registrations`
--
ALTER TABLE `event_registrations`
  ADD CONSTRAINT `event_registrations_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_registrations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `guest_registrations`
--
ALTER TABLE `guest_registrations`
  ADD CONSTRAINT `guest_registrations_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD CONSTRAINT `job_applications_job_offer_id_foreign` FOREIGN KEY (`job_offer_id`) REFERENCES `job_offers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_applications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_favorites`
--
ALTER TABLE `job_favorites`
  ADD CONSTRAINT `job_favorites_job_offer_id_foreign` FOREIGN KEY (`job_offer_id`) REFERENCES `job_offers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_favorites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_offers`
--
ALTER TABLE `job_offers`
  ADD CONSTRAINT `job_offers_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_offers_posted_by_foreign` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_offer_category`
--
ALTER TABLE `job_offer_category`
  ADD CONSTRAINT `job_offer_category_job_offer_category_id_foreign` FOREIGN KEY (`job_offer_category_id`) REFERENCES `job_offer_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_offer_category_job_offer_id_foreign` FOREIGN KEY (`job_offer_id`) REFERENCES `job_offers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_offer_skill`
--
ALTER TABLE `job_offer_skill`
  ADD CONSTRAINT `job_offer_skill_job_offer_id_foreign` FOREIGN KEY (`job_offer_id`) REFERENCES `job_offers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_offer_skill_job_skill_id_foreign` FOREIGN KEY (`job_skill_id`) REFERENCES `job_skills` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `profiles`
--
ALTER TABLE `profiles`
  ADD CONSTRAINT `profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_accepted_answer_id_foreign` FOREIGN KEY (`accepted_answer_id`) REFERENCES `answers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `questions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `question_tag`
--
ALTER TABLE `question_tag`
  ADD CONSTRAINT `question_tag_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `question_tag_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_handled_by_foreign` FOREIGN KEY (`handled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reports_reporter_id_foreign` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `resources`
--
ALTER TABLE `resources`
  ADD CONSTRAINT `resources_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
