<?php

namespace App\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Index
{
    public function indexAction(Request $request, Application $app)
    {

        return 'hello';

        //$data = array( );
        //return $app['twig']->render('index.html.twig', $data);

    }
}
