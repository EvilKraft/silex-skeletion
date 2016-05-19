<?php


// Register service providers.
$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider());

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());

$app->register(new Silex\Provider\SwiftmailerServiceProvider(), array(
    'swiftmailer.options' => $app['mailer.options'],
));

$locales = $app['orm.em']->getRepository('\App\Entity\Languages')->getActiveLocales();
$app['locale'] = reset($locales);
$app['i18n_routing.locales'] = $locales;
$app->register(new Silex\Provider\TranslationServiceProvider(), array());
$app->register(new Jenyak\I18nRouting\Provider\I18nRoutingServiceProvider());

$app->register(new Saxulum\DoctrineOrmManagerRegistry\Silex\Provider\DoctrineOrmManagerRegistryProvider());
$app->register(new Silex\Provider\ServiceControllerServiceProvider());

//Setting Doctrine2 extensions
//http://stackoverflow.com/questions/10676242/using-doctrine2-sluggable-extension-with-silex
//http://silex-doctrine-extensions.readthedocs.org/en/latest/doctrine.html
//$app['db.event_manager']->addEventSubscriber(new \Gedmo\Timestampable\TimestampableListener());
$app['db.event_manager']->addEventSubscriber(new Gedmo\Tree\TreeListener());

// Token generator.
$app['user.tokenGenerator'] = $app->share(function($app) { return new \App\TokenGenerator($app['logger']); });
// Register repositories.
$app['user.manager'] = $app->share(function ($app) {return new App\Repository\UserRepository($app);});

$hierarhy = $app['orm.em']->getRepository('\App\Entity\Groups')->getRoleHierarchy();
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        // Any other URL requires auth.
        'index' => array(
            'pattern' => '^.*$',
            'form'      => array(
                'login_path'                     => '/login',
                'check_path'                     => '/login_check',
                'username_parameter'             => 'username',
                'password_parameter'             => 'password',
                'default_target_path'            => '/login_redirect',
                'always_use_default_target_path' => true
            ),
            'anonymous' => true,
            'logout'    => array('logout_path' => '/logout'),
            'users'     => $app->share(function() use ($app) {
                return $app['user.manager'];
            }),
        ),
    ),
    'security.role_hierarchy' => $hierarhy,
    'security.access_rules' => array(
        array('^/login$',                  'IS_AUTHENTICATED_ANONYMOUSLY'),
        array('^/register',                'IS_AUTHENTICATED_ANONYMOUSLY'),
        array('^/'.$app['admin_dir'].'.*', 'ROLE_ADMIN'),
        //array('^/'.$app['admin_dir'].'.*', 'IS_AUTHENTICATED_ANONYMOUSLY'),
        //    array('^.*$', 'IS_AUTHENTICATED_FULLY'),
    ),
));

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.options' => array(
        'cache' => CACHE_PATH.'/twig',
        'strict_variables' => true,
    ),
    'twig.form.templates' => array('form_div_layout.html.twig', 'common/form_div_layout.html.twig'),
    'twig.path' => array(RESOURCES_PATH.'/views')
));

$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => RESOURCES_PATH.'/logs/development.log',
    'monolog.level'   => 300 // = Logger::WARNING
));
$app->register(new App\Provider\ExtendedMonologServiceProvider());


if($app['debug']){
    $app->register(new Silex\Provider\HttpFragmentServiceProvider());
    $app->register(new Silex\Provider\WebProfilerServiceProvider(), array(
        'profiler.cache_dir' => CACHE_PATH.'/profiler',
        'profiler.mount_prefix' => '/_profiler', // this is the default
    ));
    $app->register(new Silex\Provider\DebugServiceProvider(), array(
        'debug.max_items' => 250, // this is the default
        'debug.max_string_length' => -1, // this is the default
    ));

    $app->register(new Sorien\Provider\DoctrineProfilerServiceProvider());
}else{
    $app->register(new Silex\Provider\HttpCacheServiceProvider(), array(
        'http_cache.cache_dir' => CACHE_PATH.'/http_cache/',
    ));
}



$app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
    $types[] = new App\Form\Types\LangtabsType();

    return $types;
}));