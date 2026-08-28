<?php
/**
 * Temporary diagnostic — delete after password reset works.
 * Usage: /public/check-forgot-password.php?email=you@example.com
 *        /public/check-forgot-password.php?email=you@example.com&send=1
 */
ini_set('display_errors', '1');
error_reporting(E_ALL);
header('Content-Type: text/plain; charset=utf-8');

$root = realpath(__DIR__ . '/..') ?: (__DIR__ . '/..');
$test_email = strtolower(trim((string)($_GET['email'] ?? '')));

echo "Forgot password diagnostic\n";
echo "==========================\n\n";

$files = [
    'app/Models/Password_reset_request.php',
    'app/Controllers/Login.php',
    'app/Libraries/PlatformMail.php',
    'vendor/autoload.php',
];

foreach ($files as $rel) {
    $path = $root . '/' . str_replace('/', DIRECTORY_SEPARATOR, $rel);
    echo ($rel . ': ' . (is_file($path) ? 'OK' : 'MISSING')) . PHP_EOL;
}

$model_file = $root . '/app/Models/Password_reset_request.php';
if (is_file($model_file)) {
    $src = file_get_contents($model_file);
    echo 'Registration email fallback: ' . (str_contains($src, 'build_employee_from_registration') ? 'OK' : 'OLD FILE') . PHP_EOL;
    echo 'Shared DB email lookup: ' . (str_contains($src, 'resolve_employee_by_email_shared') ? 'OK' : 'OLD FILE') . PHP_EOL;
}

echo PHP_EOL . 'PHP: ' . PHP_VERSION . PHP_EOL;

if ($test_email === '' || !filter_var($test_email, FILTER_VALIDATE_EMAIL)) {
    echo "\nAdd ?email=your@email.com to test account lookup and mail.\n";
    echo "Add &send=1 to send a real test reset email.\n";
    echo "Delete this file after fixing.\n";
    exit;
}

/**
 * Quick DB-only check (works even if CI bootstrap fails).
 */
function diag_read_env(string $root, string $key): string
{
    $envFile = $root . '/.env';
    if (!is_file($envFile)) {
        return '';
    }
    $env = file_get_contents($envFile);
    if (preg_match('/' . preg_quote($key, '/') . "\s*=\s*'([^']*)'/", $env, $m)) {
        return $m[1];
    }
    if (preg_match('/' . preg_quote($key, '/') . '\s*=\s*(\S+)/', $env, $m)) {
        return trim($m[1]);
    }

    return '';
}

function diag_db_lookup(string $root, string $email): void
{
    $host = diag_read_env($root, 'database.default.hostname') ?: 'localhost';
    $dbName = diag_read_env($root, 'database.default.database');
    $user = diag_read_env($root, 'database.default.username');
    $pass = diag_read_env($root, 'database.default.password');
    $prefix = diag_read_env($root, 'database.default.DBPrefix') ?: 'wbpos_';

    if ($dbName === '') {
        echo "DB config: missing in .env\n";
        return;
    }

    mysqli_report(MYSQLI_REPORT_OFF);
    $mysqli = @new mysqli($host, $user, $pass, $dbName);
    if ($mysqli->connect_error) {
        echo 'DB connect: FAIL ' . $mysqli->connect_error . PHP_EOL;
        return;
    }

    echo "DB connect: OK ({$dbName})\n";

    $table = $prefix . 'subscription_requests';
    $stmt = $mysqli->prepare(
        "SELECT tenant_code, owner_username, owner_email, status
         FROM `{$table}`
         WHERE LOWER(owner_email) = ?
         ORDER BY request_id DESC
         LIMIT 1"
    );
    if ($stmt) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        if ($row) {
            echo "Registration email in DB: YES\n";
            echo '  tenant_code: ' . ($row['tenant_code'] ?? '') . PHP_EOL;
            echo '  username: ' . ($row['owner_username'] ?? '') . PHP_EOL;
            echo '  status: ' . ($row['status'] ?? '') . PHP_EOL;
        } else {
            echo "Registration email in DB: NO\n";
        }
    }

    $mysqli->close();
}

try {
    if (!defined('FCPATH')) {
        define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
    }
    chdir(FCPATH);

    require FCPATH . '../app/Config/Paths.php';
    $paths = new Config\Paths();

    define('APPPATH', realpath(rtrim($paths->appDirectory, '\\/ ')) . DIRECTORY_SEPARATOR);
    define('ROOTPATH', realpath(APPPATH . '../') . DIRECTORY_SEPARATOR);
    define('SYSTEMPATH', realpath(rtrim($paths->systemDirectory, '\\/ ')) . DIRECTORY_SEPARATOR);
    define('WRITEPATH', realpath(rtrim($paths->writableDirectory, '\\/ ')) . DIRECTORY_SEPARATOR);
    define('TESTPATH', realpath(rtrim($paths->testsDirectory, '\\/ ')) . DIRECTORY_SEPARATOR);

    if (!is_dir(SYSTEMPATH)) {
        throw new RuntimeException('CodeIgniter system folder not found: ' . SYSTEMPATH);
    }

    require_once APPPATH . 'Config/Constants.php';

    if (is_file(COMPOSER_PATH)) {
        require_once COMPOSER_PATH;
    }

    require_once SYSTEMPATH . 'Config/DotEnv.php';
    (new CodeIgniter\Config\DotEnv(ROOTPATH))->load();

    if (!defined('ENVIRONMENT')) {
        $env = $_ENV['CI_ENVIRONMENT'] ?? $_SERVER['CI_ENVIRONMENT'] ?? getenv('CI_ENVIRONMENT') ?: 'production';
        define('ENVIRONMENT', $env);
    }
    defined('CI_DEBUG') || define('CI_DEBUG', false);

    require APPPATH . 'Config/Boot/' . ENVIRONMENT . '.php';

    require_once APPPATH . 'Common.php';
    require_once SYSTEMPATH . 'Common.php';

    require_once SYSTEMPATH . 'Config/AutoloadConfig.php';
    require_once APPPATH . 'Config/Autoload.php';
    require_once SYSTEMPATH . 'Modules/Modules.php';
    require_once APPPATH . 'Config/Modules.php';
    require_once SYSTEMPATH . 'Autoloader/Autoloader.php';
    require_once SYSTEMPATH . 'Config/BaseService.php';
    require_once SYSTEMPATH . 'Config/Services.php';
    require_once APPPATH . 'Config/Services.php';
    Config\Services::autoloader()->initialize(new Config\Autoload(), new Config\Modules())->register();
    Config\Services::autoloader()->loadHelpers();
    Config\Services::exceptions()->initialize();

    $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'pos.chhit.com';
    $_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/public/check-forgot-password.php';
    $_SERVER['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $_SERVER['SERVER_PROTOCOL'] = $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1';
    $_SERVER['HTTPS'] = $_SERVER['HTTPS'] ?? 'on';

    $app = Config\Services::codeigniter();
    $app->initialize();
    $app->setContext('web');

    $reset_model = model(\App\Models\Password_reset_request::class);
    $mail = new \App\Libraries\PlatformMail();

    echo "\nTest email: {$test_email}\n";
    echo 'Table ready: ' . ($reset_model->table_ready() ? 'yes' : 'NO') . PHP_EOL;

    $delivery = \App\Libraries\PlatformMail::deliveryInfo();
    echo 'Mail mode: ' . ($delivery['mode'] ?? 'none') . PHP_EOL;
    echo 'Mail from: ' . ($delivery['from'] ?? '') . PHP_EOL;

    $employee = $reset_model->resolve_employee_by_email($test_email);
    if ($employee === null) {
        echo "Account lookup: NOT FOUND\n";
        echo "\n--- DB check ---\n";
        diag_db_lookup($root, $test_email);
        echo "Use the exact email from your WBPOS verify/KHQR message.\n";
        exit;
    }

    echo "Account lookup: FOUND\n";
    echo '  tenant_code: ' . ($employee->tenant_code ?? '') . PHP_EOL;
    echo '  username: ' . ($employee->username ?? '') . PHP_EOL;
    echo '  tenant_id: ' . ($employee->tenant_id ?? '') . PHP_EOL;
    echo '  person_id: ' . ($employee->person_id ?? '') . PHP_EOL;

    if (empty($_GET['send'])) {
        echo "\nLookup OK. Add &send=1 to send a test reset email.\n";
        exit;
    }

    $employee->email = $test_email;
    $token = $reset_model->create_token($employee, $test_email);
    $reset_url = site_url('login/reset-password/' . $token);
    echo 'Reset URL: ' . $reset_url . PHP_EOL;

    $result = $mail->sendPasswordReset(
        $employee,
        (string)($employee->company_name ?? 'your shop'),
        $reset_url
    );

    echo "\nSend result: " . ($result['ok'] ? 'OK' : 'FAILED') . PHP_EOL;
    if (!$result['ok']) {
        echo 'Error: ' . ($result['error'] ?? '') . PHP_EOL;
    } else {
        echo "Check inbox AND Spam for: Reset your WBPOS password\n";
    }
} catch (Throwable $e) {
    echo "\nERROR: " . $e->getMessage() . PHP_EOL;
    echo $e->getFile() . ':' . $e->getLine() . PHP_EOL;
    echo "\n--- DB check (fallback) ---\n";
    diag_db_lookup($root, $test_email);
}

echo "\nDelete this file after fixing.\n";
