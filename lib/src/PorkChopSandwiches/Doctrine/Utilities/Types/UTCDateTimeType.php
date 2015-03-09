<?php

namespace PorkChopSandwiches\Doctrine\Utilities\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\ConversionException;
use DateTime;
use DateTimeZone;

class UTCDateTimeType extends DateTimeType {
	static private $utc = null;

	/**
	 * @return DateTimeZone
	 */
	static private function getUTCTimeZone () {
		return self::$utc ? self::$utc : (self::$utc = new DateTimeZone("UTC"));
	}

	/**
	 * Converts a PHP-land value (a DateTime instance) to a Database string value.
	 *
	 * @param DateTime|null $value
	 * @param AbstractPlatform $platform
	 *
	 * @return string|null
	 */
	public function convertToDatabaseValue ($value, AbstractPlatform $platform) {
		if ($value === null) {
			return null;
		}

		$value -> setTimezone(self::getUTCTimeZone());
		return $value -> format($platform -> getDateTimeFormatString());
	}

	/**
	 * @param mixed $value
	 * @param AbstractPlatform $platform
	 *
	 * @return DateTime
	 *
	 * @throws ConversionException
	 */
	public function convertToPHPValue ($value, AbstractPlatform $platform) {
		if ($value === null) {
			return null;
		}

		$val = DateTime::createFromFormat($platform -> getDateTimeFormatString(), $value, self::getUTCTimeZone());

		if (!$val) {
			throw ConversionException::conversionFailed($value, $this -> getName());
		}

		return $val;
	}
}
