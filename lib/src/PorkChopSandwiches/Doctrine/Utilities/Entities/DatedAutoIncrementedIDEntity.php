<?php

namespace PorkChopSandwiches\Doctrine\Utilities\Entities;

use Doctrine\ORM\Mapping as ORM;

class DatedAutoIncrementedIDEntity extends Entity implements AutoIncrementedIDEntityInterface, DatedEntityInterface {
	use AutoIncrementedIDEntityTrait;
	use DatedEntityTrait;

	public function __construct () {
		$this -> initialiseDateFields();
	}

	/**
	 * @param array	[$args]
	 *
	 * @return array
	 */
	public function preserialise (array $args = array()) {
		return array_merge($this -> preserialiseID(), $this -> preserialiseDateFields());
	}
}
