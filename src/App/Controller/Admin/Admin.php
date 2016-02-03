<?php
namespace App\Controller\Admin;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;



/**
 * Base controller class
 */
abstract class Admin extends \App\Controller
{
    protected static $entity;
    protected static $icon_class = 'fa fa-link';

    protected $tpl_path = 'admin/';

    protected $AdminLTEPlugins = array(
        'dataTables' => false,

        'slimScroll' => false,
        'FastClick'  => false,
    );

    public function __construct(Application $app)
    {
        parent::__construct($app);

        if(is_null(static::$entity)){
            throw new \Exception('Entity was not set.');
        }
    }


    public static function getIcon(){
        return static::$icon_class;
    }


    protected function getLeftMenuItems(){

        $current_route     = $this->app['request']->get("_route");
        $current_route_url = $this->app['request']->getRequestUri();

        $items = array();
        foreach($this->app['routes'] as $key => $route) {
            $controller = $route->getDefault('_controller')[0];

            if(is_object($controller)){
                if(preg_match('/App\\\Controller\\\Admin\\\(.*)$/', get_class($controller), $matches)) {
                    if (!array_key_exists($matches[1], $items)) {
                        $items[$matches[1]] = array(
                            'route'  => $key,
                            'title'  => $controller::getTitle(),
                            'icon'   => $controller::getIcon(),
                            'url'    => $this->app['url_generator']->generate($key),
                            'active' => ($key == $current_route),
                        );
                    }
                }
            }
        }

        return $items;
    }


    protected function initTwig(){
        $this->twig()->addGlobal('AdminLTEPlugins', $this->AdminLTEPlugins);
        $this->twig()->addGlobal('left_menu_items', $this->getLeftMenuItems());

        $logged_user = $this->app['security.token_storage']->getToken()->getUser();
        $this->twig()->addGlobal('logged_user', $logged_user);

        parent::initTwig();
    }
}