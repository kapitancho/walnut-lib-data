<?php

namespace Walnut\Lib\Data\DataType;

use Walnut\Lib\Data\Exception\InvalidValue;
use Walnut\Lib\Data\Hydrator\CompositeValueHydrator;

/** @template T of object */
final readonly class CustomClassData implements CompositeValue {
	/**
	 * @param class-string<T> $className
	 * @param DirectValue $propertyValue
	 */
	public function __construct(
		public string      $className,
		public DirectValue $propertyValue
	) {}

	/**
	 * @param string|float|int|bool|array|object|null $value
	 * @param CompositeValueHydrator $nestedValueHydrator
	 * @return T
	 * @throws InvalidValue
	 */
	public function importValue(
		null|string|float|int|bool|array|object $value,
		CompositeValueHydrator $nestedValueHydrator
	): null|string|float|int|bool|array|object {
		return $this->propertyValue->importValue($value);
	}


}
