<?php

namespace App\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Frontend extends \App\Controller
{

    public function connect(Application $app)
    {
        $controllers = $app["controllers_factory"];

        $controllers->before(function (Request $request) use ($app) {
            // check for something here
        });

        $controllers->get("/",        [$this, 'indexAction']    )->bind('frontend_home');
        $controllers->get("/about",   [$this, 'aboutAction']    )->bind('frontend_about');

        $controllers->method('GET|POST')->match("/contact", [$this, 'contactAction']  )->bind('frontend_contact');

        $controllers->get("/test",    [$this, 'testAction']     )->bind('frontend_test');

        $controllers->after(array($this, 'after'));

        return $controllers;
    }


    public function indexAction(Request $request, Application $app)
    {
        self::$page_title = 'Main Page';
        return '';
    }

    public function aboutAction(Request $request, Application $app)
    {
        self::$page_title = 'About Us';
        return '';
    }

    public function contactAction(Request $request, Application $app)
    {
        $form = $app['form.factory']->create('\App\Form\Frontend\FeedbackType',null, array(
            'method' => 'POST',
            'action' => $app->path('frontend_contact'),
            'attr'   => array('role' => 'form')
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try{
                $data = $form->getData();

                $subject = "Message from ".$data['name'];
                $msg_body = $app['twig']->render('emails/feedback.twig', $data);

                $app['mailer']->send(\Swift_Message::newInstance()
                    ->setSubject($subject)
                    ->setFrom(array($data['email']))
                    ->setTo(array($app['email.feedback']))
                    ->setBody($msg_body,'text/html')
                    ->addPart(strip_tags($msg_body), 'text/plain')
                );

                $app['session']->getFlashBag()->add('success', 'Email has been sent.');
                return $app->redirect($app['url_generator']->generate('frontend_contact'));

            }catch (Exception $e) {
                $error = $e->getMessage();
                $app['session']->getFlashBag()->add('danger', $error);
            }
        }

        $this->data['form'] = $form->createView();

        self::$page_title = 'Contact';
        return new Response('', 200, array('Cache-Control' => 's-maxage=3600, public'));
    }

    public function testAction(Request $request, Application $app)
    {
        self::$page_title = 'Test Page';
        return new Response('', 200, array('Cache-Control' => 's-maxage=3600, public'));
    }

}
