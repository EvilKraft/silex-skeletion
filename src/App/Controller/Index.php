<?php

namespace App\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;


class Index extends \App\Controller
{
    protected $AdminLTEPlugins = array(
        'dataTables' => false,

        'slimScroll' => false,
        'FastClick'  => false,
    );

    protected $template_name = '';

    protected $data  = array();
    protected $error = null;

    public function connect(Application $app)
    {
        $controllers = $app["controllers_factory"];

        $controllers->before(function (Request $request) use ($app) {
            // check for something here
        });



        $controllers->get("/",    [$this, 'indexAction']  )->bind('homepage');



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

            $this->initTwig($request);
            $response->setContent(
                $this->twig()->render($this->template_name.'.html.twig', $this->data)
            );
        });

        return $controllers;
    }


    public function indexAction(Request $request, Application $app)
    {
        self::$page_title = 'Main Page';

        $this->template_name = 'index';
        return '';
    }

    protected function initTwig(Request $request){
        $this->twig()->addGlobal('AdminLTEPlugins', $this->AdminLTEPlugins);

        parent::initTwig($request);
    }
}
