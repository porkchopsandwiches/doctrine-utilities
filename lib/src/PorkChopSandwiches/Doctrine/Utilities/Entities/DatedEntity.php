<?php

namespace PorkChopSandwiches\Doctrine\Utilities\Entities;

use Doctrine\ORM\Mapping as ORM;

abstract class DatedEntity extends Entity implements DatedEntityInterface {
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
		return $this -> preserialiseDateFields();
	}
}
