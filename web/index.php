<?php

$loader = require_once __DIR__.'/../vendor/autoload.php';

//$loader->add('Acme', __DIR__.'/../src/');

class Application extends Silex\Application
{
    use Silex\Application\UrlGeneratorTrait;
}

$app = new Application();


//require __DIR__ . '/../app/config/prod.php';
require __DIR__ . '/../app/config/dev.php';

require __DIR__ . '/../src/app.php';
require __DIR__ . '/../src/routes.php';


$app->run();