<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

require_once RESOURCES_PATH.'/config/'.$app['environment'].'.php';
require_once APP_PATH.'/providers.php';
require_once APP_PATH.'/routes.php';


$app->before(function (Request $request) use ($app) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }

    $app['translator']->setLocale($app['locale']);
    $app['translator']->addLoader('yaml', new Symfony\Component\Translation\Loader\YamlFileLoader());
    $translates = array(
        array('xliff', VENDOR_PATH.'/symfony/form/Resources/translations/validators.'.$app['locale'].'.xlf',      $app['locale'], 'validators'),
        array('xliff', VENDOR_PATH.'/symfony/validator/Resources/translations/validators.'.$app['locale'].'.xlf', $app['locale'], 'validators'),
        array('yaml',  RESOURCES_PATH.'/translations/validators.'.$app['locale'].'.yml',                          $app['locale'], 'validators'),
        array('yaml',  RESOURCES_PATH.'/translations/'.$app['locale'].'.yml',                                     $app['locale'], 'messages'),
    );
    foreach($translates as $translate){
        if(file_exists($translate[1])){
            call_user_func_array(array($app['translator'], "addResource"),$translate);
        }
    }
});

// do some security stuff
$app->after(function (Request $request, Response $response) {
    $response->headers->set('X-Frame-Options', 'DENY');             // deny show page in iframe
    $response->headers->set('X-Content-Type-Options', 'nosniff');   // for IE
    $response->headers->set('X-XSS-Protection', '1; mode=block;');  // for IE

    $response->headers->set('X-UA-Compatible', 'IE=edge');

//    $response->headers->set('X-Content-Security-Policy', 'allow \'self\';');    // for IE
//    $response->headers->set('X-WebKit-CSP', 'allow \'self\';');                 // for FF/Chrome
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
            $message = 'The requested page could not be found.';
            $body    = $app['twig']->render('404.twig', ['code' => $code, 'message' => $message]);

            break;
        default:
            $message = 'We are sorry, but something went terribly wrong.';
            $body    = $app['twig']->render('error.twig', ['code' => $code, 'message' => $message]);
    }

    return new Response($body, $code);
});

return $app;
