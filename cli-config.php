<?php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once __DIR__ ."/vendor/autoload.php";

require __DIR__ . '/app/config/dev.php';

$isDevMode = true;

/**
 * Creates a configuration with an annotation metadata driver.
 *
 * @param array   $paths
 * @param boolean $isDevMode
 * @param string  $proxyDir
 * @param Cache   $cache
 * @param bool    $useSimpleAnnotationReader
 *
 * @return Configuration
 */
$config = Setup::createAnnotationMetadataConfiguration(array($app['orm.em.options']['mappings'][0]['path']), $isDevMode, null, null, false);

$entityManager = EntityManager::create($app['db.options'] , $config);

$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($entityManager->getConnection()),
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($entityManager)
));

return $helperSet;