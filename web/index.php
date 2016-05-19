<?php

// Path definitions
define("ROOT_PATH",         realpath(__DIR__ . "/../"));                // Root directory
define("WEB_PATH",          realpath(__DIR__));                         // Web directory
define("UPLOADS_PATH",      realpath(WEB_PATH . "/uploads/"));          // Uploads
define("RESOURCES_PATH",    realpath(ROOT_PATH . "/resources/"));       // Resources
define("CACHE_PATH",        realpath(RESOURCES_PATH . "/cache/"));      // Cache
define("APP_PATH",          realpath(ROOT_PATH . "/src/"));             // Aplication
define("VENDOR_PATH",       realpath(ROOT_PATH . "/vendor/"));          // Vendor

$loader = require_once VENDOR_PATH.'/autoload.php';
//$loader->add('Acme', __DIR__.'/../src/');
//$loader->add('Gedmo', __DIR__.'/../vendor/gedmo/doctrine-extensions/lib/');
Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

class Application extends Silex\Application
{
    use Silex\Application\UrlGeneratorTrait;
    use Silex\Application\TranslationTrait;
}

$app = new Application();

$app['environment'] = 'dev';
$app['environment'] = 'prod';

require_once APP_PATH.'/application.php';

if ($app['debug']) {
    $app->run();
}else{
    $app['http_cache']->run();
}