<?php

namespace App\Controller\Admin;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class Security extends Admin
{

    protected static $entity = '\App\Entity\Groups';
    protected static $form   = '\App\Form\GroupType';

    protected $template     = 'groups';
    protected $cancel_route = 'admin_security';

    protected static $page_title = 'Security';
    protected static $page_desc  = '';
    protected static $icon_class = 'fa fa-shield';

    protected static $roles = array(
        'ROLE_SECURITY_VIEW'   => 'View',
        'ROLE_SECURITY_CREATE' => 'Create',
        'ROLE_SECURITY_UPDATE' => 'Update',
        'ROLE_SECURITY_DELETE' => 'Delete',
    );

    protected $sortTable  = false;
//    protected $showFields = array('id', 'getLaveledTitle', 'alias');
    protected $actions    = array();

    public function connect(Application $app)
    {
        $self = $this;
        $checkView   = function () use ($self){$self->isGranted('ROLE_SECURITY_VIEW');};
        $checkCreate = function () use ($self){$self->isGranted('ROLE_SECURITY_CREATE');};
        $checkUpdate = function () use ($self){$self->isGranted('ROLE_SECURITY_UPDATE');};
        $checkDelete = function () use ($self){$self->isGranted('ROLE_SECURITY_DELETE');};


        $controllers = $app["controllers_factory"];

        $controllers->before($checkView);

        $controllers->get("/",                           [$this, 'index']                  )->bind('admin_security');

        $controllers->get("/groups/",                    [$this, 'groups']                 )->bind('admin_groups');
        $controllers->get("/groups/create",              [$this, 'groupsCreate']           )->bind('admin_groups_create')->before($checkCreate);
        $controllers->post("/groups/",                   [$this, 'groupsCreate']           )->bind('admin_groups_store')->before($checkCreate);

        $controllers->get("/groups/{id}",                [$this, 'groupsUpdate']           )->bind('admin_groups_edit')->assert('id', '\d+')->before($checkUpdate);
        $controllers->put("/groups/{id}",                [$this, 'groupsUpdate']           )->bind('admin_groups_update')->assert('id', '\d+')->before($checkUpdate);

        $controllers->delete("/groups/{id}",             [$this, 'groupsDestroy']          )->bind('admin_groups_delete')->assert('id', '\d+')->before($checkDelete);
//        $controllers->delete("/groups/delete_selected",  [$this, 'groupsDestroyCollection'])->bind('admin_groups_deleteSelected');

        $controllers->put("/groups/{id}/roles",             [$this, 'rolesAdd']          )->bind('admin_groups_roles_add')->assert('id', '\d+')->before($checkUpdate);
        $controllers->delete("/groups/{id}/roles",          [$this, 'rolesRemove']       )->bind('admin_groups_roles_remove')->assert('id', '\d+')->before($checkUpdate);

        $controllers->after(function (Request $request, Response $response) use ($app) {
            return $this->after($request, $response);
        });

        return $controllers;
    }

    public function index(Request $request, Application $app){
        return $app->redirect($app['url_generator']->generate('admin_groups'));
    }

    public function groups(Request $request, Application $app){
        // show the list of items

        $this->template = 'groups_table';

        $this->AdminLTEPlugins['dataTables'] = true;

        $modules = $this->getAdminControllers();
        $items = $this->em()->getRepository(self::$entity)->findAll();

        $fields = array('role_title' => array('title' => 'Role'));
        $used_roles = array();
        foreach($items as $item){
            $fields[$item->getName()] = array(
                'id'    => $item->getId(),
                'title' => $item->getTitle(),
            );

            foreach($item->getRoles() as $role){
                $used_roles[$role][$item->getName()] = 1;
            }
        }

        $res_record = array_fill_keys(array_keys($fields), 0);

        $res_array = array();
        foreach($modules as $module_name => $module){
            if(count($module['roles']) > 0){
                $res_array[$module_name] = array_fill_keys(array_keys($fields), '');
                $res_array[$module_name]['role_title'] = $module['title'];
                $res_array[$module_name]['is_header']  = true;

                foreach($module['roles'] as $role_name => $role_title){
                    $res_array[$role_name] = array_merge($res_record, isset($used_roles[$role_name])?$used_roles[$role_name]:array());
                    $res_array[$role_name]['role_title'] = $role_title;
                    $res_array[$role_name]['is_header']  = false;
                }
            }
        }

        $this->data['fields']     = $fields;
        $this->data['items']      = $res_array;
        $this->data['actions']    = $this->actions;
        $this->data['sort_table'] = $this->sortTable;
        return '';
    }

    // create a new item, using POST method
    public function groupsCreate(Request $request, Application $app, $id=null)
    {
        $item = new static::$entity();

        $form = $app['form.factory']->create(static::$form, $item, array(
            'method' => 'POST',
            'action' => $app->path('admin_groups_store'),
            'attr'   => array('role' => 'form'),
            'controllers' =>  $this->getAdminControllers()
        ));

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->em()->getRepository(self::$entity)->save($item);

                $app['session']->getFlashBag()->add('success', 'The item has been created.');
                return $app->redirect($app->path($this->cancel_route));
            }
        }

        $this->data['form']    = $form->createView();
        $this->data['title']   = 'Add new item';
        $this->template = 'form';
        return '';
    }

    // update the item #id, using PUT method
    public function groupsUpdate(Application $app, $id){
        $item = $this->em()->getRepository(self::$entity)->find($id);

        if(is_null($item)){
            $this->app['session']->getFlashBag()->add('danger', 'Item was not found!');
            return $this->app->redirect($this->app->path($this->cancel_route));
        }

        $form = $this->app['form.factory']->create(static::$form, $item, array(
            'method' => 'PUT',
            'action' => $this->app->path('admin_groups_update', array('id' => $id)),
            'attr'   => array('role' => 'form'),
            'controllers' =>  $this->getAdminControllers(),
        ));

        if ($this->app['request']->isMethod('PUT')) {
            $form->handleRequest($this->app['request']);

            if ($form->isValid()) {
                $this->em()->getRepository(self::$entity)->save($item);

                $this->app['session']->getFlashBag()->add('success', 'The item '.$item->getId().' has been updated.');
                return $this->app->redirect($this->app->path($this->cancel_route));
            }
        }

        $this->data['form']    = $form->createView();
        $this->data['title']   = 'Edit item';
        $this->template = 'form';
        return '';
    }

    // delete the item #id, using DELETE method
    public function groupsDestroy(Application $app, $id){
        $item = $this->em()->getRepository(self::$entity)->find($id);

        if(is_null($item)){
            $this->app['session']->getFlashBag()->add('danger', 'Item was not found!');
            return $this->app->redirect($this->app->path($this->cancel_route));
        }

        $this->em()->getRepository(self::$entity)->delete($item);

        $this->app['session']->getFlashBag()->add('success', 'Item was deleted!');
        return '';
    }


    public function rolesAdd(Request $request, Application $app, $id){
        $item = $this->em()->getRepository(self::$entity)->find($id);

        if(is_null($item)){
            $this->app['session']->getFlashBag()->add('danger', 'Group was not found!');
            return $this->app->redirect($this->app->path($this->cancel_route));
        }

        $item->addRole($request->request->get('role'));
        $this->em()->getRepository(self::$entity)->save($item);

        $this->app['session']->getFlashBag()->add('success', 'Role was added!');
        return '';
    }

    public function rolesRemove(Request $request, Application $app, $id){
        $item = $this->em()->getRepository(self::$entity)->find($id);

        if(is_null($item)){
            $this->app['session']->getFlashBag()->add('danger', 'Group was not found!');
            return $this->app->redirect($this->app->path($this->cancel_route));
        }

        $item->removeRole($request->request->get('role'));
        $this->em()->getRepository(self::$entity)->save($item);

        $this->app['session']->getFlashBag()->add('success', 'Role was removed!');
        return '';
    }
}