<?php

namespace App\Controller\Admin;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class User extends Admin
{

    protected static $entity = '\App\Entity\Users';

    protected $template_name = 'users';

    protected $page_title = 'Users';
    protected $page_desc  = '';

    protected $data = array();


    public function connect(Application $app)
    {
        $controllers = $app["controllers_factory"];

        $controllers->before(function (Request $request) use ($app) {
            // check for something here

            //echo $request->get("_route"); exit;
        });



        $controllers->get("/",          [$this, 'index']  )->bind('users');

        $controllers->post("/save",         [$this, 'save']  )->bind('users_save');

        //$controllers->post("/",         [$this, 'store']  )->bind('users_create');
        $controllers->get("/{id}",      [$this, 'show']   )->bind('users_show');
        $controllers->get("/edit/{id}", [$this, 'edit']   )->bind('users_edit');
        //$controllers->put("/{id}",      [$this, 'update'] )->bind('users_update');
        $controllers->delete("/{id}",   [$this, 'destroy'])->bind('users_delete');


        $controllers->after(function (Request $request, Response $response) use ($app) {
            if ('application/json' === $request->headers->get('Accept')) {
                return $app->json($this->data);
            }


            $this->initTwig($app, $request);

            $response->setContent(
                $this->twig()->render('admin/'.$this->template_name.'.html.twig', $this->data)
            );
        });

        return $controllers;
    }

    public function index(Request $request, Application $app){
        // show the list of users

        $this->AdminLTEPlugins['dataTables'] = true;


        $items = $this->em()->getRepository(self::$entity)->findAll();

        $this->data['fields'] = $this->em()->getClassMetadata(self::$entity)->getFieldNames();
        $this->data['items']  = $items;


        //echo '<pre>'.print_r($this->data['fields'], true).'</pre>';
        //echo '<pre>'.print_r($this->data['items'], true).'</pre>';


        //return $this->twig()->render('admin/users.html.twig', $this->data);
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

        $form = $app['form.factory']->create(new \My\Namespace\To\Form\FormType(), $entity);

        $form->handleRequest($request);

        exit;
    }

    public function edit($id){
        // show edit form

        $item = $this->em()->getRepository(self::$entity)->find($id);
        $this->data['item']  = $item;

        $this->template_name = $this->template_name.'_edit';
        return '';
    }

    public function show($id){
        // show the user #id

        return $this->twig()->render(
            'Front/index.html.twig',
            [
                'currentPage' => 'home',
                'timezones'   => \DateTimeZone::listIdentifiers(),
                'edition'     => '',
            ]
        );
    }

    public function store(){
        // create a new user, using POST method
    }

    public function update($id){
        // update the user #id, using PUT method




        return '';
    }

    public function destroy($id){
        // delete the user #id, using DELETE method
    }
}