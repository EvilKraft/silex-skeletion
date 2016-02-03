<?php

namespace App\Controller\Admin;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class User extends Admin
{

    protected static $entity = '\App\Entity\Users';

    protected $template = 'users';

    protected static $page_title = 'Users';
    protected static $page_desc  = '';
    protected static $icon_class = 'fa fa-users';

    protected $data = array();


    public function connect(Application $app)
    {
        $controllers = $app["controllers_factory"];

        $controllers->before(function (Request $request) use ($app) {
            // check for something here
        });



        $controllers->get("/",          [$this, 'index']  )->bind('admin_users');

        $controllers->match("/add",      [$this, 'addAction']   )->bind('admin_users_add');



        $controllers->post("/save",         [$this, 'save']  )->bind('admin_users_save');

        //$controllers->post("/",         [$this, 'store']  )->bind('admin_users_create');
        $controllers->get("/{id}",      [$this, 'show']   )->bind('admin_users_show');
        $controllers->get("/edit/{id}", [$this, 'edit']   )->bind('admin_users_edit');
        //$controllers->put("/{id}",      [$this, 'update'] )->bind('admin_users_update');
        $controllers->delete("/{id}",   [$this, 'destroy'])->bind('admin_users_delete');


        $controllers->after(function (Request $request, Response $response) use ($app) {
            $this->after($request, $response);
        });

        return $controllers;
    }

    public function index(Request $request, Application $app){
        // show the list of users

        $this->template = 'table';

        $this->AdminLTEPlugins['dataTables'] = true;


        $items = $this->em()->getRepository(self::$entity)->findAll();

        $this->data['fields'] = $this->em()->getClassMetadata(self::$entity)->getFieldNames();
        $this->data['items']  = $items;



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


        //return $this->twig()->render('admin/users.twig', $this->data);
        return '';
    }

    public function save(Request $request, Application $app){

        $em = $app['orm.em'];

        $entity = new self::$entity;

        $form = $app['form.factory']->create(new \App\Form\UserFormType(), $entity);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirectToRoute('task_success');
        }

        return new Response($app['twig']->render('form.html.twig', array(
            'form' => $form->createView(),
        )));




        $values = $request->request->all();

        $entity = new self::$entity;

        $form = $app['form.factory']->create(new \App\Form\FormType(), $entity);

        $form->handleRequest($request);

        exit;
    }

    public function edit($id){
        // show edit form

        $item = $this->em()->getRepository(self::$entity)->find($id);
        $this->data['item']  = $item;

        $this->template = $this->template.'_edit';
        return '';
    }

    public function show($id){
        // show the user #id

        return $this->twig()->render(
            'Front/index.twig',
            [
                'currentPage' => 'home',
                'timezones'   => \DateTimeZone::listIdentifiers(),
                'edition'     => '',
            ]
        );
    }

    public function store(){
        // create a new user, using POST method
        return '';
    }

    public function update($id){
        // update the user #id, using PUT method
        return '';
    }

    public function destroy($id){
        // delete the user #id, using DELETE method
        return '';
    }


    public function addAction(Request $request, Application $app)
    {
        $user = new \App\Entity\Users();
        $form = $app['form.factory']->create(new \App\Form\UserType(), $user);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $app['repository.user']->save($user);
                $message = 'The user ' . $user->getUsername() . ' has been saved.';
                $app['session']->getFlashBag()->add('success', $message);
                // Redirect to the edit page.
                //$redirect = $app['url_generator']->generate('admin_user_edit', array('user' => $user->getId()));
                $redirect = $app['url_generator']->generate('admin_users');
                return $app->redirect($redirect);
            }
        }


        $this->data['form'] = $form->createView();
        $this->data['title'] = 'Add new user';


        $this->template_name = 'form';
        return '';
    }
}