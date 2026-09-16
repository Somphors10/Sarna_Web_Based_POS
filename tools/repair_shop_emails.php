<?php
/**
 * One-time repair: replace demo/empty Configuration email with registration owner email.
 * Run: php tools/repair_shop_emails.php
 */

declare(strict_types=1);

$mysqli = @new mysqli('localhost', 'admin', 'pointofsale', 'wbpos');
if ($mysqli->connect_error) {
    fwrite(STDERR, 'DB connect failed: ' . $mysqli->connect_error . PHP_EOL);
    exit(1);
}
$mysqli->set_charset('utf8mb4');

$demoEmails = [
    'admin@wbpos.demo',
    'changeme@example.com',
    '',
];

function isPlaceholderEmail(string $email, array $demoEmails): bool
{
    $email = strtolower(trim($email));
    return $email === '' || in_array($email, $demoEmails, true);
}

function upsertTenantConfig(mysqli $db, int $tenantId, string $key, string $value): void
{
    $stmt = $db->prepare(
        'INSERT INTO wbpos_tenant_config (tenant_id, config_key, config_value)
         VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE config_value = VALUES(config_value)'
    );
    $stmt->bind_param('iss', $tenantId, $key, $value);
    $stmt->execute();
    $stmt->close();
}

function upsertAppConfig(mysqli $db, string $key, string $value): void
{
    $stmt = $db->prepare(
        'INSERT INTO wbpos_app_config (`key`, `value`)
         VALUES (?, ?)
         ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)'
    );
    $stmt->bind_param('ss', $key, $value);
    $stmt->execute();
    $stmt->close();
}

function resolveOwnerEmail(mysqli $platform, array $tenant): string
{
    $tenantId = (int)$tenant['tenant_id'];
    $code = (string)$tenant['tenant_code'];

    // 1) Latest approved/paid registration for this tenant_code
    $stmt = $platform->prepare(
        "SELECT owner_email FROM wbpos_subscription_requests
         WHERE tenant_code = ? AND owner_email IS NOT NULL AND owner_email != ''
         ORDER BY FIELD(status, 'approved', 'paid', 'pending', 'rejected'), request_id DESC
         LIMIT 1"
    );
    $stmt->bind_param('s', $code);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!empty($res['owner_email'])) {
        return strtolower(trim((string)$res['owner_email']));
    }

    // 2) Owner person email in this (platform/shared) DB
    $sql = "SELECT p.email
            FROM wbpos_people p
            INNER JOIN wbpos_tenant_users tu ON tu.person_id = p.person_id AND tu.tenant_id = p.tenant_id
            WHERE p.tenant_id = ? AND tu.tenant_role = 'owner' AND p.email IS NOT NULL AND p.email != ''
            LIMIT 1";
    $stmt = $platform->prepare($sql);
    $stmt->bind_param('i', $tenantId);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!empty($res['email'])) {
        return strtolower(trim((string)$res['email']));
    }

    // 3) Any employee email for tenant
    $sql = "SELECT p.email
            FROM wbpos_people p
            INNER JOIN wbpos_employees e ON e.person_id = p.person_id
            WHERE p.tenant_id = ? AND p.email IS NOT NULL AND p.email != ''
              AND p.email NOT IN ('admin@wbpos.demo', 'changeme@example.com')
            ORDER BY p.person_id ASC
            LIMIT 1";
    $stmt = $platform->prepare($sql);
    $stmt->bind_param('i', $tenantId);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return strtolower(trim((string)($res['email'] ?? '')));
}

function currentConfigEmail(mysqli $db, int $tenantId): string
{
    $stmt = $db->prepare(
        "SELECT config_value FROM wbpos_tenant_config WHERE tenant_id = ? AND config_key = 'email' LIMIT 1"
    );
    $stmt->bind_param('i', $tenantId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($row !== null) {
        return (string)$row['config_value'];
    }

    $res = $db->query("SELECT `value` FROM wbpos_app_config WHERE `key` = 'email' LIMIT 1");
    $row = $res ? $res->fetch_assoc() : null;

    return (string)($row['value'] ?? '');
}

$tenants = [];
$res = $mysqli->query(
    "SELECT tenant_id, tenant_code, company_name, db_name, db_hostname, db_port, db_username, db_password, db_prefix, status
     FROM wbpos_tenants
     WHERE tenant_code != 'platform'
     ORDER BY tenant_id"
);
while ($row = $res->fetch_assoc()) {
    $tenants[] = $row;
}

$fixed = 0;
$skipped = 0;
$failed = 0;

foreach ($tenants as $tenant) {
    $tenantId = (int)$tenant['tenant_id'];
    $ownerEmail = resolveOwnerEmail($mysqli, $tenant);
    if ($ownerEmail === '' || isPlaceholderEmail($ownerEmail, $demoEmails)) {
        echo "[skip] tenant {$tenantId} ({$tenant['tenant_code']}): no real registration email found\n";
        $skipped++;
        continue;
    }

    $dbName = trim((string)($tenant['db_name'] ?? ''));
    $usePrivate = $dbName !== '' && strcasecmp($dbName, 'wbpos') !== 0;

    try {
        if ($usePrivate) {
            $host = (string)($tenant['db_hostname'] ?: 'localhost');
            $user = (string)($tenant['db_username'] ?: 'admin');
            $pass = (string)($tenant['db_password'] ?: 'pointofsale');
            $port = (int)($tenant['db_port'] ?: 3306);
            $shop = @new mysqli($host, $user, $pass, $dbName, $port);
            if ($shop->connect_error) {
                throw new RuntimeException($shop->connect_error);
            }
            $shop->set_charset('utf8mb4');

            // Private DB may still use tenant_id column values.
            $current = currentConfigEmail($shop, $tenantId);
            if (!isPlaceholderEmail($current, $demoEmails) && strtolower($current) === $ownerEmail) {
                echo "[ok]   tenant {$tenantId} private DB already has {$ownerEmail}\n";
                $shop->close();
                $skipped++;
                continue;
            }

            upsertTenantConfig($shop, $tenantId, 'email', $ownerEmail);
            upsertAppConfig($shop, 'email', $ownerEmail);
            $shop->close();
            echo "[fix] tenant {$tenantId} ({$tenant['tenant_code']}) private DB: {$current} -> {$ownerEmail}\n";
            $fixed++;
        } else {
            $current = currentConfigEmail($mysqli, $tenantId);
            if (strtolower(trim($current)) === $ownerEmail) {
                echo "[ok]   tenant {$tenantId} already has {$ownerEmail}\n";
                $skipped++;
                continue;
            }
            // Only replace demo/empty placeholders — never overwrite a real custom company email.
            if (!isPlaceholderEmail($current, $demoEmails)) {
                echo "[skip] tenant {$tenantId} keeps custom email {$current}\n";
                $skipped++;
                continue;
            }

            upsertTenantConfig($mysqli, $tenantId, 'email', $ownerEmail);
            // Shared DB: only rewrite app_config when this is the only/active shop reading it,
            // or when app_config still has demo email.
            $appRes = $mysqli->query("SELECT `value` FROM wbpos_app_config WHERE `key` = 'email' LIMIT 1");
            $appRow = $appRes ? $appRes->fetch_assoc() : null;
            $appEmail = (string)($appRow['value'] ?? '');
            if (isPlaceholderEmail($appEmail, $demoEmails)) {
                upsertAppConfig($mysqli, 'email', $ownerEmail);
            }

            echo "[fix] tenant {$tenantId} ({$tenant['tenant_code']}): {$current} -> {$ownerEmail}\n";
            $fixed++;
        }
    } catch (Throwable $e) {
        echo "[fail] tenant {$tenantId} ({$tenant['tenant_code']}): {$e->getMessage()}\n";
        $failed++;
    }
}

echo PHP_EOL . "Done. fixed={$fixed} skipped={$skipped} failed={$failed}" . PHP_EOL;
$mysqli->close();
