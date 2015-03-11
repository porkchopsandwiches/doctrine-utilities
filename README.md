# doctrine-utilities
Common utilities for Doctrine-based projects, including a UTCDateTime column type and basic Entity classes.

## Entity Manager Generator
Convenience generator for Doctrine Entity Manager, useful in both `cli-config.php` and in the app proper.

Automatically registers the `utcdatetime` column type which stores a DateTime in UTC regardless of the timezone of the MySQL server or PHP environment.

```php
use PorkChopSandwiches\Doctrine\Utilities\EntityManager\Generator;

$entity_manager = Generator::manufacture(
	
	# The \Doctrine\DBAL\Connection instance or array
	$database_config, 
	
	# A \Doctrine\Common\Cache instance, for Query and MetaData caching
	$cache,
	
	# A \Doctrine\Common\Annotations\AnnotationReader instance
	$annotation_reader,
	
	# An array of directories to read Entity annotations from
	$entity_paths,
	
	# The proxy autogeneration behaviour
	AbstractProxyFactory::AUTOGENERATE_ALWAYS,
	
	# Whether to ensure production settings are on
	false,
	
	# Absolute path to Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php file
	ROOT_PATH . Generator::DOCTRINE_ANNOTATIONS_FILE_PATH,
	
	# PHP namespace for proxies
	"App\\Proxies",
	
	# Absolute path to directory where proxies will be generated
	ROOT_PATH . "/App/Proxies"
);
```

## Entity classes
Includes a basic `Entity` class, plus 3 extending utility classes:

1. `DatedEntity`, with `date_created` and `date_updated` UTC DateTime columns,
2. `AutoIncrementedIDEntity` with an auto-incrementing UNSIGNED INT `id` column, and
3. `DatedAutoIncrementedIDEntity` with both of the above.

### Basic entity

```php
use PorkChopSandwiches\Doctrine\Utilities\Entities\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 * 	name="sample_entities",
 *	uniqueConstraints={
 *	}
 * )
 */
class SampleEntity extends Entity {

	/**
	 * @ORM\Column(type="string", length=100, options={"default"=""})
	 * @ORM\Id
	 */
	private $label = "";

	/**
	 * @param array [$args]
	 * 
	 * @return array
	 */
	public function preserialise (array $args = array()) {
		return array(
			"label" => $this -> label
		);
	}
}
```

### Dated entity

```php
use PorkChopSandwiches\Doctrine\Utilities\Entities\DatedEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 * 	name="sample_dated_entities",
 *	uniqueConstraints={
 *	}
 * )
 */
class SampleDatedEntity extends DatedEntity {

	/**
	 * @ORM\Column(type="string", length=100, options={"default"=""})
	 * @ORM\Id
	 */
	private $label = "";

	/**
	 * @param array [$args]
	 * 
	 * @return array
	 */
	public function preserialise (array $args = array()) {
		return array_merge(parent::preserialise($args), array(
			"label" => $this -> label
		));
	}
}

...

$instance = new SampleDatedEntity;
$instance -> getDateCreated(); // => DateTime
$instance -> getDateUpdated(); // => DateTime
$instance -> setDateUpdated(new \DateTime);

```

Produces Doctrine SQL:

```sql
CREATE TABLE sample_dated_entities (`label` VARCHAR(100) DEFAULT '' NOT NULL, date_created DATETIME NOT NULL, date_updated DATETIME NOT NULL, PRIMARY KEY(`label`)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
```

### Auto-incrementing ID entity
An entity with an auto-incrementing `id` UNSIGNED INT column.

```php
use PorkChopSandwiches\Doctrine\Utilities\Entities\AutoIncrementedIDEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 * 	name="sample_aiid_entities",
 *	uniqueConstraints={
 *	}
 * )
 */
class SampleAutoIncrementedIDEntity extends AutoIncrementedIDEntity {

	/**
	 * @ORM\Column(type="string", length=100, options={"default"=""})
	 */
	private $label = "";

	public function preserialise (array $args = array()) {
		return array_merge(parent::preserialise($args), array(
			"label" => $this -> label
		));
	}
}

...

$instance = new SampleAutoIncrementedIDEntity;
$instance -> getID(); // int (or null if not yet flushed)

```

Produces Doctrine SQL:

```sql
CREATE TABLE sample_aiid_entities (id INT UNSIGNED AUTO_INCREMENT NOT NULL, `label` VARCHAR(100) DEFAULT '' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
```
