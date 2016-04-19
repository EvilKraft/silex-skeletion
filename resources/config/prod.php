<?php

$app['site_title']      = 'My Silex Site';

// Emails.
$app['admin_email'] = 'noreply@silex.nothing';
$app['site_email']  = 'noreply@silex.nothing';

// Doctrine (db)
$app['db.options'] = array(
    'driver'   => 'pdo_mysql',
    'host'     => '127.0.0.1',
    'port'     => '3306',
    'dbname'   => 'silex_db',
    'user'     => 'root',
    'password' => '',
    'charset'  => 'utf8',
);

// SwiftMailer
$app['swiftmailer.options'] = array(
    'host' => 'host',
    'port' => '25',
    'username' => 'username',
    'password' => 'password',
    'encryption' => null,
    'auth_mode' => null
);

// Timezone.
date_default_timezone_set('Europe/Paris');

define("ROOT_PATH",         realpath(__DIR__ . "/../../"));             // Root directory
define("WEB_PATH",          realpath(ROOT_PATH . "/web/"));             // Web directory
define("UPLOADS_PATH",      realpath(WEB_PATH . "/uploads/"));          // Uploads
define("RESOURCES_PATH",    realpath(ROOT_PATH . "/resources/"));       // Resources
define("CACHE_PATH",        realpath(RESOURCES_PATH . "/cache/"));      // Cache
define("TWIG_CACHE_PATH",   realpath(CACHE_PATH.'/twig/'));             // Twig cache

// Administration Pannel
$app['admin_dir']        = 'admin';
$app['admin_tpl_skin']   = 'skin-blue';      // Possible values: skin-blue, skin-black, skin-purple, skin-yellow, skin-red, skin-green
$app['admin_tpl_layout'] = 'sidebar-mini';   // Possible values: fixed, layout-boxed, layout-top-nav, sidebar-collapse, sidebar-mini


$app['orm.proxies_dir'] = CACHE_PATH.'/doctrine/proxies';
$app['orm.auto_generate_proxies'] = $app['debug'];  //php vendor/bin/doctrine orm:generate-proxies //for manually generation
$app['orm.default_cache'] = array(
    'driver' => 'filesystem',
    'path' => CACHE_PATH.'/doctrine/cache',
);
$app['orm.em.options'] = array(
    'mappings' => array(
        array(
            "type" => "annotation",
            "namespace" => 'App\Entity',
            "path" => ROOT_PATH.'/src/App/Entity',
            "use_simple_annotation_reader" => false,
        ),
    ),
);