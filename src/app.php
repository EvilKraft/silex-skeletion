<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;


// Register service providers.
$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider());





//Setting Doctrine2 extensions
//http://stackoverflow.com/questions/10676242/using-doctrine2-sluggable-extension-with-silex
//http://silex-doctrine-extensions.readthedocs.org/en/latest/doctrine.html
//$app['db.event_manager']->addEventSubscriber(new \Gedmo\Timestampable\TimestampableListener());
$app['db.event_manager']->addEventSubscriber(new Gedmo\Tree\TreeListener());


$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());

$app->register(new Silex\Provider\TranslationServiceProvider());
//$app->register(new Silex\Provider\SwiftmailerServiceProvider());


$app->register(new Jenyak\I18nRouting\Provider\I18nRoutingServiceProvider());
$locales = $app['orm.em']->getRepository('\App\Entity\Languages')->getActiveLocales();
$app['locale'] = reset($locales);
$app['i18n_routing.locales'] = $locales;

$app->register(new Saxulum\DoctrineOrmManagerRegistry\Silex\Provider\DoctrineOrmManagerRegistryProvider());

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

// Token generator.
$app['user.tokenGenerator'] = $app->share(function($app) { return new \App\TokenGenerator($app['logger']); });

// Register repositories.
$app['user.manager'] = $app->share(function ($app) {return new App\Repository\UserRepository($app);});

$hierarhy = $app['orm.em']->getRepository('\App\Entity\Groups')->getRoleHierarchy();

//dump($hierarhy);

$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        // Any other URL requires auth.
        'index' => array(
            'pattern' => '^.*$',
            'form'      => array(
                'login_path'         => '/login',
                'check_path'         => '/login_check',
                'username_parameter' => 'username',
                'password_parameter' => 'password',

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

//    'security.role_hierarchy' => array(
//        'ROLE_ADMIN' => array('ROLE_USER'),
//    ),

    'security.access_rules' => array(
        array('^/login$',                  'IS_AUTHENTICATED_ANONYMOUSLY'),
        array('^/register',                'IS_AUTHENTICATED_ANONYMOUSLY'),
        //array('^/'.$app['admin_dir'].'.*', 'ROLE_ADMIN'),
        array('^/'.$app['admin_dir'].'.*', 'IS_AUTHENTICATED_ANONYMOUSLY'),

    //    array('^.*$', 'IS_AUTHENTICATED_FULLY'),
    ),
));

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.options' => array(
        'cache' => isset($app['twig.options.cache']) ? $app['twig.options.cache'] : false,
        'strict_variables' => true,
    ),
    'twig.form.templates' => array('form_div_layout.html.twig', 'common/form_div_layout.html.twig'),
    'twig.path' => array(__DIR__ . '/../app/views')
));

if($app['debug']){

    $app->register(new Silex\Provider\MonologServiceProvider(), array(
        'monolog.logfile' => __DIR__.'/../app/logs/development.log',
    ));


    $app->register(new Silex\Provider\HttpFragmentServiceProvider());

    $app->register(new Silex\Provider\WebProfilerServiceProvider(), array(
        'profiler.cache_dir' => __DIR__.'/../app/cache/profiler',
        'profiler.mount_prefix' => '/_profiler', // this is the default
    ));

    $app->register(new Silex\Provider\DebugServiceProvider(), array(
        'debug.max_items' => 250, // this is the default
        'debug.max_string_length' => -1, // this is the default
    ));

    $app->register(new Sorien\Provider\DoctrineProfilerServiceProvider());
}

$app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
    $types[] = new App\Form\LangtabsType();

    return $types;
}));


$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

// Protect admin urls.
$app->before(function (Request $request) use ($app) {
    /*
        $session = $request->getSession();
        $secured = unserialize($session->get('_security_secured'));
    */
});

//need to use put, delete, patch, options methods
Request::enableHttpMethodParameterOverride();

// Register ErrorHandlers
ErrorHandler::register();
ExceptionHandler::register($app['debug']);

// Register the error handler.
$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    switch ($code) {
        case 404:
            //$message = 'The requested page could not be found.';

            $loader = $app['dataloader'];
            $data = array(
                'global' => $loader->load('global'),
                'common' => $loader->load('common', $app['locale']),
                'header' => $loader->load('header', $app['locale']),
                'footer' => $loader->load('footer', $app['locale'])
            );

            return new Response( $app['twig']->render('404.html.twig', array( 'data' => $data )), 404);

            break;
        default:
            $message = 'We are sorry, but something went terribly wrong.';
    }

    return new Response($message, $code);
});

return $app;
