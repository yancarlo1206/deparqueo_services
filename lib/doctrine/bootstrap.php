<?php
// bootstrap.php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once "vendor/autoload.php";

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/src"), $isDevMode);
// or if you prefer yaml or XML
//$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);
//$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/config/yaml"), $isDevMode);

// database configuration parameters
$connectionOptions = array(
    'driver' => 'pdo_ibm_i',
	//'driverClass' => 'Doctrine\DBAL\Driver\IBMDB2\DB2Driver',
	'host' => 'localhost',
    'port' => '50000',
    'user' => 'db2admin',
    'password' => '123456',
    'dbname' => 'DBSIANX'
);

// obtaining the entity manager
$entityManager = EntityManager::create($connectionOptions, $config);

?>