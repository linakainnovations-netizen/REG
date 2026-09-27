<?php
/**
 * Giving Gateway abstraction
 * Supports: momo_manual (today, zero fees) + flutterwave + dpo (when keys added)
 * Manual flow: record txn_id -> treasurer verifies -> posts to finance.
 * Gateway flow: build checkout link, verify on callback, then auto-post to finance.
 */

class GivingGateway
{
    public static function providers(): array
    {
        return [
            'momo_manual' => 'Mobile Money (Manual verify)',
            'flutterwave' => 'Flutterwave (Card / MoMo)',
            'dpo' => 'DPO Pay (Card / MoMo)',
        ];
    }

    public static function setting(PDO $pdo, string $key, string $default = ''): string
    {
        // Secrets always come from .env (never trust DB copies).
        $secretEnv = [
            'flutterwave_pub_key' => 'FLUTTERWAVE_PUB_KEY',
            'flutterwave_secret_key' => 'FLUTTERWAVE_SECRET_KEY',
            'dpo_company_token' => 'DPO_COMPANY_TOKEN',
        ];
        if (isset($secretEnv[$key])) {
            $fromEnv = getenv($secretEnv[$key]);
            if ($fromEnv !== false && $fromEnv !== '') return (string)$fromEnv;
            if (defined($secretEnv[$key]) && constant($secretEnv[$key]) !== '') return (string)constant($secretEnv[$key]);
        }
        try {
            $s = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
            $s->execute([$key]);
            $v = $s->fetchColumn();
            return $v !== false ? (string)$v : $default;
        } catch (Throwable $e) {
            return $default;
        }
    }

    public static function isEnabled(PDO $pdo, string $provider): bool
    {
        if ($provider === 'momo_manual') return true;
        if ($provider === 'flutterwave') return self::setting($pdo, 'flutterwave_enabled', '0') === '1' && self::setting($pdo, 'flutterwave_pub_key') !== '';
        if ($provider === 'dpo') return self::setting($pdo, 'dpo_enabled', '0') === '1' && self::setting($pdo, 'dpo_company_token') !== '';
        return false;
    }

    /** Record a pending online intent (gateway or manual). Returns insert id. */
    public static function recordIntent(PDO $pdo, array $d): int
    {
        $stmt = $pdo->prepare(
            "INSERT INTO giving_transactions (giver_name, giver_phone, amount, type, network, txn_id, provider, provider_ref, currency, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'ZMW', 'pending')"
        );
        // txn_id must be unique: for gateway intents use a generated ref
        $txn = $d['txn_id'] ?? ('GW-' . strtoupper($d['provider'] ?? 'FW') . '-' . date('YmdHis') . '-' . substr(bin2hex(random_bytes(3)), 0, 6));
        $stmt->execute([
            $d['giver_name'], $d['giver_phone'], $d['amount'], $d['type'] ?? 'offertory',
            $d['network'] ?? 'MTN', $txn, $d['provider'] ?? 'momo_manual', $d['provider_ref'] ?? null,
        ]);
        return (int)$pdo->lastInsertId();
    }

    /** Mark verified + post once to finance ledger. Idempotent. */
    public static function markVerified(PDO $pdo, int $id, $verifiedBy = null): bool
    {
        $pdo->beginTransaction();
        try {
            $s = $pdo->prepare("SELECT * FROM giving_transactions WHERE id = ? FOR UPDATE");
            $s->execute([$id]);
            $t = $s->fetch(PDO::FETCH_ASSOC);
            if (!$t || $t['status'] === 'verified') {
                $pdo->rollBack();
                return $t && $t['status'] === 'verified';
            }
            $pdo->prepare("UPDATE giving_transactions SET status='verified', verified_by=? WHERE id=?")
                ->execute([$verifiedBy, $id]);
            $finType = in_array($t['type'], ['tithe', 'donation', 'offertory'], true) ? $t['type'] : 'offertory';
            $pdo->prepare("INSERT INTO finance (type, amount, transaction_date, description) VALUES (?, ?, CURDATE(), ?)")
                ->execute([$finType, $t['amount'], "Online giving verified: {$t['giver_name']} {$t['provider']} {$t['txn_id']}"]);
            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            return false;
        }
    }

    /**
     * Build a Flutterwave checkout payload (v3 standard).
     * Caller redirects user to $link. Configure webhook/callback to call markVerified().
     * Docs: https://developer.flutterwave.com/reference/flutterwave-standard
     */
    public static function flutterwaveCheckout(PDO $pdo, array $d, string $redirectUrl): array
    {
        $secret = self::setting($pdo, 'flutterwave_secret_key');
        if (!$secret) return ['ok' => false, 'error' => 'Flutterwave not configured. Add keys in Dashboard → Settings.'];
        $txRef = ($d['tx_ref'] ?? ('STMASS-' . date('YmdHis') . '-' . bin2hex(random_bytes(4))));
        $payload = [
            'tx_ref' => $txRef,
            'amount' => (float)$d['amount'],
            'currency' => 'ZMW',
            'redirect_url' => $redirectUrl,
            'customer' => ['email' => $d['email'] ?? 'giver@parish.local', 'phonenumber' => $d['giver_phone'], 'name' => $d['giver_name']],
            'customizations' => ['title' => 'St. Charles Lwanga Regiment Parish', 'description' => 'Online giving (' . ($d['type'] ?? 'offertory') . ')'],
        ];
        $ch = curl_init('https://api.flutterwave.com/v3/payments');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $secret, 'Content-Type: application/json'],
            CURLOPT_TIMEOUT => 25,
        ]);
        $res = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $json = json_decode((string)$res, true);
        if ($code >= 200 && $code < 300 && isset($json['data']['link'])) {
            return ['ok' => true, 'link' => $json['data']['link'], 'tx_ref' => $txRef];
        }
        return ['ok' => false, 'error' => 'Flutterwave error: ' . substr((string)$res, 0, 200)];
    }

    /** Verify a Flutterwave transaction by id (server-side, after callback). */
    public static function flutterwaveVerify(PDO $pdo, $transactionId): array
    {
        $secret = self::setting($pdo, 'flutterwave_secret_key');
        $ch = curl_init('https://api.flutterwave.com/v3/transactions/' . urlencode((string)$transactionId) . '/verify');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $secret, 'Content-Type: application/json'],
            CURLOPT_TIMEOUT => 25,
        ]);
        $res = curl_exec($ch);
        curl_close($ch);
        $json = json_decode((string)$res, true);
        if (($json['status'] ?? '') === 'success' && ($json['data']['status'] ?? '') === 'successful') {
            return ['ok' => true, 'amount' => $json['data']['amount'] ?? 0, 'currency' => $json['data']['currency'] ?? 'ZMW', 'tx_ref' => $json['data']['tx_ref'] ?? ''];
        }
        return ['ok' => false, 'error' => 'Not successful'];
    }
}
