<?php

namespace Walnut\Lib\Data\Exception\NumberType;

use Walnut\Lib\Data\Exception\InvalidValue;

final class NumberNotMultipleOf extends InvalidValue {
	private const ERROR_MESSAGE = "The value '%f' should be a multiple of '%f'.";
	public function __construct(
		public readonly float $multipleOf,
		public readonly float $actualValue
	) {
		parent::__construct();
	}

	public function __toString(): string {
		return sprintf(self::ERROR_MESSAGE, $this->actualValue, $this->multipleOf);
	}
}
