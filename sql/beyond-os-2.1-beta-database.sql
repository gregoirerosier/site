-- Beyond OS 2.1 Beta
-- Additive marketplace, wallet, auction, Stripe, shipping, and subscription schema
-- MySQL 8+ / MariaDB compatible
-- Review table prefixes and existing users table before production deployment.

SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE TABLE IF NOT EXISTS wallets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    bits_balance BIGINT NOT NULL DEFAULT 0,
    cash_pending DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    cash_available DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    currency CHAR(3) NOT NULL DEFAULT 'USD',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_wallet_user (user_id),
    KEY idx_wallet_currency (currency)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS wallet_transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    wallet_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    transaction_type ENUM(
        'bits_credit','bits_debit','cash_pending_credit','cash_pending_debit',
        'cash_available_credit','cash_available_debit','refund','adjustment'
    ) NOT NULL,
    bits_amount BIGINT NOT NULL DEFAULT 0,
    cash_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    currency CHAR(3) NOT NULL DEFAULT 'USD',
    reference_type VARCHAR(50) DEFAULT NULL,
    reference_id BIGINT UNSIGNED DEFAULT NULL,
    description VARCHAR(255) DEFAULT NULL,
    status ENUM('pending','completed','failed','reversed') NOT NULL DEFAULT 'completed',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_wallet_tx_wallet (wallet_id),
    KEY idx_wallet_tx_user (user_id),
    KEY idx_wallet_tx_reference (reference_type, reference_id),
    KEY idx_wallet_tx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS seller_profiles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    store_name VARCHAR(150) NOT NULL,
    store_slug VARCHAR(170) NOT NULL,
    bio TEXT DEFAULT NULL,
    logo_path VARCHAR(255) DEFAULT NULL,
    banner_path VARCHAR(255) DEFAULT NULL,
    status ENUM('draft','pending','active','suspended') NOT NULL DEFAULT 'draft',
    stripe_account_id VARCHAR(100) DEFAULT NULL,
    stripe_onboarding_complete TINYINT(1) NOT NULL DEFAULT 0,
    payout_enabled TINYINT(1) NOT NULL DEFAULT 0,
    rating_average DECIMAL(3,2) NOT NULL DEFAULT 0.00,
    rating_count INT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_seller_user (user_id),
    UNIQUE KEY uq_seller_slug (store_slug),
    KEY idx_seller_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS product_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    parent_id BIGINT UNSIGNED DEFAULT NULL,
    name VARCHAR(120) NOT NULL,
    slug VARCHAR(140) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_category_slug (slug),
    KEY idx_category_parent (parent_id),
    KEY idx_category_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS listings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    seller_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED DEFAULT NULL,
    title VARCHAR(180) NOT NULL,
    slug VARCHAR(200) NOT NULL,
    short_description VARCHAR(300) DEFAULT NULL,
    description LONGTEXT DEFAULT NULL,
    listing_type ENUM('buy_now','auction','buy_now_auction','digital','service') NOT NULL DEFAULT 'buy_now',
    item_type ENUM('physical','digital','service') NOT NULL DEFAULT 'physical',
    condition_type ENUM('new','used_like_new','used_good','used_fair','not_applicable') NOT NULL DEFAULT 'not_applicable',
    price_cash DECIMAL(12,2) DEFAULT NULL,
    price_bits BIGINT DEFAULT NULL,
    currency CHAR(3) NOT NULL DEFAULT 'USD',
    quantity INT UNSIGNED NOT NULL DEFAULT 1,
    allow_best_offer TINYINT(1) NOT NULL DEFAULT 0,
    status ENUM('draft','pending','active','sold','ended','suspended','deleted') NOT NULL DEFAULT 'draft',
    published_at DATETIME DEFAULT NULL,
    ends_at DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_listing_slug (slug),
    KEY idx_listing_seller (seller_id),
    KEY idx_listing_category (category_id),
    KEY idx_listing_status (status),
    KEY idx_listing_type (listing_type),
    KEY idx_listing_item_type (item_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS listing_images (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    listing_id BIGINT UNSIGNED NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    alt_text VARCHAR(180) DEFAULT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_primary TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_listing_images_listing (listing_id),
    KEY idx_listing_images_primary (listing_id, is_primary)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS digital_assets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    listing_id BIGINT UNSIGNED NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    mime_type VARCHAR(120) DEFAULT NULL,
    file_size_bytes BIGINT UNSIGNED DEFAULT NULL,
    download_limit INT UNSIGNED NOT NULL DEFAULT 5,
    download_expiry_days INT UNSIGNED NOT NULL DEFAULT 30,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_digital_asset_listing (listing_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS auctions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    listing_id BIGINT UNSIGNED NOT NULL,
    starting_bid DECIMAL(12,2) NOT NULL,
    reserve_price DECIMAL(12,2) DEFAULT NULL,
    buy_now_price DECIMAL(12,2) DEFAULT NULL,
    bid_increment DECIMAL(12,2) NOT NULL DEFAULT 1.00,
    current_bid DECIMAL(12,2) DEFAULT NULL,
    current_bidder_id BIGINT UNSIGNED DEFAULT NULL,
    bid_count INT UNSIGNED NOT NULL DEFAULT 0,
    starts_at DATETIME NOT NULL,
    ends_at DATETIME NOT NULL,
    status ENUM('scheduled','live','ended','cancelled') NOT NULL DEFAULT 'scheduled',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_auction_listing (listing_id),
    KEY idx_auction_status_time (status, ends_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS auction_bids (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    auction_id BIGINT UNSIGNED NOT NULL,
    bidder_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    currency CHAR(3) NOT NULL DEFAULT 'USD',
    is_winning TINYINT(1) NOT NULL DEFAULT 0,
    status ENUM('active','outbid','won','cancelled','retracted') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_bid_auction (auction_id),
    KEY idx_bid_bidder (bidder_id),
    KEY idx_bid_winning (auction_id, is_winning),
    KEY idx_bid_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS carts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    status ENUM('active','converted','abandoned') NOT NULL DEFAULT 'active',
    currency CHAR(3) NOT NULL DEFAULT 'USD',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_cart_user_status (user_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cart_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cart_id BIGINT UNSIGNED NOT NULL,
    listing_id BIGINT UNSIGNED NOT NULL,
    seller_id BIGINT UNSIGNED NOT NULL,
    quantity INT UNSIGNED NOT NULL DEFAULT 1,
    unit_price_cash DECIMAL(12,2) DEFAULT NULL,
    unit_price_bits BIGINT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_cart_listing (cart_id, listing_id),
    KEY idx_cart_item_seller (seller_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS addresses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    address_type ENUM('shipping','billing','both') NOT NULL DEFAULT 'shipping',
    full_name VARCHAR(150) NOT NULL,
    company VARCHAR(150) DEFAULT NULL,
    line1 VARCHAR(180) NOT NULL,
    line2 VARCHAR(180) DEFAULT NULL,
    city VARCHAR(120) NOT NULL,
    region VARCHAR(120) DEFAULT NULL,
    postal_code VARCHAR(30) NOT NULL,
    country_code CHAR(2) NOT NULL,
    phone VARCHAR(40) DEFAULT NULL,
    is_default TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_address_user (user_id),
    KEY idx_address_default (user_id, address_type, is_default)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS shipping_profiles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    seller_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(120) NOT NULL,
    ships_from_country CHAR(2) NOT NULL,
    handling_days INT UNSIGNED NOT NULL DEFAULT 2,
    is_default TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_shipping_profile_seller (seller_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS shipping_rates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    shipping_profile_id BIGINT UNSIGNED NOT NULL,
    destination_scope ENUM('local_pickup','domestic','us','canada','international','custom') NOT NULL,
    destination_country CHAR(2) DEFAULT NULL,
    method_name VARCHAR(120) NOT NULL,
    rate_type ENUM('free','flat','calculated','pickup') NOT NULL DEFAULT 'flat',
    amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    currency CHAR(3) NOT NULL DEFAULT 'USD',
    estimated_min_days INT UNSIGNED DEFAULT NULL,
    estimated_max_days INT UNSIGNED DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_shipping_rate_profile (shipping_profile_id),
    KEY idx_shipping_rate_destination (destination_scope, destination_country)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS listing_shipping (
    listing_id BIGINT UNSIGNED PRIMARY KEY,
    shipping_profile_id BIGINT UNSIGNED NOT NULL,
    weight_grams INT UNSIGNED DEFAULT NULL,
    length_cm DECIMAL(8,2) DEFAULT NULL,
    width_cm DECIMAL(8,2) DEFAULT NULL,
    height_cm DECIMAL(8,2) DEFAULT NULL,
    local_pickup_available TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_listing_shipping_profile (shipping_profile_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(40) NOT NULL,
    buyer_id BIGINT UNSIGNED NOT NULL,
    payment_method ENUM('stripe','bits','mixed') NOT NULL,
    currency CHAR(3) NOT NULL DEFAULT 'USD',
    subtotal_cash DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    subtotal_bits BIGINT NOT NULL DEFAULT 0,
    shipping_cash DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    tax_cash DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    platform_fee_cash DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    platform_fee_bits BIGINT NOT NULL DEFAULT 0,
    total_cash DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total_bits BIGINT NOT NULL DEFAULT 0,
    status ENUM(
        'pending_payment','paid','processing','ready_to_ship','shipped',
        'delivered','completed','cancelled','refunded','partially_refunded'
    ) NOT NULL DEFAULT 'pending_payment',
    shipping_address_id BIGINT UNSIGNED DEFAULT NULL,
    billing_address_id BIGINT UNSIGNED DEFAULT NULL,
    stripe_checkout_session_id VARCHAR(255) DEFAULT NULL,
    stripe_payment_intent_id VARCHAR(255) DEFAULT NULL,
    placed_at DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_order_number (order_number),
    KEY idx_order_buyer (buyer_id),
    KEY idx_order_status (status),
    KEY idx_order_stripe_session (stripe_checkout_session_id),
    KEY idx_order_payment_intent (stripe_payment_intent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    listing_id BIGINT UNSIGNED NOT NULL,
    seller_id BIGINT UNSIGNED NOT NULL,
    title_snapshot VARCHAR(180) NOT NULL,
    item_type ENUM('physical','digital','service') NOT NULL,
    quantity INT UNSIGNED NOT NULL DEFAULT 1,
    unit_price_cash DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    unit_price_bits BIGINT NOT NULL DEFAULT 0,
    line_total_cash DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    line_total_bits BIGINT NOT NULL DEFAULT 0,
    fulfillment_status ENUM(
        'pending','processing','ready_to_ship','shipped','delivered',
        'download_ready','completed','cancelled','refunded'
    ) NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_order_item_order (order_id),
    KEY idx_order_item_seller (seller_id),
    KEY idx_order_item_listing (listing_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS shipments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    seller_id BIGINT UNSIGNED NOT NULL,
    carrier VARCHAR(80) DEFAULT NULL,
    service_name VARCHAR(100) DEFAULT NULL,
    tracking_number VARCHAR(120) DEFAULT NULL,
    tracking_url VARCHAR(255) DEFAULT NULL,
    shipping_cost DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    currency CHAR(3) NOT NULL DEFAULT 'USD',
    status ENUM('pending','packed','shipped','in_transit','out_for_delivery','delivered','returned','lost') NOT NULL DEFAULT 'pending',
    shipped_at DATETIME DEFAULT NULL,
    delivered_at DATETIME DEFAULT NULL,
    estimated_delivery_at DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_shipment_order (order_id),
    KEY idx_shipment_tracking (tracking_number),
    KEY idx_shipment_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    provider ENUM('stripe','bits') NOT NULL,
    provider_reference VARCHAR(255) DEFAULT NULL,
    amount_cash DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    amount_bits BIGINT NOT NULL DEFAULT 0,
    currency CHAR(3) NOT NULL DEFAULT 'USD',
    status ENUM('created','pending','succeeded','failed','cancelled','refunded','partially_refunded') NOT NULL DEFAULT 'created',
    paid_at DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_payment_order (order_id),
    KEY idx_payment_user (user_id),
    KEY idx_payment_provider_reference (provider, provider_reference),
    KEY idx_payment_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS refunds (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payment_id BIGINT UNSIGNED NOT NULL,
    order_id BIGINT UNSIGNED NOT NULL,
    amount_cash DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    amount_bits BIGINT NOT NULL DEFAULT 0,
    currency CHAR(3) NOT NULL DEFAULT 'USD',
    reason VARCHAR(255) DEFAULT NULL,
    provider_refund_id VARCHAR(255) DEFAULT NULL,
    status ENUM('pending','succeeded','failed','cancelled') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_refund_payment (payment_id),
    KEY idx_refund_order (order_id),
    KEY idx_refund_provider (provider_refund_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS stripe_events (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    stripe_event_id VARCHAR(255) NOT NULL,
    event_type VARCHAR(120) NOT NULL,
    payload_json LONGTEXT NOT NULL,
    processed TINYINT(1) NOT NULL DEFAULT 0,
    processing_error TEXT DEFAULT NULL,
    received_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    processed_at DATETIME DEFAULT NULL,
    UNIQUE KEY uq_stripe_event (stripe_event_id),
    KEY idx_stripe_event_processed (processed),
    KEY idx_stripe_event_type (event_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS subscriptions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    product_code VARCHAR(80) NOT NULL,
    plan_code VARCHAR(80) NOT NULL,
    payment_method ENUM('stripe','bits') NOT NULL,
    price_cash DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    price_bits BIGINT NOT NULL DEFAULT 0,
    currency CHAR(3) NOT NULL DEFAULT 'USD',
    stripe_subscription_id VARCHAR(255) DEFAULT NULL,
    status ENUM('trialing','active','past_due','cancelled','expired') NOT NULL DEFAULT 'active',
    starts_at DATETIME NOT NULL,
    ends_at DATETIME DEFAULT NULL,
    cancelled_at DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_subscription_user (user_id),
    KEY idx_subscription_product (product_code, status),
    KEY idx_subscription_stripe (stripe_subscription_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS reviews (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_item_id BIGINT UNSIGNED NOT NULL,
    listing_id BIGINT UNSIGNED NOT NULL,
    seller_id BIGINT UNSIGNED NOT NULL,
    reviewer_id BIGINT UNSIGNED NOT NULL,
    rating TINYINT UNSIGNED NOT NULL,
    title VARCHAR(150) DEFAULT NULL,
    body TEXT DEFAULT NULL,
    status ENUM('pending','published','hidden','removed') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_review_order_item_reviewer (order_item_id, reviewer_id),
    KEY idx_review_listing (listing_id, status),
    KEY idx_review_seller (seller_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS listing_watchlist (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    listing_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_watchlist_user_listing (user_id, listing_id),
    KEY idx_watchlist_listing (listing_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_audit_log (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_user_id BIGINT UNSIGNED NOT NULL,
    action VARCHAR(120) NOT NULL,
    entity_type VARCHAR(80) DEFAULT NULL,
    entity_id BIGINT UNSIGNED DEFAULT NULL,
    details_json LONGTEXT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_admin_audit_admin (admin_user_id),
    KEY idx_admin_audit_entity (entity_type, entity_id),
    KEY idx_admin_audit_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Suggested starter categories
INSERT IGNORE INTO product_categories (id, parent_id, name, slug, sort_order) VALUES
(1, NULL, 'Digital Goods', 'digital-goods', 10),
(2, NULL, 'Physical Goods', 'physical-goods', 20),
(3, NULL, 'Services', 'services', 30),
(4, 1, 'Wallpapers', 'wallpapers', 11),
(5, 1, 'Tattoo Stencils', 'tattoo-stencils', 12),
(6, 1, 'Fonts & Logos', 'fonts-logos', 13),
(7, 1, 'Music & Beats', 'music-beats', 14),
(8, 1, 'Code & Templates', 'code-templates', 15),
(9, 2, 'Beyond Merchandise', 'beyond-merchandise', 21),
(10, 3, 'Tutoring', 'tutoring', 31),
(11, 3, 'Design', 'design-services', 32),
(12, 3, 'Development', 'development-services', 33),
(13, 3, 'Marketing & Social Media', 'marketing-social-media', 34);
