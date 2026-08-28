<?php

namespace App\Libraries;

use Throwable;

/**
 * Ensures control-plane tables for isolated tenancy, plans, and template sync.
 */
class PlatformArchitecture
{
    public const PLAN_STANDARD = 'standard';
    public const PLAN_PRO = 'pro';
    public const PLAN_ENTERPRISE = 'enterprise';

    /**
     * @return array<string, list<string>>
     */
    public static function defaultPlanFeatures(): array
    {
        $all = array_keys(platform_pos_features());

        return [
            self::PLAN_PRO => $all,
        ];
    }

    public function ensure(): void
    {
        try {
            $db = db_connect('platform');
        } catch (Throwable $e) {
            return;
        }

        if (!$db->tableExists('tenants')) {
            return;
        }

        $this->ensureTables($db);
        $this->ensurePlans($db);
        $this->ensurePlanFeatures($db);
        $this->ensureTemplateMeta($db);
    }

    /**
     * @param object $db
     */
    private function ensureTables($db): void
    {
        $prefix = $db->getPrefix();

        if (!$db->tableExists('plan_features')) {
            $db->query("CREATE TABLE IF NOT EXISTS `{$prefix}plan_features` (
                `plan_id` BIGINT UNSIGNED NOT NULL,
                `feature_id` VARCHAR(64) NOT NULL,
                `enabled` TINYINT(1) NOT NULL DEFAULT 1,
                PRIMARY KEY (`plan_id`, `feature_id`),
                KEY `idx_plan_features_feature` (`feature_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        if (!$db->tableExists('tenant_logins')) {
            $db->query("CREATE TABLE IF NOT EXISTS `{$prefix}tenant_logins` (
                `login_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `tenant_id` BIGINT UNSIGNED NOT NULL,
                `person_id` INT(10) NOT NULL,
                `username` VARCHAR(50) NOT NULL,
                `display_name` VARCHAR(255) NULL,
                `is_owner` TINYINT(1) NOT NULL DEFAULT 0,
                `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`login_id`),
                UNIQUE KEY `uk_tenant_logins_username` (`username`),
                KEY `idx_tenant_logins_tenant` (`tenant_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        if (!$db->tableExists('platform_template_meta')) {
            $db->query("CREATE TABLE IF NOT EXISTS `{$prefix}platform_template_meta` (
                `meta_key` VARCHAR(100) NOT NULL,
                `meta_value` TEXT NULL,
                PRIMARY KEY (`meta_key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        if ($db->tableExists('tenants') && !$db->fieldExists('template_version', 'tenants')) {
            $db->query("ALTER TABLE `{$prefix}tenants`
                ADD COLUMN `template_version` VARCHAR(40) NULL AFTER `db_prefix`");
        }

        if ($db->tableExists('tenants') && !$db->fieldExists('isolated_at', 'tenants')) {
            $db->query("ALTER TABLE `{$prefix}tenants`
                ADD COLUMN `isolated_at` DATETIME NULL AFTER `template_version`");
        }

        if ($db->tableExists('subscription_requests')) {
            $signup_columns = [
                'business_type' => "ALTER TABLE `{$prefix}subscription_requests` ADD COLUMN `business_type` VARCHAR(64) NULL AFTER `tenant_code`",
                'address' => "ALTER TABLE `{$prefix}subscription_requests` ADD COLUMN `address` VARCHAR(255) NULL AFTER `business_type`",
                'city' => "ALTER TABLE `{$prefix}subscription_requests` ADD COLUMN `city` VARCHAR(120) NULL AFTER `address`",
                'country' => "ALTER TABLE `{$prefix}subscription_requests` ADD COLUMN `country` VARCHAR(80) NULL AFTER `city`",
                'tax_id' => "ALTER TABLE `{$prefix}subscription_requests` ADD COLUMN `tax_id` VARCHAR(64) NULL AFTER `country`",
                'payment_token' => "ALTER TABLE `{$prefix}subscription_requests` ADD COLUMN `payment_token` VARCHAR(64) NULL AFTER `payment_reference`",
                'email_verify_token' => "ALTER TABLE `{$prefix}subscription_requests` ADD COLUMN `email_verify_token` VARCHAR(64) NULL AFTER `payment_token`",
                'email_verified_at' => "ALTER TABLE `{$prefix}subscription_requests` ADD COLUMN `email_verified_at` DATETIME NULL AFTER `email_verify_token`",
            ];
            foreach ($signup_columns as $column => $sql) {
                if ($db->fieldExists($column, 'subscription_requests')) {
                    continue;
                }
                try {
                    $db->query($sql);
                    $db->resetDataCache();
                } catch (Throwable $e) {
                    $db->resetDataCache();
                }
            }

            if ($db->fieldExists('payment_token', 'subscription_requests')) {
                $indexes = $db->query("SHOW INDEX FROM `{$prefix}subscription_requests`")->getResultArray();
                $has_token_index = false;
                foreach ($indexes as $index) {
                    if (($index['Key_name'] ?? '') === 'uk_subscription_requests_payment_token') {
                        $has_token_index = true;
                        break;
                    }
                }
                if (!$has_token_index) {
                    $db->query("ALTER TABLE `{$prefix}subscription_requests` ADD UNIQUE KEY `uk_subscription_requests_payment_token` (`payment_token`)");
                }
            }

            if ($db->fieldExists('email_verify_token', 'subscription_requests')) {
                $indexes = $db->query("SHOW INDEX FROM `{$prefix}subscription_requests`")->getResultArray();
                $has_verify_index = false;
                foreach ($indexes as $index) {
                    if (($index['Key_name'] ?? '') === 'uk_subscription_requests_email_verify_token') {
                        $has_verify_index = true;
                        break;
                    }
                }
                if (!$has_verify_index) {
                    try {
                        $db->query("ALTER TABLE `{$prefix}subscription_requests` ADD UNIQUE KEY `uk_subscription_requests_email_verify_token` (`email_verify_token`)");
                    } catch (Throwable $e) {
                        // index already exists
                    }
                }

                $db->query("UPDATE `{$prefix}subscription_requests`
                    SET `email_verified_at` = COALESCE(`reviewed_at`, `created_at`, NOW())
                    WHERE `email_verified_at` IS NULL
                      AND (`status` <> 'pending' OR `email_verify_token` IS NULL)");
            }
        }

        $this->ensureTenantAwaitingPaymentStatus($db);
    }

    /**
     * @param object $db
     */
    private function ensureTenantAwaitingPaymentStatus($db): void
    {
        if (!$db->tableExists('tenants')) {
            return;
        }

        $row = $db->query("SHOW COLUMNS FROM `" . $db->getPrefix() . "tenants` LIKE 'status'")->getRowArray();
        $type = strtolower((string)($row['Type'] ?? ''));
        if (str_contains($type, 'awaiting_payment')) {
            return;
        }

        $db->query("ALTER TABLE `" . $db->getPrefix() . "tenants`
            MODIFY `status` ENUM('active','suspended','cancelled','awaiting_payment') NOT NULL DEFAULT 'active'");
    }

    /**
     * @param object $db
     */
    private function ensurePlans($db): void
    {
        if (!$db->tableExists('plans')) {
            return;
        }

        $pos = [
            'plan_code' => self::PLAN_PRO,
            'plan_name' => 'WBPOS',
            'price_monthly' => 20.00,
            'max_users' => 100,
            'max_locations' => 20,
            'max_items' => 100000,
            'is_active' => 1,
        ];

        $existing = $db->table('plans')->where('plan_code', self::PLAN_PRO)->get(1)->getRowArray();
        if ($existing) {
            $db->table('plans')->where('plan_code', self::PLAN_PRO)->update($pos);
        } else {
            $db->table('plans')->insert($pos);
        }

        $db->table('plans')->update(['price_monthly' => 20.00]);

        $legacy_codes = [self::PLAN_STANDARD, self::PLAN_ENTERPRISE, 'starter', 'basic', 'pos_monthly'];
        foreach ($legacy_codes as $code) {
            if ($db->table('plans')->where('plan_code', $code)->countAllResults() > 0) {
                $db->table('plans')->where('plan_code', $code)->update(['is_active' => 0]);
                $this->reassignLegacyPlan($db, $code, self::PLAN_PRO);
            }
        }
    }

    /**
     * @param object $db
     */
    private function reassignLegacyPlan($db, string $from_code, string $to_code): void
    {
        if (!$db->tableExists('subscriptions')) {
            return;
        }

        $from = $db->table('plans')->select('plan_id')->where('plan_code', $from_code)->get(1)->getRow();
        $to = $db->table('plans')->select('plan_id')->where('plan_code', $to_code)->get(1)->getRow();
        if (!$from || !$to) {
            return;
        }

        $db->table('subscriptions')
            ->where('plan_id', (int)$from->plan_id)
            ->update(['plan_id' => (int)$to->plan_id]);

        if ($db->tableExists('subscription_requests')) {
            $db->table('subscription_requests')
                ->where('plan_id', (int)$from->plan_id)
                ->update(['plan_id' => (int)$to->plan_id]);
        }
    }

    /**
     * @param object $db
     */
    private function ensurePlanFeatures($db): void
    {
        if (!$db->tableExists('plan_features') || !$db->tableExists('plans')) {
            return;
        }

        foreach (self::defaultPlanFeatures() as $plan_code => $feature_ids) {
            $plan = $db->table('plans')->select('plan_id')->where('plan_code', $plan_code)->get(1)->getRow();
            if (!$plan) {
                continue;
            }

            $this->enableAllFeaturesForPlan($db, (int)$plan->plan_id);
        }
    }

    /**
     * @param object $db
     */
    private function enableAllFeaturesForPlan($db, int $plan_id): void
    {
        foreach (array_keys(platform_pos_features()) as $feature_id) {
            $exists = $db->table('plan_features')
                ->where('plan_id', $plan_id)
                ->where('feature_id', $feature_id)
                ->countAllResults();
            if ($exists > 0) {
                $db->table('plan_features')
                    ->where('plan_id', $plan_id)
                    ->where('feature_id', $feature_id)
                    ->update(['enabled' => 1]);
                continue;
            }

            $db->table('plan_features')->insert([
                'plan_id' => $plan_id,
                'feature_id' => $feature_id,
                'enabled' => 1,
            ]);
        }
    }

    /**
     * @param object $db
     */
    private function ensureTemplateMeta($db): void
    {
        if (!$db->tableExists('platform_template_meta')) {
            return;
        }

        if ($db->table('platform_template_meta')->where('meta_key', 'template_version')->countAllResults() === 0) {
            $db->table('platform_template_meta')->insert([
                'meta_key' => 'template_version',
                'meta_value' => '1',
            ]);
        }
        if ($db->table('platform_template_meta')->where('meta_key', 'last_sync_at')->countAllResults() === 0) {
            $db->table('platform_template_meta')->insert([
                'meta_key' => 'last_sync_at',
                'meta_value' => null,
            ]);
        }
    }

    public function getTemplateMeta(): array
    {
        $defaults = [
            'template_version' => '1',
            'last_sync_at' => null,
            'last_sync_report' => null,
        ];

        try {
            $db = db_connect('platform');
        } catch (Throwable $e) {
            return $defaults;
        }

        if (!$db->tableExists('platform_template_meta')) {
            return $defaults;
        }

        foreach ($db->table('platform_template_meta')->get()->getResultArray() as $row) {
            $defaults[$row['meta_key']] = $row['meta_value'];
        }

        return $defaults;
    }

    public function bumpTemplateVersion(): string
    {
        $db = db_connect('platform');
        $current = (int)($this->getTemplateMeta()['template_version'] ?? 1);
        $next = (string)($current + 1);
        $db->table('platform_template_meta')
            ->where('meta_key', 'template_version')
            ->update(['meta_value' => $next]);

        return $next;
    }

    public function setTemplateMeta(string $key, ?string $value): void
    {
        $db = db_connect('platform');
        if (!$db->tableExists('platform_template_meta')) {
            return;
        }

        $exists = $db->table('platform_template_meta')->where('meta_key', $key)->countAllResults();
        if ($exists > 0) {
            $db->table('platform_template_meta')->where('meta_key', $key)->update(['meta_value' => $value]);
            return;
        }

        $db->table('platform_template_meta')->insert([
            'meta_key' => $key,
            'meta_value' => $value,
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getActivePlans(): array
    {
        try {
            $db = db_connect('platform');
        } catch (Throwable $e) {
            return [];
        }

        if (!$db->tableExists('plans')) {
            return [];
        }

        return $db->table('plans')
            ->where('is_active', 1)
            ->where('plan_code', self::PLAN_PRO)
            ->orderBy('price_monthly', 'asc')
            ->get()
            ->getResultArray();
    }

    /**
     * @return array<int, array<string, bool>>
     */
    public function getPlanFeatureMatrix(): array
    {
        $matrix = [];
        try {
            $db = db_connect('platform');
        } catch (Throwable $e) {
            return $matrix;
        }

        if (!$db->tableExists('plan_features')) {
            return $matrix;
        }

        foreach ($db->table('plan_features')->get()->getResultArray() as $row) {
            $matrix[(int)$row['plan_id']][(string)$row['feature_id']] = (int)$row['enabled'] === 1;
        }

        return $matrix;
    }

    public function setPlanFeature(int $plan_id, string $feature_id, bool $enabled): bool
    {
        if (!array_key_exists($feature_id, platform_pos_features())) {
            return false;
        }

        $db = db_connect('platform');
        if (!$db->tableExists('plan_features')) {
            return false;
        }

        $exists = $db->table('plan_features')
            ->where('plan_id', $plan_id)
            ->where('feature_id', $feature_id)
            ->countAllResults();

        if ($exists > 0) {
            return $db->table('plan_features')
                ->where('plan_id', $plan_id)
                ->where('feature_id', $feature_id)
                ->update(['enabled' => $enabled ? 1 : 0]);
        }

        return $db->table('plan_features')->insert([
            'plan_id' => $plan_id,
            'feature_id' => $feature_id,
            'enabled' => $enabled ? 1 : 0,
        ]);
    }

    public function getTenantPlanId(int $tenant_id): ?int
    {
        if ($tenant_id <= 0) {
            return null;
        }

        try {
            $db = db_connect('platform');
        } catch (Throwable $e) {
            return null;
        }

        if (!$db->tableExists('subscriptions')) {
            return null;
        }

        $row = $db->table('subscriptions')
            ->select('plan_id')
            ->where('tenant_id', $tenant_id)
            ->whereIn('status', ['active', 'trialing'])
            ->orderBy('subscription_id', 'desc')
            ->get(1)
            ->getRow();

        return $row ? (int)$row->plan_id : null;
    }

    /**
     * @return list<string>
     */
    public function getEnabledFeatureIdsForPlan(?int $plan_id): array
    {
        $all = array_keys(platform_pos_features());
        if ($plan_id === null || $plan_id <= 0) {
            return $all;
        }

        try {
            $db = db_connect('platform');
        } catch (Throwable $e) {
            return $all;
        }

        if (!$db->tableExists('plan_features')) {
            return $all;
        }

        $rows = $db->table('plan_features')
            ->select('feature_id')
            ->where('plan_id', $plan_id)
            ->where('enabled', 1)
            ->get()
            ->getResultArray();

        if ($rows === []) {
            return $all;
        }

        return array_values(array_intersect($all, array_column($rows, 'feature_id')));
    }

    public function upsertTenantLogin(
        int $tenant_id,
        int $person_id,
        string $username,
        string $display_name = '',
        bool $is_owner = false,
        bool $is_active = true
    ): void {
        if ($tenant_id <= 0 || $person_id <= 0 || $username === '') {
            return;
        }

        try {
            $db = db_connect('platform');
        } catch (Throwable $e) {
            return;
        }

        if (!$db->tableExists('tenant_logins')) {
            return;
        }

        $existing = $db->table('tenant_logins')
            ->where('tenant_id', $tenant_id)
            ->where('person_id', $person_id)
            ->get(1)
            ->getRowArray();

        $payload = [
            'username' => $username,
            'display_name' => $display_name,
            'is_owner' => $is_owner ? 1 : 0,
            'is_active' => $is_active ? 1 : 0,
        ];

        if ($existing) {
            $db->table('tenant_logins')
                ->where('login_id', (int)$existing['login_id'])
                ->update($payload);
            return;
        }

        $taken = $db->table('tenant_logins')->where('username', $username)->countAllResults();
        if ($taken > 0) {
            return;
        }

        $db->table('tenant_logins')->insert($payload + [
            'tenant_id' => $tenant_id,
            'person_id' => $person_id,
        ]);
    }

    public function usernameExists(string $username): bool
    {
        try {
            $db = db_connect('platform');
        } catch (Throwable $e) {
            return false;
        }

        if ($db->tableExists('tenant_logins')
            && $db->table('tenant_logins')->where('username', $username)->countAllResults() > 0
        ) {
            return true;
        }

        try {
            $app = db_connect();
            if ($app->tableExists('employees')
                && $app->table('employees')->where('username', $username)->countAllResults() > 0
            ) {
                return true;
            }
        } catch (Throwable $e) {
            return false;
        }

        return false;
    }
}
