<?php

namespace PorkChopSandwiches\Doctrine\Utilities\Entities;

use Doctrine\ORM\Mapping as ORM;

trait AutoIncrementedIDEntityTrait {

	/**
	 * @var int $id
	 * @ORM\Column(type="integer", length=10, options={"unsigned"=true})
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @return int
	 */
	public function getID () {
		return $this -> id;
	}

	/**
	 * @return string
	 */
	public function getIdentifier () {
		return get_called_class() . "#" . $this -> id;
	}

	/**
	 *
	 * @return array
	 */
	protected function preserialiseID () {
		$data = array(
			"id"	=> $this -> id
		);

		return $data;
	}
}
