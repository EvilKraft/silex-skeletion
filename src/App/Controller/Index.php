<?php

namespace App\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Index
{
    public function indexAction(Request $request, Application $app)
    {

        //return 'hello';

        $my_dump = $app['request'];

        $data = array(
            'my_dump' => '<pre>'.print_r($my_dump, true).'</pre>'
        );
        return $app['twig']->render('index.html.twig', $data);

    }
}
