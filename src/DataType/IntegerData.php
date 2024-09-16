<?php

namespace Walnut\Lib\Data\DataType;

use Attribute;
use Walnut\Lib\Data\Exception\InvalidValueType;
use Walnut\Lib\Data\Exception\NumberType\{NumberBelowMinimum};
use Walnut\Lib\Data\Exception\NumberType\NumberAboveMaximum;
use Walnut\Lib\Data\Exception\NumberType\NumberNotMultipleOf;

/**
 * 
 * 
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class IntegerData extends NumberData {
	/**
	 * @throws InvalidValueType|NumberAboveMaximum|NumberBelowMinimum|NumberNotMultipleOf
	 */
	public function importValue(
		null|string|float|int|bool|array|object $value
	): ?int {
		if (!is_int($value) && !($value === null && $this->nullable)) {
			throw new InvalidValueType('integer', gettype($value));
		}
		$this->validateValue($value);
		return $value ?? null;
	}
}
