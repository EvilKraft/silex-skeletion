<?php

namespace App\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Frontend extends \App\Controller
{

    public function connect(Application $app)
    {
        $controllers = $app["controllers_factory"];

        $controllers->before(function (Request $request) use ($app) {
            // check for something here
        });

        $controllers->get("/",        [$this, 'indexAction']    )->bind('homepage');
        $controllers->get("/about",   [$this, 'aboutAction']    )->bind('about_page');
        $controllers->get("/contact", [$this, 'contactAction']  )->bind('contact_page');
        $controllers->get("/test",    [$this, 'testAction']     )->bind('test_page');

        $controllers->after(array($this, 'after'));

        return $controllers;
    }


    public function indexAction(Request $request, Application $app)
    {
        self::$page_title = 'Main Page';

        $this->template = 'index';
        return '';
    }

    public function aboutAction(Request $request, Application $app)
    {
        self::$page_title = 'About Us';

        $this->template = 'about';

        return '';
    }

    public function contactAction(Request $request, Application $app)
    {
        self::$page_title = 'Contact';

        $this->template = 'contact';
        return '';
    }

    public function testAction(Request $request, Application $app)
    {
        self::$page_title = 'Test Page';

        $this->template = 'index';

        return new Response('', 200, array('Cache-Control' => 's-maxage=3600, public'));
    }

}
