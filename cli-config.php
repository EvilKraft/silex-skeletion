<?php

use Doctrine\Common\Annotations\AnnotationRegistry,
    Symfony\Component\Console\Helper\HelperSet,
    Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper,
    Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper,
    Symfony\Component\Console\Application as CliAplication,
    Doctrine\ORM\Tools\Console\Command;


$loader = require __DIR__ ."/vendor/autoload.php";

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));


$app = new Silex\Application();

require __DIR__ . '/app/config/dev.php';

$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider());
$app['db.event_manager']->addEventSubscriber(new Gedmo\Tree\TreeListener());

$helperSet = new HelperSet(array(
    'db' => new ConnectionHelper($app['orm.em']->getConnection()),
    'em' => new EntityManagerHelper($app['orm.em'])
));

$cli = new CliAplication('Doctrine Command Line Interface', Doctrine\DBAL\Version::VERSION);
$cli->setCatchExceptions(true);
$cli->setHelperSet($helperSet);
$cli->addCommands([
    new Command\GenerateRepositoriesCommand,
    new Command\GenerateEntitiesCommand,
    new Command\ConvertMappingCommand,
    new Command\ValidateSchemaCommand,
    new Command\SchemaTool\CreateCommand,
    new Command\SchemaTool\UpdateCommand,
    new Command\GenerateProxiesCommand
]);
$cli->run();
