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

// Now, the hard part, handle fatal error.
$handler = ExceptionHandler::register($app['debug']);
$handler->setHandler(function ($exception, $x) use ($app) {

    // Create an ExceptionEvent with all the informations needed.
    $event = new Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent(
        $app,
        $app['request'],
        Symfony\Component\HttpKernel\HttpKernelInterface::MASTER_REQUEST,
        $exception
    );

    // Hey Silex ! We have something for you, can you handle it with your exception handler ?
    $app['dispatcher']->dispatch(Symfony\Component\HttpKernel\KernelEvents::EXCEPTION, $event);

    // And now, just display the response ;)
    $response = $event->getResponse();
    $response->sendHeaders();
    $response->sendContent();
    // $response->send(); //We can't do that, something happened with the buffer, and Symfony still return its HTML.

    $app->terminate($app['request'], $response);
});

// Register the error handler.
$app->error(function (\Exception $e, $code) use ($app) {

    if ($app['debug']) {
        return;
    }

    $template = 'error';

    switch ($code) {
        case 403:
            $message = 'You are unauthorized to perform this action.';
            break;

        case 404:
            $message = 'The requested page could not be found.';
            $template = '404';
            break;

        case 405:
            $message = 'Method is not allowed.';
            break;

        default:
            $message = 'We are sorry, but something went terribly wrong.';
    }


    $app['request']->get('_controller')[0]->setTemplate($template);
    $app['request']->get('_controller')[0]->setData(['code' => $code, 'message' => $message]);
    $app['request']->get('_controller')[0]->setError($message);

    return new Response('', $code);
});

return $app;
