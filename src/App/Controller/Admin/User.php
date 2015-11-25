<?php

namespace App\Controller\Admin;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;


class User extends Admin
{

    protected static $entity = '\App\Entity\Likes';


    public function connect(Application $app)
    {
        $controllers = $app["controllers_factory"];

        $controllers->before(function() {
            // check for something here
        });

        $controllers->get("/",          [$this, 'index']  )->bind('users');
        $controllers->post("/",         [$this, 'store']  )->bind('create_user');
        $controllers->get("/{id}",      [$this, 'show']   )->bind('show_user');
        $controllers->get("/edit/{id}", [$this, 'edit']   )->bind('edit_user');
        $controllers->put("/{id}",      [$this, 'update'] )->bind('update_user');
        $controllers->delete("/{id}",   [$this, 'destroy'])->bind('delete_user');

        return $controllers;
    }

    public function index(Request $request, Application $app){
        // show the list of users



        $forms = $this->em()->getRepository(self::$entity)->findAll();
        echo '<pre>'.print_r($forms, true).'</pre>';


        return 'zzzzzz';
    }

    public function edit($id){
        // show edit form
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
    }

    public function destroy($id){
        // delete the user #id, using DELETE method
    }
}