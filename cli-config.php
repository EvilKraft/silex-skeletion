<?php


// Path definitions
define("ROOT_PATH",         realpath(__DIR__));                         // Root directory
define("RESOURCES_PATH",    realpath(ROOT_PATH . "/resources/"));       // Resources
define("CACHE_PATH",        realpath(RESOURCES_PATH . "/cache/"));      // Cache
define("APP_PATH",          realpath(ROOT_PATH . "/src/"));             // Aplication
define("VENDOR_PATH",       realpath(ROOT_PATH . "/vendor/"));          // Vendor

$loader = require_once VENDOR_PATH.'/autoload.php';
Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));


use Symfony\Component\Console\Application as CliAplication;
use Doctrine\ORM\Tools\Console\Command;


$app = new Silex\Application();

require_once RESOURCES_PATH.'/config/dev.php';

$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider());
$app['db.event_manager']->addEventSubscriber(new Gedmo\Tree\TreeListener());

$helperSet = new Symfony\Component\Console\Helper\HelperSet(array(
    'db' => new Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($app['orm.em']->getConnection()),
    'em' => new Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($app['orm.em'])
));

$cli = new CliAplication('My Application', Doctrine\DBAL\Version::VERSION);
$cli->setCatchExceptions(true);
$cli->setHelperSet($helperSet);
$cli->addCommands([
    new Command\GenerateRepositoriesCommand,
    new Command\GenerateEntitiesCommand,
    new Command\ConvertMappingCommand,
    new Command\ValidateSchemaCommand,
    new Command\SchemaTool\CreateCommand,
    new Command\SchemaTool\UpdateCommand,
    new Command\GenerateProxiesCommand,

    new App\Command\HelloWorldCommand(),
    new App\Command\ClearCacheCommand(CACHE_PATH),
]);
$cli->run();
