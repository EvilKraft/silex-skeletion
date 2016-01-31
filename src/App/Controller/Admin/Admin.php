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

    protected function initTwig(Application $app, Request $request){

        $app['twig']->addGlobal('AdminLTEPlugins', $this->AdminLTEPlugins);
        $app['twig']->addGlobal('left_menu_items', $this->getLeftMenuItems($app, $request->getRequestUri()));

        //$app['twig']->addGlobal('loggedInUser', getUser());

        $app['twig']->addGlobal('page_title', $this->page_title);
        $app['twig']->addGlobal('page_desc', $this->page_desc);
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
            array('name' => 'admin',     'title' => 'Dashboard',            'icon'  => 'fa fa-link'),

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
}