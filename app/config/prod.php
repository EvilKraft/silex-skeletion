<?php

// Timezone.
date_default_timezone_set('Europe/Paris');

// Cache
$app['cache.path'] = __DIR__ . '/../cache';

// Twig cache
$app['twig.options.cache'] = $app['cache.path'] . '/twig';

// Emails.
$app['admin_email'] = 'noreply@silex.nothing';
$app['site_email']  = 'noreply@silex.nothing';

$app['site_title']      = 'My Silex Site';

$app['admin_dir']        = 'admin';
$app['admin_tpl_skin']   = 'skin-blue';      // Possible values: skin-blue, skin-black, skin-purple, skin-yellow, skin-red, skin-green
$app['admin_tpl_layout'] = 'sidebar-mini';   // Possible values: fixed, layout-boxed, layout-top-nav, sidebar-collapse, sidebar-mini

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

$app['orm.proxies_dir'] = $app['cache.path'].'/doctrine/proxies';
//$app['orm.default_cache'] = 'array';
$app['orm.default_cache'] = array(
    'driver' => 'filesystem',
    'path' => $app['cache.path'].'/doctrine/cache',
);
$app['orm.em.options'] = array(
    'mappings' => array(
        array(
            "type" => "annotation",
            "namespace" => 'App\Entity',
            "path" => __DIR__."/../../src/App/Entity",
            "use_simple_annotation_reader" => false,
        ),
    ),

);

// SwiftMailer
// See http://silex.sensiolabs.org/doc/providers/swiftmailer.html
$app['swiftmailer.options'] = array(
    'host' => 'host',
    'port' => '25',
    'username' => 'username',
    'password' => 'password',
    'encryption' => null,
    'auth_mode' => null
);


