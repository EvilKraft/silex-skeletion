<?php

namespace App\Controller\Admin;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Content extends Admin
{

    protected static $entity = '\App\Entity\Content';
    protected static $form   = '\App\Form\ContentType';

    protected $template     = 'content';
    protected $cancel_route = 'admin_content';

    protected static $page_title = 'Content';
    protected static $page_desc  = '';

//    protected $showFields = array('id', 'username', 'email', 'roles', 'createdAt', 'isActive');

    public function connect(Application $app)
    {
        $controllers = $app["controllers_factory"];

        $controllers->before(function (Request $request) use ($app) {
            // check for something here
        });

        $controllers->get("/",                    [$this, 'index']            )->bind('admin_content');

        $controllers->get("/create",              [$this, 'create']           )->bind('admin_content_create');
        $controllers->post("/",                   [$this, 'create']           )->bind('admin_content_store');

        $controllers->get("/{id}",                [$this, 'update']           )->assert('id', '\d+')->bind('admin_content_edit');
        $controllers->put("/{id}",                [$this, 'update']           )->assert('id', '\d+')->bind('admin_content_update');
        $controllers->delete("/{id}",             [$this, 'destroy']          )->assert('id', '\d+')->bind('admin_content_delete');
        $controllers->delete("/delete_selected",  [$this, 'destroyCollection'])->bind('admin_content_deleteSelected');

        //->convert('id', function ($id) { return (int) $id; });


        $controllers->after(function (Request $request, Response $response) use ($app) {
            return $this->after($request, $response);
        });

        return $controllers;
    }

    public function index(Request $request, Application $app){
        // show the list of items

        $this->template = 'table';

        $this->AdminLTEPlugins['dataTables'] = true;

        $this->data['items']  = $this->em()->getRepository(self::$entity)->findAll();
        $this->data['fields'] = count($this->showFields) ? $this->showFields : $this->em()->getClassMetadata(self::$entity)->getFieldNames();

        return '';
    }

    // create a new user, using POST method
    public function create(Request $request, Application $app)
    {
        $this->data['langs'] = $this->em()->getRepository('\App\Entity\Languages')->findAllActive();

        $item = new static::$entity();

        $form = $app['form.factory']->create(new static::$form($app), $item, array(
            'method' => 'POST',
            'action' => $app->path('admin_content_store'),
            'attr'   => array('role' => 'form')
        ));

        $form->add('languages', \Symfony\Component\Form\Extension\Core\Type\CollectionType::class, array(
            'entry_type' => ContentLanguageType::class
        ));

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            if ($form->isValid()) {
                $app['repository.user']->save($item);

                $app['session']->getFlashBag()->add('success', 'The user '.$item->getUsername().' has been created.');
                return $app->redirect($app->path($this->cancel_route));
            }
        }

        $this->data['form'] = $form->createView();
        $this->data['title'] = 'Add new user';

        $this->template = $this->template.'_form';
        return '';
    }

    // update the user #id, using PUT method
    public function update(Application $app, $id){
        $user = $this->em()->getRepository(self::$entity)->find($id);

        if(is_null($user)){
            $this->app['session']->getFlashBag()->add('danger', 'User was not found!');
            return $this->app->redirect($this->app->path($this->cancel_route));
        }

        $form = $this->app['form.factory']->create(new static::$form($app), $user, array(
            'method' => 'PUT',
            'action' => $this->app->path('admin_content_update', array('id' => $id)),
            'attr'   => array('role' => 'form')
        ));

        if ($this->app['request']->isMethod('PUT')) {

            $form->handleRequest($this->app['request']);

            if ($form->isValid()) {
                $this->app['repository.user']->save($user);

                $this->app['session']->getFlashBag()->add('success', 'The user '.$user->getUsername().' has been updated.');
                return $this->app->redirect($this->app->path($this->cancel_route));
            }
        }

        $this->data['form'] = $form->createView();
        $this->data['title'] = 'Edit User';

        $this->template = $this->template.'_form';
        return '';
    }

    // delete the user #id, using DELETE method
    public function destroy(Application $app, $id){
        $user = $this->em()->getRepository(self::$entity)->find($id);

        if(is_null($user)){
            $this->app['session']->getFlashBag()->add('danger', 'User was not found!');
            return $this->app->redirect($this->app->path($this->cancel_route));
        }

        $this->app['repository.user']->delete($user);

        $this->app['session']->getFlashBag()->add('success', 'User was deleted!');
        return '';
    }

    public function destroyCollection(Request $request, Application $app){
        $ids = $this->app['request']->get('ids');

        if(!is_array($ids)){
            $ids = array($ids);
        }

        $qb = $this->em()->createQueryBuilder()
            ->delete(static::$entity, 't')
            ->where('t.id IN (:ids)')
            ->setParameter('ids', $ids);
        $qb->getQuery()->execute();

        $this->app['session']->getFlashBag()->add('success', 'Rows were deleted!');
        return '';
    }

}