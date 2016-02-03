<?php

// Register route converters.
// Each converter needs to check if the $id it received is actually a value,
// as a workaround for https://github.com/silexphp/Silex/pull/768.
//$app['controllers']->convert('user', function ($id) use ($app) {
//    if ($id) {
//        return $app['repository.user']->find($id);
//    }
//});

// Register routes.

$app->mount("/", new App\Controller\Frontend($app));

$app->match('/login',          'App\Auth::loginAction')->bind('login');
$app->get('/logout',           'App\Auth::logoutAction')->bind('logout');
$app->match('/login_redirect', 'App\Auth::loginRedirectAction')->bind('login-redirect');



$app->mount('/'.$app['admin_dir'],            new App\Controller\Admin\Dashboard($app));
$app->mount('/'.$app['admin_dir'].'/users',   new App\Controller\Admin\User($app));

/*
$app->get('/'.$app['admin_dir'].'/users',                 'App\Controller\AdminUserController::indexAction' )->bind('admin_users');
$app->match('/'.$app['admin_dir'].'/users/add',           'App\Controller\AdminUserController::addAction'   )->bind('admin_user_add');
$app->match('/'.$app['admin_dir'].'/users/{user}/edit',   'App\Controller\AdminUserController::editAction'  )->bind('admin_user_edit');
$app->match('/'.$app['admin_dir'].'/users/{user}/delete', 'App\Controller\AdminUserController::deleteAction')->bind('admin_user_delete');
*/
