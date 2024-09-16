<?php

namespace Walnut\Lib\Data\Exception\NumberType;

use Walnut\Lib\Data\Exception\InvalidValue;

final class NumberAboveMaximum extends InvalidValue {
	private const ERROR_MESSAGE = "The value '%f' cannot be%s over '%f'.";
	private const EQUAL_OR = " equal or";
	public function __construct(
		public readonly float $maximum,
		public readonly bool $exclusiveMaximum,
		public readonly float $actualValue
	) {
		parent::__construct();
	}

	public function __toString(): string {
		return sprintf(self::ERROR_MESSAGE, $this->actualValue,
			$this->exclusiveMaximum ? self::EQUAL_OR : '', $this->maximum);
	}
}
