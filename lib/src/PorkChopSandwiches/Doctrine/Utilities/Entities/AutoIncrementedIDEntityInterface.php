<?php

namespace PorkChopSandwiches\Doctrine\Utilities\Entities;

use PorkChopSandwiches\Doctrine\Utilities\IdentifiableInterface;

interface AutoIncrementedIDEntityInterface extends IdentifiableInterface {

	/**
	 * @return int
	 */
	public function getID ();
}
