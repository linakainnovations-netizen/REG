<?php
/**
 * Parish Settings - St. Charles Lwanga Regiment Parish
 * Values come from `.env` (see .env.example). Dashboard → Settings
 * can override the PUBLIC links (stored in site_settings table);
 * SECRETS are read from .env only.
 */
require_once __DIR__ . '/../backend/env_loader.php';
loadEnv(dirname(__DIR__));

define('PARISH_NAME', env('PARISH_NAME', 'St. Charles Lwanga Regiment Parish'));
define('PARISH_ADDRESS', env('PARISH_ADDRESS', 'Chitukuko Road, Lusaka, Zambia'));
define('PARISH_PHONE', env('PARISH_PHONE', '0975255734'));
define('PARISH_PHONE_INTL', env('PARISH_PHONE_INTL', '+260975255734'));

// Live stream sources (Option 2: embed FB + YT, no video stored in DB)
define('FB_PAGE_URL', env('FB_PAGE_URL', 'https://web.facebook.com/groups/539879469476464/events'));
define('YT_CHANNEL_URL', env('YT_CHANNEL_URL', 'https://www.youtube.com/'));
define('YT_CHANNEL_ID', env('YT_CHANNEL_ID', ''));

// Online giving - manual MoMo verification (no API keys needed)
// Treasurer verifies transaction IDs submitted via giving form.
define('MOMO_MTN_NUMBER', env('MOMO_MTN_NUMBER', '0975255734'));
define('MOMO_AIRTEL_NUMBER', env('MOMO_AIRTEL_NUMBER', '0975255734'));
define('MOMO_NAME', env('MOMO_NAME', 'St. Charles Lwanga Regiment Parish'));

// Gateway keys — from .env ONLY (preferred over anything in DB).
define('FLUTTERWAVE_PUB_KEY', env('FLUTTERWAVE_PUB_KEY', ''));
define('FLUTTERWAVE_SECRET_KEY', env('FLUTTERWAVE_SECRET_KEY', ''));
define('DPO_COMPANY_TOKEN', env('DPO_COMPANY_TOKEN', ''));
