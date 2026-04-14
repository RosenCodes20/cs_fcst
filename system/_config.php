	<?php

// !important - Other configs:
// - .htaccess

define ('SQL_HOST', 'localhost');
define ('SQL_DATABASE', 'cstu');
define ('SQL_USER', 'root');
define ('SQL_PASSWORD', '');

// �����

// ���������� �� ����� ����� � ������ ����� � ������� languages
// ������������� �� ���������� ������ �� ����� ��� ������� � system/languages

// Domain (including http://), without "/" at the end

define ('HOST', 'http://localhost:63342/csft_project/');

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