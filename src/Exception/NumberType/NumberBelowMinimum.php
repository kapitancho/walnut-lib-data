<?php

namespace Walnut\Lib\Data\Exception\NumberType;

use Walnut\Lib\Data\Exception\InvalidValue;

final class NumberBelowMinimum extends InvalidValue {
	private const ERROR_MESSAGE = "The value '%f' cannot be%s less than '%f'.";
	private const EQUAL_OR = " equal or";
	public function __construct(
		public readonly float $minimum,
		public readonly bool $exclusiveMinimum,
		public readonly float $actualValue
	) {
		parent::__construct();
	}

	public function __toString(): string {
		return sprintf(self::ERROR_MESSAGE, $this->actualValue,
			$this->exclusiveMinimum ? self::EQUAL_OR : '', $this->minimum);
	}
}