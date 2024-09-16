<?php

namespace Walnut\Lib\Data\Exception\ArrayType;

use Walnut\Lib\Data\Exception\InvalidValue;

final class TooFewArrayElements extends InvalidValue {
	private const ERROR_MESSAGE = "The number of elements in the array '%d' is less than the minimal required '%d' elements.";
	public function __construct(
		public readonly int $minItems,
		public readonly int $actualItemsCount
	) {
		parent::__construct();
	}

	public function __toString(): string {
		return sprintf(self::ERROR_MESSAGE, $this->actualItemsCount, $this->minItems);
	}

}
