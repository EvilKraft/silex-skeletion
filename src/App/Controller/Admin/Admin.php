<?php
namespace App\Controller\Admin;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Base controller class
 */
abstract class Admin extends \App\Controller
{

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

    protected function getLeftMenuItems(Application $app, $RequestUri){
        $items = array();

//TODO

/*
        // get all routing objects
        $patterns = $app['routes']->getIterator(); // seems to be changed in Silex 1.1.0 !!! ... ->current()->all();

        // walk through the routing objects
        foreach ($patterns as $pattern) {
            $match = $pattern->getPattern();
            echo "$match<br />";
        }
*/


        $controllers = array(
            array('name' => 'admin_dashboard',     'title' => 'Dashboard',            'icon'  => 'fa fa-link'),

        //    array('name' => 'artists',   'title' => 'Artists',              'icon'  => 'fa fa-link'),
        //    array('name' => 'comments',  'title' => 'Comments',             'icon'  => 'fa fa-link'),
        //    array('name' => 'likes',     'title' => 'Likes',                'icon'  => 'fa fa-link'),

            array('name' => 'users',     'title' => 'Users',                'icon'  => 'fa fa-users'),
        );

        foreach($controllers as $controller){
            $items[] = array(
                'url'    => $app['url_generator']->generate($controller['name']),
                'title'  => $controller['title'],
                'active' => preg_match('/^'.$controller['name'].'.+$/', $RequestUri),
                'icon'   => $controller['icon'],
            );
        }

        //echo $RequestUri;



        return $items;
    }

    protected function initTwig(Request $request){
        $this->twig()->addGlobal('AdminLTEPlugins', $this->AdminLTEPlugins);
        $this->twig()->addGlobal('left_menu_items', $this->getLeftMenuItems($this->app, $request->getRequestUri()));

        //$this->twig()->addGlobal('loggedInUser', getUser());

        parent::initTwig($request);
    }
}