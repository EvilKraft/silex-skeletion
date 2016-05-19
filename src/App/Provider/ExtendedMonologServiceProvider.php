<?php

namespace App\Provider;
use Monolog\Handler\BufferHandler;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Logger;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Bridge\Monolog\Handler\SwiftMailerHandler;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
class ExtendedMonologServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register( Application $app )
    {
        $app['email.support'] = 'noreply@silex.nothing';

        $app['logger.message_generator'] = function () use ( $app ) {
            $message = \Swift_Message::newInstance();
            $message
                ->setSubject('Error report from '.$_SERVER['HTTP_HOST'] )
                ->setFrom($app['email.support'])
                ->setTo($app['email.support']);
            return $message;
        };
        $app['logger.swift_mailer_handler'] = $app->share(function ( $app ) {
            $handler = new SwiftMailerHandler($app['mailer'], $app['logger.message_generator'], Logger::DEBUG);
            $handler->setTransport($app['swiftmailer.transport']);
            return $handler;
        });
        if (!$app['debug']) {
            $app['monolog'] = $app->share($app->extend('monolog',
                function ( $monolog, $app ) {
                    /** @var $monolog Logger */
                    $bufferHander = new BufferHandler($app['logger.swift_mailer_handler']);
                    $fingersCrossedHandler = new FingersCrossedHandler($bufferHander, Logger::ERROR, 200);
                    $monolog->pushHandler($fingersCrossedHandler);
                    return $monolog;
                }
            ));
        }
    }
    /**
     * {@inheritdoc}
     */
    public function boot( Application $app )
    {
        $app->on(KernelEvents::TERMINATE, function ( PostResponseEvent $event ) use ( $app ) {
            if ($app['mailer.initialized']) {
                $app['logger.swift_mailer_handler']->onKernelTerminate($event);
            }
        });
    }
}