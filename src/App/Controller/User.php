<?php

namespace App\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Validator\Constraints as Assert;

class User extends \App\Controller
{
    private $isEmailConfirmationRequired = true;

    public function connect(Application $app)
    {
        $controllers = $app["controllers_factory"];

        $controllers->before(function (Request $request) use ($app) {
            // check for something here
        });

        $controllers->method('GET|POST')->match("/register", [$this, 'registerAction']  )->bind('user_register');


        $controllers->get("/confirm", [$this, 'confirmEmailAction']        )->bind('user_confirm');
        $controllers->get("/resend",  [$this, 'resendConfirmationAction']  )->bind('user_resend');
        $controllers->get("/forgot",  [$this, 'forgotPasswordAction']      )->bind('user_forgot');
        $controllers->get("/reset",   [$this, 'resetPasswordAction']       )->bind('user_reset');


        $controllers->get("/view",     [$this, 'viewAction']       )->bind('user_view');
        $controllers->get("/viewself", [$this, 'viewSelfAction']   )->bind('user_viewself');
        $controllers->get("/edit",     [$this, 'editAction']       )->bind('user_edit');

        $controllers->after(array($this, 'after'));

        return $controllers;
    }


    public function registerAction(Request $request, Application $app)
    {
        $user = new \App\Entity\Users();
        $form = $app['form.factory']->create('\App\Form\User\RegisterType', $user, array(
            'method' => 'POST',
            'action' => $app->path('user_register'),
            'attr'   => array('role' => 'form')
        ));

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                if ($this->isEmailConfirmationRequired) {
                    $user->setEnabled(false);
                    $user->setConfirmationToken($app['user.tokenGenerator']->generateToken());
                }


                try{

                    $group = $this->em()->getRepository('\App\Entity\Groups')->findOneByName('TESTGROUP');
                    $user->addGroup($group);
                    $user->setRoles(array());

                    $app['user.manager']->save($user);

                    if ($this->isEmailConfirmationRequired) {
                        $message = \Swift_Message::newInstance()
                            ->setSubject('['.$app['site_title'].'] Registration')
                            ->setFrom($app['site_email'])
                            ->setTo($user->getEmail());

                        $htmlBody = 'dddddd_html';
                        $textBody = 'dddddd_text';

                        $message->setBody($htmlBody, 'text/html')
                            ->addPart($textBody, 'text/plain');

                        $app['mailer']->send($message);

                        $app['session']->getFlashBag()->add('success', 'Account created.');

                        return $app->redirect($app['url_generator']->generate('user_confirm'));
                    }else{
                        // Log the user in to the new account.
                        $app['user.manager']->loginAsUser($user);

                        $app['session']->getFlashBag()->add('success', 'Account created.');

                        // Redirect to user's new profile page.
                        //return $app->redirect($app['url_generator']->generate('user_view', array('id' => $user->getId())));
                        return $app->redirect($app['url_generator']->generate('user_viewself'));
                    }
                }catch (Exception $e) {
                    $error = $e->getMessage();
                    $app['session']->getFlashBag()->add('danger', $error);
                }

            }
        }


        $this->data['form'] = $form->createView();

        self::$page_title = 'Registration';
        $this->template = 'user_register';

        return '';
    }


    public function confirmEmailAction(Request $request, Application $app)
    {

        self::$page_title = 'Confirm Email';
        //$this->template = 'user_register';
        $this->template = 'index';

        return '';
    }
}
