	<?php
    // Source - https://stackoverflow.com/a/47261330
    // Posted by Vicky Mahale
    // Retrieved 2026-04-28, License - CC BY-SA 3.0

    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);

    // !important - Other configs:
// - .htaccess

    function loadEnv($path) {
        if (!file_exists($path)) return;

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            list($name, $value) = explode('=', $line, 2);

            $name = trim($name);
            $value = trim($value);

            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
            }
        }
    }

    loadEnv($_SERVER['DOCUMENT_ROOT'] . '/.env');

    define('SQL_HOST', getenv('SQL_HOST'));
    define('SQL_DATABASE', getenv('SQL_DATABASE'));
    define('SQL_USER', getenv('SQL_USER'));
    define('SQL_PASSWORD', getenv('SQL_PASSWORD'));

// �����

// ���������� �� ����� ����� � ������ ����� � ������� languages
// ������������� �� ���������� ������ �� ����� ��� ������� � system/languages

// Domain (including http://), without "/" at the end

define ('HOST', 'http://localhost:63342/cs_fcst/');

// Working subdirectory ( Example: "/websitedirectory" ). Use "/" for root directory

define ('ROOT', '/');

// Personal emails

#define ('EMAIL', 'info@doeros.com');
define ('EMAIL', 'tng@gbg.bg');

// Include path delimiter ( use ";" for windows and ":" for linux )

define ('PATH_DELIMITER', ':');

// ���������� ���� ���� �� ��������

$_MAX_MENU_LEVEL[1] = 5;
$_MAX_MENU_LEVEL[2] = 1;

// Google Recaptcha keys

define("RECAPTCHA_SITEKEY", "6LcM9qwZAAAAAC4jpZfSwwE3UOu0WuV3YzldEZut");
define("RECAPTCHA_SECRET", "6LcM9qwZAAAAAG4zSkH8Xt0JTu6zOXqpEMfwJtBr");



?>