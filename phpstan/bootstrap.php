<?php

$rootDir = getenv('_PS_ROOT_DIR_');
if (!$rootDir) {
    echo '[ERROR] Define _PS_ROOT_DIR_ with the path to PrestaShop folder' . PHP_EOL;
    exit(1);
}

// This file will be in the directory vendor/prestashop/php-dev-tools/phpstan.
$pathToModuleRoot = __DIR__ . '/../../../../';

// Add module composer autoloader
require_once $pathToModuleRoot . 'vendor/autoload.php';

// Add PrestaShop composer autoload
define('_PS_ADMIN_DIR_', $rootDir . '/admin-dev/');
define('PS_ADMIN_DIR', _PS_ADMIN_DIR_);

require_once $rootDir . '/config/defines.inc.php';
require_once $rootDir . '/config/autoload.php';
require_once $rootDir . '/config/bootstrap.php';

// Make sure loader php-parser is coming from php stan composer

// 1- Use module vendors
$loader = new \Composer\Autoload\ClassLoader();
$loader->setPsr4('PhpParser\\', ['vendor/nikic/php-parser/lib/PhpParser']);
$loader->register(true);
// 2- Use with Docker container
$loader = new \Composer\Autoload\ClassLoader();
$loader->setPsr4('PhpParser\\', ['/composer/vendor/nikic/php-parser/lib/PhpParser']);
$loader->register(true);
// 3- Use with PHPStan phar
$loader = new \Composer\Autoload\ClassLoader();
// Contains the vendor in phar, like "phar://phpstan.phar/vendor"
$loader->setPsr4('PhpParser\\', ['phar://' . dirname($_SERVER['PATH_TRANSLATED']) . '/../phpstan/phpstan-shim/phpstan.phar/vendor/nikic/php-parser/lib/PhpParser/']);
$loader->register(true);
// 4- Use phpstan phar with sym link
$loader = new \Composer\Autoload\ClassLoader();
$loader->setPsr4('PhpParser\\', ['phar://' . realpath($_SERVER['PATH_TRANSLATED']) . '/vendor/nikic/php-parser/lib/PhpParser/']);
$loader->register(true);

// We must declare these constant in this boostrap script.
// Ignoring the error partern with this value will throw another error if not found
// during the checks.
$constantsToDefine = [
  '_DB_SERVER_' => 'string',
  '_DB_NAME_' => 'string',
  '_DB_USER_' => 'string',
  '_DB_PASSWD_' => 'string',
  '_MYSQL_ENGINE_' => 'string',
  '_COOKIE_KEY_' => 'string',
  '_COOKIE_IV_' => 'string',
  '_DB_PREFIX_' => 'string',
  '_PS_SSL_PORT_' => 'int',
  '_THEME_NAME_' => 'string',
  '_THEME_COL_DIR_' => 'string',
  '_PARENT_THEME_NAME_' => 'string',
  '__PS_BASE_URI__' => 'string',
  '_PS_PRICE_DISPLAY_PRECISION_' => 'int',
  '_PS_PRICE_COMPUTE_PRECISION_' => 'string',
  '_PS_OS_CHEQUE_' => 'int',
  '_PS_OS_PAYMENT_' => 'int',
  '_PS_OS_PREPARATION_' => 'int',
  '_PS_OS_SHIPPING_' => 'int',
  '_PS_OS_DELIVERED_' => 'int',
  '_PS_OS_CANCELED_' => 'int',
  '_PS_OS_REFUND_' => 'int',
  '_PS_OS_ERROR_' => 'int',
  '_PS_OS_OUTOFSTOCK_' => 'int',
  '_PS_OS_OUTOFSTOCK_PAID_' => 'int',
  '_PS_OS_OUTOFSTOCK_UNPAID_' => 'int',
  '_PS_OS_BANKWIRE_' => 'int',
  '_PS_OS_PAYPAL_' => 'int',
  '_PS_OS_WS_PAYMENT_' => 'int',
  '_PS_OS_COD_VALIDATION_' => 'int',
  '_PS_THEME_DIR_' => 'string',
  '_PS_BASE_URL_' => 'string',
  '_MODULE_DIR_' => 'string'
];

foreach ($constantsToDefine as $key => $value) {
    if (!defined($value)) {
        switch ($value) {
            case 'string':
                define($key, 'DUMMY_VALUE');
            break;
            case 'int':
                define($key, 1);
            break;
            default:
                define($key, 'DUMMY_VALUE');
            break;
        }
    }
}

