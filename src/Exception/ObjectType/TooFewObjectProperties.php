<?php

namespace Walnut\Lib\Data\Exception\ObjectType;

use Walnut\Lib\Data\Exception\InvalidValue;

final class TooFewObjectProperties extends InvalidValue {
	private const ERROR_MESSAGE = "The number of properties in the object '%d' is less than the minimal allowed '%d' properties.";
	public function __construct(
		public readonly int $minProperties,
		public readonly int $actualPropertiesCount
	) {
		parent::__construct();
	}

	public function __toString(): string {
		return sprintf(self::ERROR_MESSAGE, $this->actualPropertiesCount, $this->minProperties);
	}
}