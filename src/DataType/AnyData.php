<?php

namespace Walnut\Lib\Data\DataType;

use Attribute;
use Walnut\Lib\Data\Exception\InvalidValueType;

#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class AnyData implements DirectValue {
	public function __construct(
		public bool $nullable = false,
	) {}

	/**
	 * @throws InvalidValueType
	 */
	public function importValue(
		null|string|float|int|bool|array|object $value
	): null|string|float|int|bool|array|object {
		if ($value === null && !$this->nullable) {
			throw new InvalidValueType('mixed', gettype($value));
		}
		return $value;
	}
}
