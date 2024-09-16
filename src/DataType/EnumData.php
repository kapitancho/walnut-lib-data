<?php

namespace Walnut\Lib\Data\DataType;

use ReflectionException;
use Walnut\Lib\Data\Exception\{ObjectType\RequiredObjectPropertyMissing, StringType\StringNotInEnum};
use Walnut\Lib\Data\Exception\InvalidValue;
use Walnut\Lib\Data\Exception\InvalidValueType;
use Walnut\Lib\Data\Exception\ObjectType\TooFewObjectProperties;
use Walnut\Lib\Data\Exception\ObjectType\TooManyObjectProperties;
use Walnut\Lib\Data\Exception\ObjectType\UnsupportedObjectPropertyFound;

/**
 * 
 * 
 * @template T of object
 */
final readonly class EnumData implements DirectValue {
	/**
	 * @param class-string<T> $className
	 * @param EnumDataType $type
	 * @param non-empty-list<string>|non-empty-list<int> $values
	 */
	public function __construct(
		public string       $className,
		public EnumDataType $type,
		public array        $values
	) {}

	/**
	 * @param string|float|int|bool|array|object|null $value
	 * @return T
	 * @throws InvalidValueType|RequiredObjectPropertyMissing|TooFewObjectProperties|TooManyObjectProperties|UnsupportedObjectPropertyFound|InvalidValue|ReflectionException
	 */
	public function importValue(
		null|string|float|int|bool|array|object $value
	): object {
		if ($this->type === EnumDataType::INT && !is_int($value)) {
			throw new InvalidValueType('int enum', gettype($value));
		}
		if ($this->type === EnumDataType::STRING && !is_string($value)) {
			throw new InvalidValueType('string enum', gettype($value));
		}
		if ($this->type === EnumDataType::UNIT && !is_string($value)) {
			throw new InvalidValueType('unit enum', gettype($value));
		}
		if ($this->type !== EnumDataType::UNIT) {
			return ($this->className)::tryFrom($value) ??
				throw new StringNotInEnum($this->values, (string)$value);
		}
		foreach(($this->className)::cases() as $case) {
			if ($case->name === $value) {
				return $case;
			}
		}
		throw new StringNotInEnum($this->values, $value);
	}

}
