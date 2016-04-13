<?php
namespace App\Controller\Admin;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 * Base controller class
 */
abstract class Admin extends \App\Controller
{
    protected static $entity;

    protected $cancel_route;

    protected static $form;
    protected static $icon_class = 'fa fa-circle-o';

    protected static $roles;

    protected $tpl_path = 'admin/';

    protected $AdminLTEPlugins = array(
        'dataTables' => false,
        'select2'    => false,

        'slimScroll' => false,
        'FastClick'  => false,
    );

    protected $sortTable  = true;
    protected $showFields = array();
    protected $actions    = array('edit', 'delete');

    public function __construct(Application $app)
    {
        parent::__construct($app);

        if(is_null(static::$entity)){
            throw new \Exception('Entity was not set.');
        }
        if(is_null(static::$form)){
            throw new \Exception('Form was not set.');
        }
        if(!is_array(static::$roles)){
            throw new \Exception('Roles was not set.');
        }
    }


    public static function getIcon(){
        return static::$icon_class;
    }

    public static function getRoles(){
        return static::$roles;
    }

    protected function getAdminControllers(){
        $current_route     = $this->app['request']->get("_route");
        $current_route_url = $this->app['request']->getRequestUri();

        $items = array();
        foreach($this->app['routes'] as $key => $route) {
            if(is_array($route->getDefault('_controller'))){
                $controller = $route->getDefault('_controller')[0];

                if(is_object($controller)){
                    if(preg_match('/'.addslashes( __NAMESPACE__).'\\\(.*)$/', get_class($controller), $matches)) {
                        if (!array_key_exists($matches[1], $items)) {
                            $items[$matches[1]] = array(
                                'title'  => $controller::getTitle(),
                                'icon'   => $controller::getIcon(),
                                'url'    => $this->app->path($key),
                                'active' => ($key == $current_route),
                                'roles' => $controller::getRoles(),
                            );
                        }
                    }
                }
            }
        }

        return $items;
    }

    protected function getLeftMenuItems(){
        $items = $this->getAdminControllers();

        $items['Reports'] = array(
            'title'  => 'Reports',
            'icon'   => 'fa fa-line-chart',
            'url'    => '#',
            'active' => false,
            'children' => array(
                array('title' => 'Report 1', 'icon' => 'fa fa-circle-o', 'url' => '#', 'active' => false),
                array('title' => 'Report 2', 'icon' => 'fa fa-circle-o', 'url' => '#', 'active' => false),
                array('title' => 'Report 3', 'icon' => 'fa fa-circle-o', 'url' => '#', 'active' => false),
            )
        );

        foreach($items as $controllerName => $item) {
            $role = ($controllerName == 'Dashboard') ? 'ROLE_ADMIN' : 'ROLE_'.strtoupper($controllerName).'_VIEW';

            if(!$this->app['security.authorization_checker']->isGranted($role)){
                unset($items[$controllerName]);
            }
        }

        return $items;
    }


    protected function initTwig(){
        $this->twig()->addGlobal('AdminLTEPlugins', $this->AdminLTEPlugins);
        $this->twig()->addGlobal('left_menu_items', $this->getLeftMenuItems());

        $this->twig()->addGlobal('cancel_route', $this->cancel_route);

        $logged_user = $this->app['security.token_storage']->getToken()->getUser();
        $this->twig()->addGlobal('logged_user', $logged_user);

        parent::initTwig();
    }

     protected function isGranted($role){
        if(!$this->app['security.authorization_checker']->isGranted($role)) {
            throw new AccessDeniedException();
        }
    }
}