<?php

class Doctrine {
	
    static protected $em = null;
	
    private function __construct() {		
	}
	
	public function getEm() {
		if (self::$em == null) {
			require_once 'Doctrine/Common/ClassLoader.php';
			$classLoader = new \Doctrine\Common\ClassLoader('Doctrine');
			$classLoader->register();
			$classLoader = new \Doctrine\Common\ClassLoader('Entities', __DIR__."/../../models");
			$classLoader->register();
			$classLoader = new \Doctrine\Common\ClassLoader('Proxies', __DIR__);
			$classLoader->register();
			
			//echo("Revisar".__DIR__);

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
				'driver' => 'pdo_mysql',
				'host' => 'localhost',
				'port' => '',
				'user' => 'root',
				'password' => 'adminoiti2019**',
				'dbname' => 'deparqueo_bd'
			);

			/*$connectionOptions = array(
			 	'driver' => 'pdo_ibm_i',
			 	'host' => '192.168.2.15',
			 	'port' => '50000',
			 	'user' => 'INPROWEB',
			 	'password' => '4c41npr0',
			 	'dbname' => 'BDINPRO'  
			 );*/

			/*$connectionOptions = array(
				'driver' => 'pdo_ibm_i',
				'host' => '192.168.3.120',
				'port' => '50000',
				'user' => '',
				'password' => '',
				'dbname' => 'DBSIANX'
			);*/

            try {
                self::$em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);
            }  
            catch (Exception $e){
            	echo "error de conexión";
            }

		}
		
        return self::$em;
    }
}
?>