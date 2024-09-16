<?php

namespace Walnut\Lib\Data\DataType;

use Attribute;
use Walnut\Lib\Data\Exception\{InvalidValueRange};
use Walnut\Lib\Data\Exception\InvalidValueType;
use Walnut\Lib\Data\Exception\NumberType\{NumberBelowMinimum};
use Walnut\Lib\Data\Exception\NumberType\NumberAboveMaximum;
use Walnut\Lib\Data\Exception\NumberType\NumberNotMultipleOf;

/**
 * 
 * 
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class NumberData implements DirectValue {
	public function __construct(
		public bool    $nullable = false,
		public ?float  $minimum = null,
		public bool    $exclusiveMinimum = false,
		public ?float  $maximum = null,
		public bool    $exclusiveMaximum = false,
		public ?float  $multipleOf = null,
		public ?string $format = null,
	) {
		if (isset($this->minimum, $this->maximum) && $this->minimum > $this->maximum) {
			throw new InvalidValueRange($this->minimum, $this->maximum);
		}
	}

	/**
	 * @throws InvalidValueType|NumberAboveMaximum|NumberBelowMinimum|NumberNotMultipleOf
	 */
	public function importValue(
		null|string|float|int|bool|array|object $value
	): null|float|int {
		$this->validateValue($value);
		return isset($value) ? (float)$value : null;
	}

	/**
	 * @throws InvalidValueType|NumberAboveMaximum|NumberBelowMinimum|NumberNotMultipleOf
	 */
	protected function validateValue(
		null|string|float|int|bool|array|object $value
	): void {
		if (!is_float($value) && !is_int($value) && !($value === null && $this->nullable)) {
			throw new InvalidValueType('double', gettype($value));
		}
		if (isset($value)) {
			$this->tooSmall($value)->tooLarge($value)->notMultipleOf($value);
		}
	}

	/**
	 * @throws NumberBelowMinimum
	 */
	private function tooSmall(int|float $value): self {
		if (isset($this->minimum)) {
			if ($value < $this->minimum || ((float)$value === $this->minimum && $this->exclusiveMinimum)) {
				throw new NumberBelowMinimum($this->minimum, $this->exclusiveMinimum, $value);
			}
		}
		return $this;
	}

	/**
	 * @throws NumberAboveMaximum
	 */
	private function tooLarge(int|float $value): self {
		if (isset($this->maximum)) {
			if ($value > $this->maximum || ((float)$value === $this->maximum && $this->exclusiveMaximum)) {
				throw new NumberAboveMaximum($this->maximum, $this->exclusiveMaximum, $value);
			}
		}
		return $this;
	}

	/**
	 * @throws NumberNotMultipleOf
	 */
	private function notMultipleOf(int|float $value): self {
		if (isset($this->multipleOf) && $value % $this->multipleOf) {
			throw new NumberNotMultipleOf($this->multipleOf, $value);
		}
		return $this;
	}

}
