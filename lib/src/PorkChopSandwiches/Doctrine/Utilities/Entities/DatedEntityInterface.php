<?php

namespace PorkChopSandwiches\Doctrine\Utilities\Entities;

use DateTime;

interface DatedEntityInterface {

	/**
	 * @param DateTime $date_updated
	 * @return $this
	 */
	public function setDateUpdated (DateTime $date_updated = null);

	/**
	 * @return DateTime
	 */
	public function getDateUpdated ();

	/**
	 * @return DateTime
	 */
	public function getDateCreated ();
}
