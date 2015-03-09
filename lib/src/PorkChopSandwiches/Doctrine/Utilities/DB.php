<?php

namespace PorkChopSandwiches\Doctrine\Utilities;

use Doctrine\ORM\EntityManager;

/**
 * Class DB
 *
 * @author Cam Morrow
 */
class DB {

	/* @var EntityManager $entity_manager */
	protected $entity_manager;

	/**
	 * @param EntityManager $entity_manager
	 */
	public function __construct (EntityManager $entity_manager) {
		$this -> entity_manager = $entity_manager;
	}

	/**
	 * @return EntityManager
	 */
	public function getEntityManager () {
		return $this -> entity_manager;
	}

	/**
	 * Builds a sub-query for an IN statement with bound values.
	 *
	 * @example buildInQueryBindings(array("a", "b", "c"), "ex") => "(:ex_0, :ex_1, :ex_2)", and adds keys for 'ex_0', 'ex_1' and 'ex_2' to the bound parameters
	 *
	 * @param array			$values					The values to bind.
	 * @param string		[$prefix]				A prefix to apply to the bound parameter keys.
	 * @param array			[&$bound_parameters]	A reference to the array to inject the bindings on to.
	 *
	 * @return string								Contains the SQL query subsection, ex. "(:in_1, :in_2)"
	 */
	public function buildInQueryBindings ($values, $prefix = "in_", &$bound_parameters = array()) {

		$sql 				= "()";

		if (is_array($values) && count($values) > 0) {
			$sql = array();
			$values = array_values($values);

			foreach ($values as $index => $value) {
				$label_name = $prefix . "_" . $index;
				$sql[] = ":" . $label_name;
				$bound_parameters[$label_name] = $value;
			}

			$sql = "(" . implode(", ", $sql) . ")";
		}

		return $sql;
	}

	/**
	 * Performs an arbitrary SQL query.
	 *
	 * @author Cam Morrow
	 *
	 * @param string		$sql
	 * @param array			[$bound_parameters]
	 * @param bool			[$fetch]
	 *
	 * @return array|null
	 */
	public function query ($sql, array $bound_parameters = array(), $fetch = true) {

		$statement = $this -> entity_manager -> getConnection() -> prepare($sql);

		foreach ($bound_parameters as $parameter => $value) {
			$statement -> bindValue($parameter, $value);
		}

		$statement -> execute();

		if ($fetch) {
			return $statement -> fetchAll();
		} else {
			return null;
		}
	}

	/**
	 * Performs an arbitrary DQL query.
	 *
	 * @param string	$dql
	 * @param array		[$bound_parameters]
	 *
	 * @return array
	 */
	public function queryDQL ($dql, array $bound_parameters = array()) {
		$statement = $this -> entity_manager -> createQuery($dql);

		foreach ($bound_parameters as $parameter => $value) {
			$statement -> setParameter($parameter, $value);
		}

		return $statement -> getResult();
	}

	/**
	 * @param string	$table_name
	 * @param array		$columns
	 *
	 * @return string
	 */
	public function insert ($table_name, $columns) {
		$column_keys = array();
		foreach ($columns as $key => $value) {
			$column_keys[$key] = ":" . $key;
		}

		$query = "INSERT INTO " . $table_name . " (" . implode(", ", array_keys($column_keys)) . ") VALUES (" . implode(", ", array_values($column_keys)) . ")";
		$this -> query($query, $columns, false);
		return $this -> getLastInsertedID();
	}

	/**
	 * @return string
	 */
	public function getLastInsertedID () {
		return $this -> entity_manager -> getConnection() -> lastInsertId();
	}
}
