<?php

// (1) Autocargamos clases
require_once 'Doctrine/Common/ClassLoader.php';
$classLoader = new \Doctrine\Common\ClassLoader('Doctrine');
$classLoader->register();
$classLoader = new \Doctrine\Common\ClassLoader('Entities', __DIR__);
$classLoader->register();
$classLoader = new \Doctrine\Common\ClassLoader('Proxies', __DIR__);
$classLoader->register();

// (2) Configuración
$config = new \Doctrine\ORM\Configuration();

// (3) Caché
$cache = new \Doctrine\Common\Cache\ArrayCache();
$config->setMetadataCacheImpl($cache);
$config->setQueryCacheImpl($cache);

// (4) Driver
$driverImpl = $config->newDefaultAnnotationDriver(array(__DIR__."/Entities"));
$config->setMetadataDriverImpl($driverImpl);

// (5) Proxies
$config->setProxyDir(__DIR__ . '/Proxies');
$config->setProxyNamespace('Proxies');

// (6) Conexión
$connectionOptions = array(
    'driver' => 'pdo_sqlite',
    'path' => 'database.sqlite'
);

// (7) EntityManager
$em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);

// (8) HelperSet
$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)
));