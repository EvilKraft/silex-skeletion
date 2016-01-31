<?php
namespace App;

use Silex\Application;
use Silex\ControllerProviderInterface;

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

    protected $page_title;
    protected $page_desc;
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
}