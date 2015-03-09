<?php

namespace PorkChopSandwiches\Doctrine\Utilities\EntityManager;

use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\DBAL\Connection;
use Doctrine\Common\Cache\Cache;
use Doctrine\DBAL\DriverManager;
use Doctrine\Common\Annotations\Reader;
use Exception;

/**
 * Class Generator
 *
 * @author Cam Morrow
 *
 * Provides an interface to generate an EntityManager instance, usable in both the Doctrine CLI and the site proper
 */
class Generator {

	/**
	 * Manufactures an EntityManager instance using the passed configuration.
	 *
	 * @param array|Connection		$conn
	 * @param Cache					$cache_driver
	 * @param Reader				$annotation_reader
	 * @param array					$entity_paths
	 * @param boolean				[$autogenerate_strategy]
	 * @param boolean				[$ensure_production_settings]
	 * @param string|null			[$root_path]
	 * @param string				[$proxy_namespace]
	 * @param string				[$proxy_dir]
	 *
	 * @throws Exception
	 *
	 * @return EntityManager
	 */
	static public function manufacture ($conn, Cache $cache_driver, Reader $annotation_reader, array $entity_paths, $autogenerate_strategy = false, $ensure_production_settings = false, $root_path = null, $proxy_namespace = "Doctrine\\Proxies", $proxy_dir = "/lib/src/Doctrine/Proxies") {
		# Let the IDE know that the annotation reader is of the expected type
		/** @var AnnotationReader $annotation_reader */

		$config				= new Configuration();

		if (is_null($root_path)) {
			if (defined("ROOT_PATH")) {
				$root_path = ROOT_PATH;
			} else {
				throw new Exception("No root path found and no ROOT_PATH constant set.");
			}
		}

		# Set up the Metadata Cache implementation -- this caches the scraped Metadata Configuration (i.e. the Annotations/XML/YAML) values
		# !!!WARNING!!! - Doctrine does NOT throw an error if it can't connect to MemCache, it just silently goes on without a cache. ALWAYS CHECK TO SEE IF CACHE IS BEING POPULATED ($cache_driver -> getStats())
		$config -> setMetadataCacheImpl($cache_driver);

		# Register the Annotation handle file for reasons that are not entirely clear
		AnnotationRegistry::registerFile($root_path . "/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php");

		# Set up the Metadata Driver implementation -- this tells Doctrine where to find the Annotated PHP classes to form Entities
		#$paths = require $root_path . "/config/entity_paths.php";
		$config -> setMetadataDriverImpl(new AnnotationDriver($annotation_reader, $entity_paths));

		# Set up the Query Cache implementation -- this caches DQL query transformations into plain SQL
		$config -> setQueryCacheImpl($cache_driver);

		# Set up the Proxy directory where Doctrine will store Proxy classes
		# Also set the namespace so the autoloader can find them(?)
		$config -> setProxyDir($root_path . $proxy_dir);
		$config -> setProxyNamespace($proxy_namespace);

		# Configure proxy generation
		$config -> setAutoGenerateProxyClasses($autogenerate_strategy);

		# Test production settings
		if ($ensure_production_settings) {
			$config -> ensureProductionSettings();
		}

		# If connection is just the raw details for the moment, generate the real deal
		if (is_array($conn)) {
			$conn = DriverManager::getConnection($conn, $config);
		}

		# Create the Entity Manager with the DB config details and ORM Config values
		$em = EntityManager::create($conn, $config);

		# Define our handy-dandy UTC Date Time column type
		if (!Type::hasType("utcdatetime")) {
			Type::addType("utcdatetime", "PorkChopSandwiches\\Doctrine\\Utilities\\Types\\UTCDateTimeType");
			$em -> getConnection() -> getDatabasePlatform() -> registerDoctrineTypeMapping("datetime", "utcdatetime");
		}

		return $em;
	}
}
