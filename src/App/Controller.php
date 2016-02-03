<?php
namespace App;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Base controller class
 */
abstract class Controller implements ControllerProviderInterface
{
    protected $app;

    private $em;
    private $twig;

    protected static $entity;

    protected $template;

    protected static $page_title;
    protected static $page_desc;
    protected $content;

    public function __construct(Application $app)
    {
        $this->app = $app;

        if(is_null(static::$entity)){
            throw new \Exception('Entity was not set.');
        }
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

    protected function initTwig(Request $request)
    {
        $this->twig()->addGlobal('page_title', static::$page_title);
        $this->twig()->addGlobal('page_desc', static::$page_desc);

        $user = $this->app['security.token_storage']->getToken()->getUser();

        $this->twig()->addGlobal('logged_user', $user);
    }
}