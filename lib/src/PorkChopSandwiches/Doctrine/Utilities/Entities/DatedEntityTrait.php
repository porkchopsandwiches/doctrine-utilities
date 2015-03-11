<?php

namespace PorkChopSandwiches\Doctrine\Utilities\Entities;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

trait DatedEntityTrait {

	/**
	 * @ORM\Column(type="utcdatetime", options={})
	 * @var DateTime
	 */
	protected $date_created;

	/**
	 * @ORM\Column(type="utcdatetime", options={})
	 * @var DateTime
	 */
	protected $date_updated;

	/**
	 * @param DateTime $date_updated
	 * @return $this
	 */
	public function setDateUpdated (DateTime $date_updated = null) {
		$date_updated = $date_updated instanceof DateTime ? $date_updated : new DateTime();
		$this -> date_updated = $date_updated;
		return $this;
	}

	/**
	 * @return DateTime
	 */
	public function getDateUpdated () {
		return $this -> date_updated;
	}

	/**
	 * @return DateTime
	 */
	public function getDateCreated () {
		return $this -> date_created;
	}

	protected function initialiseDateFields () {
		$this -> date_created = new DateTime;
		$this -> date_updated = new DateTime;
	}

	/**
	 * @param array	[$args]
	 *
	 * @return array
	 */
	protected function preserialiseDateFields (array $args = array()) {
		$data = array();

		if (Entity::argIsTrue($args, "entity.include_meta_dates")) {
			$data["date_created"] = $this -> date_created -> getTimestamp();
			$data["date_updated"] = $this -> date_updated -> getTimestamp();
		}

		return $data;
	}
}
