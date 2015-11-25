<?php

// Register route converters.
// Each converter needs to check if the $id it received is actually a value,
// as a workaround for https://github.com/silexphp/Silex/pull/768.
$app['controllers']->convert('user', function ($id) use ($app) {
    if ($id) {
        return $app['repository.user']->find($id);
    }
});

// Register routes.
$app->get('/', 'App\Controller\IndexController::indexAction')
    ->bind('homepage');

$app->get('/me', 'App\Controller\UserController::meAction')
    ->bind('me');
$app->match('/login', 'App\Controller\UserController::loginAction')
    ->bind('login');
$app->get('/logout', 'App\Controller\UserController::logoutAction')
    ->bind('logout');


$app->get('/admin', 'App\Controller\AdminController::indexAction')
    ->bind('admin');

$app->mount("/users", new App\Controller\Admin\User($app));

/*
$app->get('/admin/users', 'App\Controller\AdminUserController::indexAction')
    ->bind('admin_users');
$app->match('/admin/users/add', 'App\Controller\AdminUserController::addAction')
    ->bind('admin_user_add');
$app->match('/admin/users/{user}/edit', 'App\Controller\AdminUserController::editAction')
    ->bind('admin_user_edit');
$app->match('/admin/users/{user}/delete', 'App\Controller\AdminUserController::deleteAction')
    ->bind('admin_user_delete');
*/
