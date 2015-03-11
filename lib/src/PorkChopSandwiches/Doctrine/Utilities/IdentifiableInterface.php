<?php

namespace PorkChopSandwiches\Doctrine\Utilities;

/**
 * An interface guaranteeing to provide a method that can be used as a comparison between two objects to see if they represent the same data.
 *
 * Used (for example) to compare to objects that may represent the same Database entity.
 */
interface IdentifiableInterface {

	/**
	 * Should return a globally unique identifier of the object, for example the database table and unique ID of a DB entity.
	 *
	 * @return mixed
	 */
	public function getIdentifier ();
}
