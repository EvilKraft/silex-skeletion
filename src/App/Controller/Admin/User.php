<?php

namespace App\Controller\Admin;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class User extends Admin
{

    protected static $entity = '\App\Entity\Users';
    protected static $form   = '\App\Form\Admin\UserType';

    protected $template     = 'users';
    protected $cancel_route = 'admin_users';

    protected static $page_title = 'Users';
    protected static $page_desc  = '';
    protected static $icon_class = 'fa fa-users';

    protected static $roles = array(
        'ROLE_USER_VIEW'   => 'View',
        'ROLE_USER_CREATE' => 'Create',
        'ROLE_USER_UPDATE' => 'Update',
        'ROLE_USER_DELETE' => 'Delete',
    );

    protected $showFields = array('id', 'username', 'email', 'roles', 'createdAt', 'isActive');


    private $isEmailConfirmationRequired = false;


    public function connect(Application $app)
    {
        $self = $this;
        $checkView   = function () use ($self){
            $self->isGranted('ROLE_USER_VIEW');
        };
        $checkCreate = function () use ($self){
            $self->isGranted('ROLE_USER_CREATE');
        };
        $checkUpdate = function () use ($self){
            $self->isGranted('ROLE_USER_UPDATE');
        };
        $checkDelete = function () use ($self){
            $self->isGranted('ROLE_USER_DELETE');
        };

        $controllers = $app["controllers_factory"];

        $controllers->before($checkView);

        $controllers->get("/",                    [$this, 'index']            )->bind('admin_users');

        $controllers->get("/create",              [$this, 'create']           )->bind('admin_users_create')->before($checkCreate);
        $controllers->post("/",                   [$this, 'create']           )->bind('admin_users_store')->before($checkCreate);

        $controllers->get("/{id}",                [$this, 'update']           )->assert('id', '\d+')->bind('admin_users_edit')->before($checkUpdate);
        $controllers->put("/{id}",                [$this, 'update']           )->assert('id', '\d+')->bind('admin_users_update')->before($checkUpdate);
        $controllers->delete("/{id}",             [$this, 'destroy']          )->assert('id', '\d+')->bind('admin_users_delete')->before($checkDelete);
        $controllers->delete("/delete_selected",  [$this, 'destroyCollection'])->bind('admin_users_deleteSelected')->before($checkDelete);

        //->convert('id', function ($id) { return (int) $id; });

        $controllers->after(array($this, 'after'));

        return $controllers;
    }

    public function index(Request $request, Application $app){
        // show the list of users

        $this->data['items']      = $app['user.manager']->findAll();
        $this->data['fields']     = count($this->showFields) ? $this->showFields : $this->em()->getClassMetadata(self::$entity)->getFieldNames();
        $this->data['actions']    = $this->actions;
        $this->data['sort_table'] = $this->sortTable;


       // $this->data['fields'] = $this->em()->getClassMetadata(self::$entity)->getFieldNames();

        // http://symfony.com/doc/current/book/doctrine.html
        //  http://docs.doctrine-project.org/projects/doctrine-mongodb-odm/en/latest/reference/query-builder-api.html#conditional-operators
        // http://www.mendoweb.be/blog/using-repositories-doctrine-2/
        // http://odiszapc.ru/doctrine/working-with-objects/

        //   $qb = $this->em()->getRepository('\App\Entity\Drivers')->createQueryBuilder('n');
        //   $items = $qb->where($qb->expr()->in('n.status', array(1,2,3)))->getQuery()->getResult();


        //   $q = $this->em()->createQuery("select n from \App\Entity\Drivers n where n.name = 'Ali'");
        //   $items = $q->getResult();

        // $items = $this->em()->getRepository('\App\Entity\Drivers')->findBy(array('name' => 'Ali'));

        // $query = $this->em()->createQuery("select d from \App\Entity\Drivers d where d.status=1");
        // $query->setMaxResults(30);
        // $items = $query->getResult();

        //http://stackoverflow.com/questions/15619054/is-there-posible-to-use-createquerybuilder-for-insert-update-if-not-what-funct


        //echo '<pre>'.print_r($this->data['fields'], true).'</pre>';
        //echo '<pre>'.print_r($this->data['items'], true).'</pre>';


        $this->AdminLTEPlugins['dataTables'] = true;
        $this->setTemplate('table');
        return '';
    }

    // create a new user, using POST method
    public function create(Request $request, Application $app)
    {
        $user = new static::$entity();

        $form = $app['form.factory']->create(static::$form, $user, array(
            'method' => 'POST',
            'action' => $app->path('admin_users_store'),
            'attr'   => array('role' => 'form')
        ));

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            if ($form->isValid()) {
                if ($this->isEmailConfirmationRequired) {
                    $user->setEnabled(false);
                    $user->setConfirmationToken($app['user.tokenGenerator']->generateToken());
                }


                $app['user.manager']->save($user);

                $app['session']->getFlashBag()->add('success', 'The user '.$user->getUsername().' has been created.');
                return $app->redirect($app->path($this->cancel_route));
            }
        }

        $this->data['form'] = $form->createView();
        $this->data['title'] = 'Add new user';

        $this->setTemplate('form');
        return '';
    }

    // update the user #id, using PUT method
    public function update(Application $app, $id){
        $user = $app['user.manager']->find($id);

        if(is_null($user)){
            $this->app['session']->getFlashBag()->add('danger', 'User was not found!');
            return $this->app->redirect($this->app->path($this->cancel_route));
        }

        $form = $this->app['form.factory']->create(static::$form, $user, array(
            'method' => 'PUT',
            'action' => $this->app->path('admin_users_update', array('id' => $id)),
            'attr'   => array('role' => 'form')
        ));

        if ($this->app['request']->isMethod('PUT')) {
            $form->handleRequest($this->app['request']);

            if ($form->isValid()) {
                $this->app['user.manager']->save($user);

                $this->app['session']->getFlashBag()->add('success', 'The user '.$user->getUsername().' has been updated.');
                return $this->app->redirect($this->app->path($this->cancel_route));
            }
        }

        $this->data['form'] = $form->createView();
        $this->data['title'] = 'Edit User';

        $this->setTemplate('form');
        return '';
    }

    // delete the user #id, using DELETE method
    public function destroy(Application $app, $id){
        $user = $app['user.manager']->find($id);

        if(is_null($user)){
            $this->app['session']->getFlashBag()->add('danger', 'User was not found!');
            return $this->app->redirect($this->app->path($this->cancel_route));
        }

        $this->app['user.manager']->delete($user);

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

        $this->app['session']->getFlashBag()->add('success', 'Users were deleted!');
        return '';
    }

}