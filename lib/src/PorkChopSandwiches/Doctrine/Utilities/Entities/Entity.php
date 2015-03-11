<?php

namespace PorkChopSandwiches\Doctrine\Utilities\Entities;

use PorkChopSandwiches\Preserialiser\Preserialisable;

abstract class Entity implements Preserialisable {

	/**
	 * @param array		$args
	 * @param string	$key
	 *
	 * @return bool
	 */
	static public function argIsTrue (array &$args = array(), $key) {
		return array_key_exists($key, $args) && !!$args[$key];
	}

	/**
	 * @param $value
	 * @return int|null
	 */
	static protected function intOrNull ($value) {
		return is_null($value) ? $value : (int) $value;
	}

	/**
	 * @return string
	 */
	static public function getClassName () {
		return get_called_class();
	}
}
