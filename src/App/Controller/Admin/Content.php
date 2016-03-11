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

    protected $sortTable  = false;
    protected $showFields = array('id', 'getLaveledTitle', 'alias');
    protected $actions    = array('edit', 'delete', 'create_child');

    public function connect(Application $app)
    {
        $controllers = $app["controllers_factory"];

        $controllers->before(function (Request $request) use ($app) {
            // check for something here
        });

        $controllers->get("/",                    [$this, 'index']            )->bind('admin_content');

        $controllers->get("/create",              [$this, 'create']           )->bind('admin_content_create');
        $controllers->get("/{id}/create",         [$this, 'create']           )->bind('admin_content_createChild')->assert('id', '\d+');
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

        $query = $this->em()
            ->createQueryBuilder()
            ->select('node')
            ->from(self::$entity, 'node')
            ->orderBy('node.root, node.lft', 'ASC')
            ->getQuery()
        ;
        $tree = $query->getResult();

       // $this->data['items']   = $this->em()->getRepository(self::$entity)->findAll();
        $this->data['items']      = $tree;
        $this->data['fields']     = count($this->showFields) ? $this->showFields : $this->em()->getClassMetadata(self::$entity)->getFieldNames();
        $this->data['actions']    = $this->actions;
        $this->data['sort_table'] = $this->sortTable;

        return '';
    }

    // create a new item, using POST method
    public function create(Request $request, Application $app, $id=null)
    {
        $item = new static::$entity();

        $langs = $this->em()->getRepository('\App\Entity\Languages')->findAllActive();
        foreach($langs as $lang){
            if(!$item->hasLang($lang->getId())){
                $newLang = new \App\Entity\ContentLangs();
                $newLang->setLanguageId($lang->getId());
                $item->addLang($newLang);
            }
        }

        $form = $app['form.factory']->create(new static::$form($app), $item, array(
            'method' => 'POST',
            'action' => $app->path('admin_content_store'),
            'attr'   => array('role' => 'form'),
            'langs'  => $langs,
        ));

        if(!is_null($id)){
            $parent = $this->em()->getRepository(self::$entity)->find($id);
            if(is_null($parent)){
                throw new Exception('Item '.$id.' not found!');
            }
            $form->get('parentId')->setData($parent->getId());
        }


        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            if ($form->isValid()) {
                $item->setParent($this->em()->getRepository(self::$entity)->find($form->get('parentId')->getData()));
                $this->em()->getRepository(self::$entity)->save($item);

                $app['session']->getFlashBag()->add('success', 'The item has been created.');
                return $app->redirect($app->path($this->cancel_route));
            }
        }

        $this->data['form'] = $form->createView();
        $this->data['title'] = 'Add new item';

        $this->template = $this->template.'_form';
        return '';
    }

    // update the item #id, using PUT method
    public function update(Application $app, $id){
        $item = $this->em()->getRepository(self::$entity)->find($id);

        if(is_null($item)){
            $this->app['session']->getFlashBag()->add('danger', 'Item was not found!');
            return $this->app->redirect($this->app->path($this->cancel_route));
        }

        $langs = $this->em()->getRepository('\App\Entity\Languages')->findAllActive();
        foreach($langs as $lang){
            if(!$item->hasLang($lang->getId())){
                $newLang = new \App\Entity\ContentLangs();
                $newLang->setLanguageId($lang->getId());
                $item->addLang($newLang);
            }
        }

        $form = $this->app['form.factory']->create(new static::$form($app), $item, array(
            'method' => 'PUT',
            'action' => $this->app->path('admin_content_update', array('id' => $id)),
            'attr'   => array('role' => 'form'),
            'langs'  => $langs,
        ));


        if ($this->app['request']->isMethod('PUT')) {

            $form->handleRequest($this->app['request']);

            if ($form->isValid()) {
                $this->em()->getRepository(self::$entity)->save($item);

                $this->app['session']->getFlashBag()->add('success', 'The item '.$item->getId().' has been updated.');
                return $this->app->redirect($this->app->path($this->cancel_route));
            }
        }

        $this->data['form'] = $form->createView();
        $this->data['title'] = 'Edit item';

        $this->template = $this->template.'_form';
        return '';
    }

    // delete the item #id, using DELETE method
    public function destroy(Application $app, $id){
        $item = $this->em()->getRepository(self::$entity)->find($id);

        if(is_null($item)){
            $this->app['session']->getFlashBag()->add('danger', 'Item was not found!');
            return $this->app->redirect($this->app->path($this->cancel_route));
        }

        $this->em()->getRepository(self::$entity)->delete($item);

        $this->app['session']->getFlashBag()->add('success', 'Item was deleted!');
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

        $this->app['session']->getFlashBag()->add('success', 'Items were deleted!');
        return '';
    }

}