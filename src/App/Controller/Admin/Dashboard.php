<?php

namespace App\Controller\Admin;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class Dashboard extends Admin
{
    protected static $entity = '';
    protected static $form   = '';

    protected $template     = 'dashboard';

    protected static $page_title = 'Dashboard';
    protected static $page_desc  = '';
    protected static $icon_class = 'fa fa-dashboard';

    protected static $roles = array(
        'ROLE_ADMIN' => 'Can login Admin Pannel',
    );

    protected $data = array();

    public function connect(Application $app)
    {
        $controllers = $app["controllers_factory"];

        $controllers->before(function (Request $request) use ($app) {
            // check for something here
        });

        $controllers->get("/",          [$this, 'indexAction']  )->bind('admin_dashboard');

        $controllers->after(function (Request $request, Response $response) use ($app) {
            $this->after($request, $response);
        });

        return $controllers;
    }


    public function indexAction(Request $request, Application $app){
        $this->data['orders']   = 150;
        $this->data['users']    = $app['user.manager']->getCount();
        $this->data['rate']     = 53;
        $this->data['visitors'] = 65;

        return '';
    }

}