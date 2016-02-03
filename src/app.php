<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

// Register ErrorHandlers
ErrorHandler::register();
ExceptionHandler::register($app['debug']);

// Register service providers.
$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());

$app->register(new Silex\Provider\TranslationServiceProvider());
//$app->register(new Silex\Provider\SwiftmailerServiceProvider());


$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        // Any other URL requires auth.
        'index' => array(
            //'security' => $app['debug'] ? false : true,

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
                //return new App\UserProvider($app);
                return new App\Repository\UserRepository($app['db'], $app['security.encoder.digest']);
            }),

        ),
    ),
    'security.role_hierarchy' => array(
        'ROLE_ADMIN' => array('ROLE_USER'),
    ),
    'security.access_rules' => array(
        array('^/login$', 'IS_AUTHENTICATED_ANONYMOUSLY'),
        array('^/admin', 'ROLE_ADMIN'),
        array('^.*$', 'IS_AUTHENTICATED_FULLY'),
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


$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/development.log',
));

// Register repositories.
$app['repository.user'] = $app->share(function ($app) {
    return new App\Repository\UserRepository($app['db'], $app['security.encoder.digest']);
});


// Protect admin urls.
$app->before(function (Request $request) use ($app) {
//    $protected = array(
//        '/admin/' => 'ROLE_ADMIN',
//        '/me' => 'ROLE_USER',
//    );
//    $path = $request->getPathInfo();
//    foreach ($protected as $protectedPath => $role) {
//        if (strpos($path, $protectedPath) !== FALSE && !$app['security']->isGranted($role)) {
//            throw new AccessDeniedException();
//        }
//    }

    /*
        $token = $app['security.token_storage']->getToken();

        if (null !== $token) {
            $user = $token->getUser();

            $my_dump = $user; var_dump($my_dump); echo '<pre>'.print_r($my_dump, true).'</pre>';

            // Get list of roles for current user
            $roles = $token->getRoles();
            // Tranform this list in array
            $rolesTab = array_map(function($role){
                return $role->getRole();
            }, $roles);

            $my_dump = $rolesTab; var_dump($my_dump); echo '<pre>'.print_r($my_dump, true).'</pre>';
        }

        if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_ANONYMOUSLY')) {
            echo 'IS_AUTHENTICATED_ANONYMOUSLY'.'<br>';
        }
        if ($app['security.authorization_checker']->isGranted('ROLE_ADMIN')) {
            echo 'ROLE_ADMIN'.'<br>';
        }
        if ($app['security.authorization_checker']->isGranted('ROLE_CALL_OPERATOR')) {
            echo 'ROLE_CALL_OPERATOR'.'<br>';
        }
        if ($app['security.authorization_checker']->isGranted('ROLE_OFFICE_USER')) {
            echo 'ROLE_OFFICE_USER'.'<br>';
        }
        if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
            echo 'IS_AUTHENTICATED_FULLY'.'<br>';
        }
        if ($app['security.authorization_checker']->isGranted('ROLE_USER')) {
            echo 'ROLE_USER'.'<br>';
        }


        $session = $request->getSession();
        $secured = unserialize($session->get('_security_secured'));
        $my_dump = $secured; var_dump($my_dump); echo '<pre>'.print_r($my_dump, true).'</pre>';
    */
});

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
