<?php

namespace Walnut\Lib\Data\DataType;

use Attribute;
use Walnut\Lib\Data\Exception\InvalidValue;
use Walnut\Lib\Data\Exception\InvalidValueType;
use Walnut\Lib\Data\Hydrator\ClassRefHydrator;

/**
 * 
 * 
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class RefValue implements ClassRef {
	/**
	 * @param class-string $targetClass
	 */
	public function __construct(
		public string $targetClass,
		public bool   $nullable = false
	) {}

	/**
	 * @throws InvalidValue
	 */
	public function importValue(
		null|string|float|int|bool|array|object $value,
		ClassRefHydrator $refValueHydrator
	): ?object {
		if ($value === null && !$this->nullable) {
			throw new InvalidValueType('object', gettype($value));
		}
		if (!isset($value)) {
			return null;
		}
		return $refValueHydrator->importRefValue($value, $this->targetClass);
	}

}
