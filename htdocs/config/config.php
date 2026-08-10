<?php
/**
 * Configuration principale SEIC LBS
 */

define('PROJECT_ROOT', dirname(__DIR__));

$ENV_VARS = [];

// Chargement .env (racine projet)
$env_file = PROJECT_ROOT . DIRECTORY_SEPARATOR . '.env';
if (is_file($env_file) && is_readable($env_file)) {
    $env_lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($env_lines as $env_line) {
        $env_line = trim($env_line, "\r\n\t ");
        if ($env_line === '' || $env_line[0] === '#') {
            continue;
        }
        $parts = explode('=', $env_line, 2);
        if (count($parts) !== 2) {
            continue;
        }
        $key = trim($parts[0]);
        $value = trim($parts[1], " \t\r\n\0\x0B");
        if ($key === '') {
            continue;
        }
        $ENV_VARS[$key] = $value;
        putenv($key . '=' . $value);
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

function env_value($key, $default = null) {
    global $ENV_VARS;

    if (isset($ENV_VARS[$key]) && $ENV_VARS[$key] !== '') {
        return $ENV_VARS[$key];
    }
    if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
        return $_ENV[$key];
    }
    if (isset($_SERVER[$key]) && !is_array($_SERVER[$key]) && $_SERVER[$key] !== '') {
        return $_SERVER[$key];
    }

    $value = getenv($key);
    return ($value === false || $value === '') ? $default : $value;
}

$db_file = __DIR__ . DIRECTORY_SEPARATOR . 'db.php';
if (!is_file($db_file)) {
    die('Fichier config/db.php manquant. Copiez config/db.example.php vers config/db.php');
}
require_once $db_file;

$default_app_env = (defined('ENV') && ENV === 'production') ? 'production' : 'development';
define('APP_ENV', env_value('APP_ENV', $default_app_env));

if (APP_ENV === 'production') {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
    ini_set('log_errors', 1);
} else {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

$log_dir = PROJECT_ROOT . DIRECTORY_SEPARATOR . 'logs';
if (!is_dir($log_dir)) {
    @mkdir($log_dir, 0755, true);
}
ini_set('error_log', $log_dir . DIRECTORY_SEPARATOR . 'php_errors.log');

$pdo_db_host = env_value('DB_HOST', DB_HOST);
$pdo_db_name = env_value('DB_NAME', DB_NAME);
$pdo_db_user = env_value('DB_USER', DB_USER);
$pdo_db_pass = env_value('DB_PASS', DB_PASS);

define('JWT_SECRET', env_value('JWT_SECRET', 'CHANGE_ME_IN_ENV'));

define('UPLOADS_DIR', PROJECT_ROOT . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR);

$default_base_url = (defined('ENV') && ENV === 'production')
    ? '/'
    : '/M-moire_officielle-/htdocs/';
define('BASE_URL', env_value('BASE_URL', $default_base_url));

function url_to(string $path = ''): string {
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

function redirect_to(string $path): void {
    header('Location: ' . url_to($path));
    exit;
}

try {
    $pdo = new PDO(
        'mysql:host=' . $pdo_db_host . ';dbname=' . $pdo_db_name . ';charset=utf8mb4',
        $pdo_db_user,
        $pdo_db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci',
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_TIMEOUT => 30,
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
        ]
    );
} catch (PDOException $e) {
    if (APP_ENV === 'production') {
        error_log('Database connection error: ' . $e->getMessage());
        die('Une erreur technique est survenue. Veuillez réessayer plus tard.');
    }
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}
