-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: sdb-84.hosting.stackcp.net
-- Generation Time: Jul 03, 2026 at 08:53 PM
-- Server version: 10.11.18-MariaDB-log
-- PHP Version: 8.3.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `BeyondImagination-3530393677b6`
--
CREATE DATABASE IF NOT EXISTS `BeyondImagination-3530393677b6` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `BeyondImagination-3530393677b6`;

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(80) NOT NULL,
  `details` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `activity_logs`
--

-- Seed/data rows removed for GitHub safety.
-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

-- Seed/data rows removed for GitHub safety.
-- --------------------------------------------------------

--
-- Table structure for table `analytics_events`
--

CREATE TABLE `analytics_events` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `event_type` varchar(80) DEFAULT NULL,
  `page` varchar(120) DEFAULT NULL,
  `ip_hash` varchar(128) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `api_keys`
--

CREATE TABLE `api_keys` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `api_key` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `last_used_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `api_usage`
--

CREATE TABLE `api_usage` (
  `id` int(11) NOT NULL,
  `endpoint` varchar(190) NOT NULL,
  `method` varchar(20) NOT NULL,
  `status_code` int(11) DEFAULT 200,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `apps`
--

CREATE TABLE `apps` (
  `id` int(11) NOT NULL,
  `slug` varchar(80) NOT NULL,
  `name` varchar(120) NOT NULL,
  `status` varchar(40) NOT NULL DEFAULT 'coming_soon',
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `apps`
--

-- Seed/data rows removed for GitHub safety.
-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(120) NOT NULL,
  `ip_address` varchar(64) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bc_menu_categories`
--

CREATE TABLE `bc_menu_categories` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bc_menu_categories`
--

-- Seed/data rows removed for GitHub safety.
-- --------------------------------------------------------

--
-- Table structure for table `bc_menu_items`
--

CREATE TABLE `bc_menu_items` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(160) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_available` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bc_menu_items`
--

-- Seed/data rows removed for GitHub safety.
-- --------------------------------------------------------

--
-- Table structure for table `bc_vendors`
--

CREATE TABLE `bc_vendors` (
  `id` int(11) NOT NULL,
  `business_name` varchar(160) NOT NULL,
  `slug` varchar(180) NOT NULL,
  `business_type` varchar(80) DEFAULT 'Catering Company',
  `cuisine` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `email` varchar(160) DEFAULT NULL,
  `phone` varchar(80) DEFAULT NULL,
  `instagram` varchar(160) DEFAULT NULL,
  `city` varchar(120) DEFAULT NULL,
  `state_region` varchar(120) DEFAULT NULL,
  `country` varchar(80) DEFAULT 'USA',
  `service_area` text DEFAULT NULL,
  `status` enum('draft','active','paused') DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bc_vendors`
--

-- Seed/data rows removed for GitHub safety.
-- --------------------------------------------------------

--
-- Table structure for table `bc_vendor_profiles`
--

CREATE TABLE `bc_vendor_profiles` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `tagline` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `cover_image_path` varchar(255) DEFAULT NULL,
  `pickup_available` tinyint(1) DEFAULT 0,
  `delivery_available` tinyint(1) DEFAULT 0,
  `catering_available` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bc_vendor_profiles`
--

-- Seed/data rows removed for GitHub safety.
-- --------------------------------------------------------

--
-- Table structure for table `billing`
--

CREATE TABLE `billing` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `gross_amount` decimal(10,2) DEFAULT NULL,
  `platform_fee` decimal(10,2) DEFAULT NULL,
  `processing_fee` decimal(10,2) DEFAULT NULL,
  `vendor_payout` decimal(10,2) DEFAULT NULL,
  `currency` varchar(10) DEFAULT 'CAD',
  `status` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `catering_requests`
--

CREATE TABLE `catering_requests` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `guest_count` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('new','quoted','accepted','declined','completed') DEFAULT 'new',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `name` varchar(160) NOT NULL,
  `type` varchar(80) DEFAULT 'business',
  `status` varchar(30) DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

-- Seed/data rows removed for GitHub safety.
-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(190) NOT NULL,
  `subject` varchar(190) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL,
  `read_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_profiles`
--

CREATE TABLE `customer_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `phone` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dailybreath_posts`
--

CREATE TABLE `dailybreath_posts` (
  `id` int(11) NOT NULL,
  `title` varchar(190) NOT NULL,
  `body` text DEFAULT NULL,
  `status` varchar(30) DEFAULT 'draft',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dailybreath_posts`
--

-- Seed/data rows removed for GitHub safety.
-- --------------------------------------------------------

--
-- Table structure for table `deployment_logs`
--

CREATE TABLE `deployment_logs` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `target_domain` varchar(255) DEFAULT NULL,
  `version_number` int(11) DEFAULT NULL,
  `status` enum('success','failed','pending') DEFAULT 'pending',
  `message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `early_access_subscribers`
--

CREATE TABLE `early_access_subscribers` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(190) NOT NULL,
  `product` varchar(80) NOT NULL DEFAULT 'Beyond Health',
  `source` varchar(120) DEFAULT 'landing_page',
  `status` enum('active','unsubscribed','bounced') NOT NULL DEFAULT 'active',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `early_access_subscribers`
--

-- Seed/data rows removed for GitHub safety.
CPU iPhone OS 26_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/23F77 Instagram 435.1.0.20.71 (iPhone14,7; iOS 26_5; en_US; en; scale=3.00; 1170x2532; IABMV/1; 1002470399) NW/3 Safari/604.1', '2026-07-01 21:58:41');

-- --------------------------------------------------------

--
-- Table structure for table `email_verifications`
--

CREATE TABLE `email_verifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(128) NOT NULL,
  `app` varchar(50) NOT NULL DEFAULT 'beyond_id',
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu_categories`
--

CREATE TABLE `menu_categories` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `item_name` varchar(190) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `image_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_subscribers`
--

CREATE TABLE `newsletter_subscribers` (
  `id` int(11) NOT NULL,
  `email` varchar(190) NOT NULL,
  `token` varchar(128) NOT NULL,
  `status` enum('active','unsubscribed') NOT NULL DEFAULT 'active',
  `subscribed_at` datetime NOT NULL,
  `unsubscribed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_clients`
--

CREATE TABLE `oauth_clients` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `client_id` varchar(120) NOT NULL,
  `client_secret_hash` varchar(255) NOT NULL,
  `redirect_uri` text DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `stripe_payment_intent` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT 0.00,
  `currency` varchar(10) DEFAULT 'CAD',
  `status` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

-- Seed/data rows removed for GitHub safety.
-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE `profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `query_history`
--

CREATE TABLE `query_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `query_text` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `refunds`
--

CREATE TABLE `refunds` (
  `id` int(11) NOT NULL,
  `stripe_charge` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(80) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

-- Seed/data rows removed for GitHub safety.
-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(120) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

-- Seed/data rows removed for GitHub safety.
-- --------------------------------------------------------

--
-- Table structure for table `stripe_webhooks`
--

CREATE TABLE `stripe_webhooks` (
  `id` int(11) NOT NULL,
  `stripe_event_id` varchar(255) DEFAULT NULL,
  `event_type` varchar(255) DEFAULT NULL,
  `payload` longtext DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `plan` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `trial_ends_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(190) NOT NULL,
  `phone` varchar(40) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verification_token` varchar(255) DEFAULT NULL,
  `verification_sent_at` datetime DEFAULT NULL,
  `email_verified_at` datetime DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'admin',
  `status` varchar(30) NOT NULL DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

-- User seed data removed for GitHub safety.

-- --------------------------------------------------------

--
-- Table structure for table `user_apps`
--

CREATE TABLE `user_apps` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `status` varchar(40) NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` int(11) NOT NULL,
  `business_name` varchar(160) NOT NULL,
  `owner_name` varchar(160) NOT NULL,
  `email` varchar(190) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `plan` varchar(60) DEFAULT 'premium_trial',
  `trial_ends_at` datetime DEFAULT NULL,
  `slug` varchar(190) NOT NULL,
  `onboarding_step` int(11) DEFAULT 1,
  `website_status` varchar(40) DEFAULT 'draft',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `status` varchar(40) DEFAULT 'trial',
  `trial_started_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendors`
--

-- Seed/data rows removed for GitHub safety.
-- --------------------------------------------------------

--
-- Table structure for table `vendor_domains`
--

CREATE TABLE `vendor_domains` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `domain_type` enum('subdomain','existing','included_lifetime') DEFAULT 'subdomain',
  `domain` varchar(255) DEFAULT NULL,
  `subdomain` varchar(255) DEFAULT NULL,
  `dns_status` enum('pending','connected','failed') DEFAULT 'pending',
  `ssl_status` enum('pending','active','failed') DEFAULT 'pending',
  `publish_status` enum('draft','published') DEFAULT 'draft',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_media`
--

CREATE TABLE `vendor_media` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `file_type` enum('logo','hero','menu','gallery','document') DEFAULT 'gallery',
  `original_name` varchar(255) NOT NULL,
  `stored_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `mime_type` varchar(120) DEFAULT NULL,
  `file_size` int(11) DEFAULT 0,
  `usage_role` varchar(60) DEFAULT 'gallery',
  `alt_text` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_profiles`
--

CREATE TABLE `vendor_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `business_name` varchar(150) NOT NULL,
  `food_type` varchar(100) DEFAULT NULL,
  `service_area` varchar(150) DEFAULT NULL,
  `business_model` enum('food_truck','commercial_kitchen','catering','other') DEFAULT 'other',
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_sites`
--

CREATE TABLE `vendor_sites` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `theme` varchar(100) DEFAULT 'modern-food',
  `site_title` varchar(255) DEFAULT NULL,
  `tagline` varchar(255) DEFAULT NULL,
  `hero_image` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `about_text` text DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` enum('draft','published') DEFAULT 'draft',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `website_settings`
--

CREATE TABLE `website_settings` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `theme` varchar(50) DEFAULT 'modern_dark',
  `hero_title` varchar(255) DEFAULT NULL,
  `hero_subtitle` text DEFAULT NULL,
  `about_text` text DEFAULT NULL,
  `catering_text` text DEFAULT NULL,
  `hero_image` varchar(255) DEFAULT NULL,
  `primary_color` varchar(20) DEFAULT '#ff7a00',
  `published` tinyint(1) DEFAULT 0,
  `health_score` int(11) DEFAULT 70,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `website_versions`
--

CREATE TABLE `website_versions` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `version_number` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `snapshot_json` longtext DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `analytics_events`
--
ALTER TABLE `analytics_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `api_keys`
--
ALTER TABLE `api_keys`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `api_usage`
--
ALTER TABLE `api_usage`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `apps`
--
ALTER TABLE `apps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `bc_menu_categories`
--
ALTER TABLE `bc_menu_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `bc_menu_items`
--
ALTER TABLE `bc_menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `bc_vendors`
--
ALTER TABLE `bc_vendors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `bc_vendor_profiles`
--
ALTER TABLE `bc_vendor_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `billing`
--
ALTER TABLE `billing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `catering_requests`
--
ALTER TABLE `catering_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_profiles`
--
ALTER TABLE `customer_profiles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dailybreath_posts`
--
ALTER TABLE `dailybreath_posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deployment_logs`
--
ALTER TABLE `deployment_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `early_access_subscribers`
--
ALTER TABLE `early_access_subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `email_verifications`
--
ALTER TABLE `email_verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `token` (`token`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `menu_categories`
--
ALTER TABLE `menu_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `client_id` (`client_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `query_history`
--
ALTER TABLE `query_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `refunds`
--
ALTER TABLE `refunds`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `stripe_webhooks`
--
ALTER TABLE `stripe_webhooks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stripe_event_id` (`stripe_event_id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_apps`
--
ALTER TABLE `user_apps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_app` (`user_id`,`app_id`),
  ADD KEY `app_id` (`app_id`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `vendor_domains`
--
ALTER TABLE `vendor_domains`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `vendor_media`
--
ALTER TABLE `vendor_media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `file_type` (`file_type`);

--
-- Indexes for table `vendor_profiles`
--
ALTER TABLE `vendor_profiles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vendor_sites`
--
ALTER TABLE `vendor_sites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `website_settings`
--
ALTER TABLE `website_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `website_versions`
--
ALTER TABLE `website_versions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `analytics_events`
--
ALTER TABLE `analytics_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `api_keys`
--
ALTER TABLE `api_keys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `api_usage`
--
ALTER TABLE `api_usage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `apps`
--
ALTER TABLE `apps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bc_menu_categories`
--
ALTER TABLE `bc_menu_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bc_menu_items`
--
ALTER TABLE `bc_menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `bc_vendors`
--
ALTER TABLE `bc_vendors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bc_vendor_profiles`
--
ALTER TABLE `bc_vendor_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `billing`
--
ALTER TABLE `billing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `catering_requests`
--
ALTER TABLE `catering_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_profiles`
--
ALTER TABLE `customer_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dailybreath_posts`
--
ALTER TABLE `dailybreath_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `deployment_logs`
--
ALTER TABLE `deployment_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `early_access_subscribers`
--
ALTER TABLE `early_access_subscribers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `email_verifications`
--
ALTER TABLE `email_verifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_categories`
--
ALTER TABLE `menu_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `profiles`
--
ALTER TABLE `profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `query_history`
--
ALTER TABLE `query_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `refunds`
--
ALTER TABLE `refunds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `stripe_webhooks`
--
ALTER TABLE `stripe_webhooks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_apps`
--
ALTER TABLE `user_apps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `vendor_domains`
--
ALTER TABLE `vendor_domains`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendor_media`
--
ALTER TABLE `vendor_media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendor_profiles`
--
ALTER TABLE `vendor_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendor_sites`
--
ALTER TABLE `vendor_sites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `website_settings`
--
ALTER TABLE `website_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `website_versions`
--
ALTER TABLE `website_versions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `api_keys`
--
ALTER TABLE `api_keys`
  ADD CONSTRAINT `api_keys_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bc_menu_categories`
--
ALTER TABLE `bc_menu_categories`
  ADD CONSTRAINT `bc_menu_categories_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `bc_vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bc_menu_items`
--
ALTER TABLE `bc_menu_items`
  ADD CONSTRAINT `bc_menu_items_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `bc_vendors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bc_menu_items_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `bc_menu_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `bc_vendor_profiles`
--
ALTER TABLE `bc_vendor_profiles`
  ADD CONSTRAINT `bc_vendor_profiles_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `bc_vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  ADD CONSTRAINT `oauth_clients_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `profiles`
--
ALTER TABLE `profiles`
  ADD CONSTRAINT `profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_apps`
--
ALTER TABLE `user_apps`
  ADD CONSTRAINT `user_apps_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_apps_ibfk_2` FOREIGN KEY (`app_id`) REFERENCES `apps` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
