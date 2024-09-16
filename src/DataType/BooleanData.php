<?php

namespace Walnut\Lib\Data\DataType;

use Attribute;
use Walnut\Lib\Data\Exception\InvalidValueType;

/**
 * 
 * 
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class BooleanData implements DirectValue {
	public function __construct(
		public bool $nullable = false,
	) {}

	/**
	 * @throws InvalidValueType
	 */
	public function importValue(
		null|string|float|int|bool|array|object $value
	): ?bool {
		if (!is_bool($value) && !($value === null && $this->nullable)) {
			throw new InvalidValueType('bool', gettype($value));
		}
		return $value ?? null;
	}

}
