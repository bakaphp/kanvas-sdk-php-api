<?php

use Dotenv\Dotenv;
use function Canvas\Core\appPath;
use Phalcon\Loader;
use Kanvas\Sdk\Kanvas;

// Register the auto loader
require '/canvas-core/src/Core/functions.php';
//require dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . '/vendor/canvas/core/src/Core/functions.php';

// Composer Autoloader.
require appPath('vendor/autoload.php');

//development loads
$loader = new Loader();
$namespaces = [
    'Canvas' => '/canvas-core/src',
    'Kanvas\Sdk' => '/canvas-sdk-php/src',
    'Baka\Auth' => '/baka/auth/src',
    'Baka\Database' => '/baka/database/src',
    'Baka\Elasticsearch' => '/baka/elasticsearch/src',
    'Baka\Http' => '/baka/http/src',
    'Phalcon\Cashier' => '/baka/cashier/src',
    'Baka\Mail' => '/baka/mail/src',
    'Baka\Blameable' => '/baka/blameable/src',
    'Baka\Support' => '/baka/support/src',
    'Baka\Router' => '/baka/router/src',

    'Gewaer' => appPath('/library'),
    'Gewaer\Api\Controllers' => appPath('/api/controllers'),
    'Gewaer\Cli\Tasks' => appPath('/cli/tasks'),
    'Niden\Tests' => appPath('/tests'),
    'Gewaer\Tests' => appPath('/tests')
];
$loader->registerNamespaces($namespaces);

$loader->register();

// Load environment
(new Dotenv(Canvas\Core\appPath()))->overload();

/**
 * @todo check if this goes here
 */
Kanvas::setApiKey(getenv('GEWAER_APP_ID'));

