<?php

namespace Config;

use CodeIgniter\Database\Config;

/**
 * Database Configuration
 */
class Database extends Config
{
    /**
     * The directory that holds the Migrations and Seeds directories.
     */
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;

    /**
     * Lets you choose which connection group to use if no other is specified.
     */
    public string $defaultGroup = 'default';

    /**
     * The default database connection.
     *
     * @var array<string, mixed>
     */
    public array $default = [
        'DSN'          => '',
        'hostname'     => 'localhost',
        'username'     => 'admin',
        'password'     => 'pointofsale',
        'database'     => 'ospos',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => 'ospos_',
        'pConnect'     => false,
        'DBDebug'      => (ENVIRONMENT !== 'production'),
        'charset'      => 'utf8mb4',
        'DBCollat'     => 'utf8mb4_general_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 3306,
        'dateFormat'   => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    /**
     * Platform/control-plane database (tenants, subscriptions, platform admins).
     *
     * @var array<string, mixed>
     */
    public array $platform = [
        'DSN'          => '',
        'hostname'     => 'localhost',
        'username'     => 'admin',
        'password'     => 'pointofsale',
        'database'     => 'ospos',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => 'ospos_',
        'pConnect'     => false,
        'DBDebug'      => (ENVIRONMENT !== 'production'),
        'charset'      => 'utf8mb4',
        'DBCollat'     => 'utf8mb4_general_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 3306,
        'dateFormat'   => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    /**
     * Tenant/data-plane database for operational POS data.
     * This group can be overridden per request using tenant session metadata.
     *
     * @var array<string, mixed>
     */
    public array $tenant = [
        'DSN'          => '',
        'hostname'     => 'localhost',
        'username'     => 'admin',
        'password'     => 'pointofsale',
        'database'     => 'ospos',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => 'ospos_',
        'pConnect'     => false,
        'DBDebug'      => (ENVIRONMENT !== 'production'),
        'charset'      => 'utf8mb4',
        'DBCollat'     => 'utf8mb4_general_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 3306,
        'dateFormat'   => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    /**
     * This database connection is used when running PHPUnit database tests.
     *
     * @var array<string, mixed>
     */
    public array $tests = [
        'DSN'          => '',
        'hostname'     => 'localhost',
        'username'     => 'admin',
        'password'     => 'pointofsale',
        'database'     => 'ospos',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => 'ospos_',
        'pConnect'     => false,
        'DBDebug'      => (ENVIRONMENT !== 'production'),
        'charset'      => 'utf8mb4',
        'DBCollat'     => 'utf8mb4_general_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 3306,
        'foreignKeys'  => true,
        'busyTimeout'  => 1000,
        'dateFormat'   => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    /**
     * This database connection is used when developing against non-production data.
     *
     * @var array
     */
    public $development = [
        'DSN'          => '',
        'hostname'     => 'localhost',
        'username'     => 'admin',
        'password'     => 'pointofsale',
        'database'     => 'ospos',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => 'ospos_',
        'pConnect'     => false,
        'DBDebug'      => (ENVIRONMENT !== 'production'),
        'charset'      => 'utf8mb4',
        'DBCollat'     => 'utf8mb4_general_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 3306,
        'foreignKeys'  => true,
        'busyTimeout'  => 1000,
        'dateFormat'   => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    public function __construct()
    {
        parent::__construct();

        // Ensure that we always set the database group to 'tests' if
        // we are currently running an automated test suite, so that
        // we don't overwrite live data on accident.
        switch (ENVIRONMENT) {
            case 'testing':
                $this->defaultGroup = 'tests';
                break;
            case 'development';
                $this->defaultGroup = 'development';
                break;
        }

        // Respect CodeIgniter-style dotenv keys used by this project.
        $this->applyDotEnvGroupOverride($this->default, 'database.default');
        $this->applyDotEnvGroupOverride($this->development, 'database.development');
        $this->applyDotEnvGroupOverride($this->tests, 'database.tests');

        foreach ([&$this->development, &$this->tests, &$this->default, &$this->tenant, &$this->platform] as &$config) {
            $config['hostname'] = !getenv('MYSQL_HOST_NAME') ? $config['hostname'] : getenv('MYSQL_HOST_NAME');
            $config['username'] = !getenv('MYSQL_USERNAME') ? $config['username'] : getenv('MYSQL_USERNAME');
            $config['password'] = !getenv('MYSQL_PASSWORD') ? $config['password'] : getenv('MYSQL_PASSWORD');
            $config['database'] = !getenv('MYSQL_DB_NAME') ? $config['database'] : getenv('MYSQL_DB_NAME');
            $config['port'] = !getenv('MYSQL_PORT') ? $config['port'] : (int)getenv('MYSQL_PORT');
            if (!isset($config['DBPrefix']) || trim((string)$config['DBPrefix']) === '') {
                $config['DBPrefix'] = 'ospos_';
            }
        }

        // By default, tenant/platform follow the active app database credentials.
        foreach (['hostname', 'username', 'password', 'database', 'port', 'DBPrefix'] as $key) {
            $this->tenant[$key] = $this->default[$key];
            $this->platform[$key] = $this->default[$key];
        }

        $this->platform['hostname'] = !getenv('PLATFORM_MYSQL_HOST_NAME') ? $this->platform['hostname'] : getenv('PLATFORM_MYSQL_HOST_NAME');
        $this->platform['username'] = !getenv('PLATFORM_MYSQL_USERNAME') ? $this->platform['username'] : getenv('PLATFORM_MYSQL_USERNAME');
        $this->platform['password'] = !getenv('PLATFORM_MYSQL_PASSWORD') ? $this->platform['password'] : getenv('PLATFORM_MYSQL_PASSWORD');
        $this->platform['database'] = !getenv('PLATFORM_MYSQL_DB_NAME') ? $this->platform['database'] : getenv('PLATFORM_MYSQL_DB_NAME');
        $this->platform['port'] = !getenv('PLATFORM_MYSQL_PORT') ? $this->platform['port'] : (int)getenv('PLATFORM_MYSQL_PORT');

        // Per-request tenant DB override from session (set at login/bootstrap).
        $this->applyTenantSessionOverride();

        // Final safety: OSPOS schema uses prefixed tables (ospos_*).
        foreach ([&$this->development, &$this->tests, &$this->default, &$this->tenant, &$this->platform] as &$config) {
            if (!isset($config['DBPrefix']) || trim((string)$config['DBPrefix']) === '') {
                $config['DBPrefix'] = 'ospos_';
            }
        }
    }

    private function applyTenantSessionOverride(): void
    {
        if (is_cli() || session_status() !== PHP_SESSION_ACTIVE || empty($_SESSION) || !is_array($_SESSION)) {
            return;
        }

        $tenant_db_name = (string)($_SESSION['tenant_db_name'] ?? '');

        if ($tenant_db_name === '') {
            return;
        }

        $this->tenant['hostname'] = (string)($_SESSION['tenant_db_hostname'] ?? $this->tenant['hostname']);
        $this->tenant['username'] = (string)($_SESSION['tenant_db_username'] ?? $this->tenant['username']);
        $this->tenant['password'] = (string)($_SESSION['tenant_db_password'] ?? $this->tenant['password']);
        $this->tenant['database'] = $tenant_db_name;
        $this->tenant['port'] = (int)($_SESSION['tenant_db_port'] ?? $this->tenant['port']);
        $session_prefix = trim((string)($_SESSION['tenant_db_prefix'] ?? ''));
        if ($session_prefix !== '') {
            $this->tenant['DBPrefix'] = $session_prefix;
        }
    }

    /**
     * Load DB credentials from .env keys like database.default.username.
     *
     * @param array<string, mixed> $config
     */
    private function applyDotEnvGroupOverride(array &$config, string $prefix): void
    {
        $hostname = env($prefix . '.hostname');
        $username = env($prefix . '.username');
        $password = env($prefix . '.password');
        $database = env($prefix . '.database');
        $dbPrefix = env($prefix . '.DBPrefix');
        $port = env($prefix . '.port');

        if ($hostname !== null && $hostname !== false && $hostname !== '') {
            $config['hostname'] = (string)$hostname;
        }
        if ($username !== null && $username !== false && $username !== '') {
            $config['username'] = (string)$username;
        }
        if ($password !== null && $password !== false) {
            $config['password'] = (string)$password;
        }
        if ($database !== null && $database !== false && $database !== '') {
            $config['database'] = (string)$database;
        }
        if ($dbPrefix !== null && $dbPrefix !== false && $dbPrefix !== '') {
            $config['DBPrefix'] = (string)$dbPrefix;
        }
        if ($port !== null && $port !== false && $port !== '') {
            $config['port'] = (int)$port;
        }
    }
}
