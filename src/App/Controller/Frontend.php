<?php

namespace App\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Frontend extends \App\Controller
{
    protected $AdminLTEPlugins = array(
        'dataTables' => false,

        'slimScroll' => false,
        'FastClick'  => false,
    );

    protected $data  = array();
    protected $error = null;

    public function connect(Application $app)
    {
        $controllers = $app["controllers_factory"];

        $controllers->before(function (Request $request) use ($app) {
            // check for something here
        });



        $controllers->get("/",     [$this, 'indexAction']  )->bind('homepage');
        $controllers->get("/test", [$this, 'testAction']  )->bind('test_page');



        $controllers->after(function (Request $request, Response $response) use ($app) {
            if($response->isRedirection()){ return; }

            if($request->isXmlHttpRequest()){
                $response_error = $this->error;

                $response_data = array('status' => 'OK', 'data' => $this->data);

                if(!is_null($response_error)){
                    $response_data['status'] = 'ERROR';
                    $response_data['error']  = $response_error;
                }

                return $app->json($response_data);
            }

            $this->initTwig();
            $response->setContent(
                $this->twig()->render($this->template.'.twig', $this->data)
            );
        });

        $controllers->after(array($this, 'after'));

        return $controllers;
    }


    public function indexAction(Request $request, Application $app)
    {
        self::$page_title = 'Main Page';

        $this->template = 'index';
        return '';
    }

    public function testAction(Request $request, Application $app)
    {
        self::$page_title = 'Test Page';

        $this->template = 'index';
        return '';
    }

    protected function initTwig(){
        $this->twig()->addGlobal('AdminLTEPlugins', $this->AdminLTEPlugins);

        parent::initTwig();
    }
}
