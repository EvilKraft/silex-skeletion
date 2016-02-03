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

        return $app['twig']->render('login.html.twig', array(
            'page_title' => 'Log in',

            'error'         => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
        ));
    }
    public function logoutAction(Request $request, Application $app)
    {
        $app['session']->clear();
        return $app->redirect($app['url_generator']->generate('login'));
    }

    public function loginRedirectAction(Request $request, Application $app){
        if ($app['security.authorization_checker']->isGranted('ROLE_ADMIN')) {
            return $app->redirect($app['url_generator']->generate('admin_users'));
        }

        if ($app['security.authorization_checker']->isGranted('ROLE_CALL_OPERATOR')) {
            return $app->redirect($app['url_generator']->generate('orders'));
        }

        if ($app['security.authorization_checker']->isGranted('ROLE_OFFICE_USER')) {
            return $app->redirect($app['url_generator']->generate('calls'));
        }

        if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $app->redirect($app['url_generator']->generate('homepage'));
        }

        return $app->redirect($app['url_generator']->generate('login'));
    }
}
