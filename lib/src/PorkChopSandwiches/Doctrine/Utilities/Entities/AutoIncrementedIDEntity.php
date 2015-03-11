<?php

namespace PorkChopSandwiches\Doctrine\Utilities\Entities;

abstract class AutoIncrementedIDEntity extends Entity implements AutoIncrementedIDEntityInterface {
	use AutoIncrementedIDEntityTrait;

	/**
	 * @param array	[$args]
	 *
	 * @return array
	 */
	public function preserialise (array $args = array()) {
		return $this -> preserialiseID();
	}
}
