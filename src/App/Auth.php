<?php

namespace App;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;


class Auth
{
    public function loginAction(Request $request, Application $app)
    {
        if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
                return $this->loginRedirectAction($request, $app);
        }

        return $app['twig']->render('login.twig', array(
            'page_title' => 'Log in',

            'error'         => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
        ));
    }
    public function logoutAction(Request $request, Application $app)
    {
        $app['session']->clear();
        return $app->redirect($app->path('login'));
    }

    public function loginRedirectAction(Request $request, Application $app){
        if ($app['security.authorization_checker']->isGranted('ROLE_ADMIN')) {
            return $app->redirect($app->path('admin_dashboard'));
        }

        if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $app->redirect($app->path('homepage'));
        }

        return $app->redirect($app->path('login'));
    }
}
