<?php
namespace App;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Base controller class
 */
abstract class Controller implements ControllerProviderInterface
{
    protected $app;

    private $em;
    private $twig;

    protected $tpl_path = '';
    protected $template;

    protected static $page_title;
    protected static $page_desc;

    protected $data  = array();
    protected $error;

    public function __construct(Application $app)
    {
        $this->app = $app;

    }

    protected function em(){
        if (is_null($this->em)) {
            $this->em = $this->app['orm.em'];
        }
        return $this->em;
    }

    /**
     * @return \Twig_Environment
     */
    protected function twig()
    {
        if (is_null($this->twig)) {
            $this->twig = $this->app['twig'];
        }
        return $this->twig;
    }

    /**
     * Returns raw, unparsed source of a file in View folder
     *
     * @param string $path Path to template file
     * @return string
     */
    protected function rawTemplate($path)
    {
        return $this->twig()->getLoader()->getSource($path);
    }

    protected function initTwig()
    {
        $this->twig()->addGlobal('page_title', static::$page_title);
        $this->twig()->addGlobal('page_desc', static::$page_desc);
    }

    protected function after(Request $request, Response $response){
        if($response->isRedirection()){ return; }

       // if ('application/json' === $request->headers->get('Accept')) {
       //     return $this->app()->json($this->data);
       // }

        if($request->isXmlHttpRequest()){
            $response_data = array('status' => 'OK', 'data' => $this->data);

            if(!is_null($this->error)){
                $response_data['status'] = 'ERROR';
                $response_data['error']  = $this->error;
            }

            return $this->app->json($response_data);
        }

        $this->initTwig();
        $response->setContent(
            $this->twig()->render($this->tpl_path.$this->template.'.twig', $this->data)
        );
    }

    public static function getTitle(){
        return static::$page_title;
    }
}