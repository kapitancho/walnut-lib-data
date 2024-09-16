<?php

namespace Walnut\Lib\Data\DataType;

use ReflectionClass;
use ReflectionException;
use Walnut\Lib\Data\Hydrator\CompositeValueHydrator;

/** @template T of object */
final readonly class WrapperClassData implements CompositeValue {
	/**
	 * @param class-string<T> $className
	 * @param string $propertyName
	 * @param DirectValue|CompositeValue|ClassRef $propertyValue
	 */
	public function __construct(
		public string                              $className,
		public string                              $propertyName,
		public DirectValue|CompositeValue|ClassRef $propertyValue
	) {}

	/**
	 * @param string|float|int|bool|array|object|null $value
	 * @param CompositeValueHydrator $nestedValueHydrator
	 * @return T
	 * @throws ReflectionException
	 */
	public function importValue(
		null|string|float|int|bool|array|object $value,
		CompositeValueHydrator $nestedValueHydrator
	): object {
		$value = $nestedValueHydrator->importNestedValue($value, $this->propertyValue);
		$c = new ReflectionClass($this->className);
		return $c->getConstructor()?->getParameters()[0]?->isVariadic() ? 
			$c->newInstanceArgs($value) : $c->newInstance($value);			
	}

}
