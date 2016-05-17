<?php

namespace App\Controller\Admin;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Content extends Admin
{

    protected static $entity = '\App\Entity\Content';
    protected static $form   = '\App\Form\Admin\ContentType';

    protected $template     = 'content';
    protected $cancel_route = 'admin_content';

    protected static $page_title = 'Content';
    protected static $page_desc  = '';

    protected static $roles = array(
        'ROLE_CONTENT_VIEW'   => 'View',
        'ROLE_CONTENT_CREATE' => 'Create',
        'ROLE_CONTENT_UPDATE' => 'Update',
        'ROLE_CONTENT_DELETE' => 'Delete',
    );

    protected $sortTable  = false;
    protected $showFields = array('id', 'getLaveledTitle', 'alias');
    protected $actions    = array('edit', 'delete', 'create_child', 'move');

    public function connect(Application $app)
    {
        $self = $this;
        $checkView   = function () use ($self){$self->isGranted('ROLE_CONTENT_VIEW');};
        $checkCreate = function () use ($self){$self->isGranted('ROLE_CONTENT_CREATE');};
        $checkUpdate = function () use ($self){$self->isGranted('ROLE_CONTENT_UPDATE');};
        $checkDelete = function () use ($self){$self->isGranted('ROLE_CONTENT_DELETE');};

        $controllers = $app["controllers_factory"];

        $controllers->before($checkView);

        $controllers->get("/",                    [$this, 'index']            )->bind('admin_content');

        $controllers->get("/create",              [$this, 'create']           )->bind('admin_content_create')->before($checkCreate);
        $controllers->get("/{id}/create",         [$this, 'create']           )->bind('admin_content_createChild')->before($checkCreate)->assert('id', '\d+');
        $controllers->post("/",                   [$this, 'create']           )->bind('admin_content_store')->before($checkCreate);

        $controllers->get("/{id}",                [$this, 'update']           )->bind('admin_content_edit')->assert('id', '\d+')->before($checkUpdate);
        $controllers->put("/{id}",                [$this, 'update']           )->bind('admin_content_update')->assert('id', '\d+')->before($checkUpdate);
        $controllers->delete("/{id}",             [$this, 'destroy']          )->bind('admin_content_delete')->assert('id', '\d+')->before($checkDelete);
        $controllers->delete("/delete_selected",  [$this, 'destroyCollection'])->bind('admin_content_deleteSelected')->before($checkDelete);

        $controllers->put("/{id}/move",           [$this, 'move']             )->bind('admin_content_move')->assert('id', '\d+')->before($checkUpdate);

        //->convert('id', function ($id) { return (int) $id; });


        $controllers->after(array($this, 'after'));

        return $controllers;
    }

    public function index(Request $request, Application $app){
        // show the list of items

        $this->template = 'table';

        $this->AdminLTEPlugins['dataTables'] = true;

        $query = $this->em()->createQueryBuilder()
            ->select('node')
            ->from(self::$entity, 'node')
            ->where('node.parent IS NOT NULL')
            ->orderBy('node.root, node.lft', 'ASC')
            ->getQuery()
        ;
        $tree = $this->em()->getRepository(self::$entity)->buildTreeArrayOfObjects($query->getResult());

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

        if(!is_null($id)){
            $parent = $this->em()->getRepository(self::$entity)->find($id);
            if(is_null($parent)){
                throw new Exception('Item '.$id.' not found!');
            }
            $item->setParent($parent);
        }


        $form = $app['form.factory']->create(static::$form, $item, array(
            'method' => 'POST',
            'action' => $app->path('admin_content_store'),
            'attr'   => array('role' => 'form'),
            'langs'  => $langs,
        ));

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            if ($form->isValid()) {
                $item->setParent($form->get('parentId')->getData());

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

        $form = $this->app['form.factory']->create(static::$form, $item, array(
            'method' => 'PUT',
            'action' => $this->app->path('admin_content_update', array('id' => $id)),
            'attr'   => array('role' => 'form'),
            'langs'  => $langs,
        ));

        if ($this->app['request']->isMethod('PUT')) {

            $form->handleRequest($this->app['request']);

            if ($form->isValid()) {
                $item->setParent($form->get('parentId')->getData());

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

    public function move(Request $request, Application $app, $id){
        $item = $this->em()->getRepository(self::$entity)->find($id);

        if(is_null($item)){
            $this->app['session']->getFlashBag()->add('danger', 'Item was not found!');
            return $this->app->redirect($this->app->path($this->cancel_route));
        }

        switch($request->get('direction')){
            case 'up'   : $this->em()->getRepository(self::$entity)->moveUp($item, 1);      break;
            case 'down' : $this->em()->getRepository(self::$entity)->moveDown($item, 1);    break;
            default:
                $this->app['session']->getFlashBag()->add('danger', 'Can not move in '.$request->get('direction').' direction!');
                return $this->app->redirect($this->app->path($this->cancel_route));
        }

        $this->app['session']->getFlashBag()->add('success', 'Item was moved!');
        return '';
    }
}