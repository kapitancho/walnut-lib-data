<?php

namespace Walnut\Lib\Data\DataType;

use Attribute;
use Walnut\Lib\Data\Exception\{InvalidValueRange};
use Walnut\Lib\Data\Exception\InvalidValue;
use Walnut\Lib\Data\Exception\InvalidValueType;
use Walnut\Lib\Data\Exception\ObjectType\{RequiredObjectPropertyMissing};
use Walnut\Lib\Data\Exception\ObjectType\TooFewObjectProperties;
use Walnut\Lib\Data\Exception\ObjectType\TooManyObjectProperties;
use Walnut\Lib\Data\Exception\ObjectType\UnsupportedObjectPropertyFound;
use Walnut\Lib\Data\Hydrator\CompositeValueHydrator;

#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class ObjectData implements CompositeValue {
	/**
	 * ObjectValue constructor.
	 * @param bool $nullable
	 * @param string[] $required
	 * @param int|null $minProperties
	 * @param int|null $maxProperties
	 * @param array<string, DirectValue|CompositeValue|ClassRef> $properties
	 * @param DirectValue|CompositeValue|ClassRef|null $additionalProperties
	 */
	public function __construct(
		public bool                                     $nullable = false,
		public array                                    $required = [],
		public ?int                                     $minProperties = null,
		public ?int                                     $maxProperties = null,
		public array                                    $properties = [],
		public DirectValue|CompositeValue|ClassRef|null $additionalProperties = null,
	) {
		if (isset($this->minProperties, $this->maxProperties) && $this->minProperties > $this->maxProperties) {
			throw new InvalidValueRange($this->minProperties, $this->maxProperties);
		}		
	}

	/**
	 * @throws InvalidValueType|RequiredObjectPropertyMissing|TooFewObjectProperties|TooManyObjectProperties|UnsupportedObjectPropertyFound|InvalidValue
	 */
	public function importValue(
		null|string|float|int|bool|array|object $value,
		CompositeValueHydrator $nestedValueHydrator
	): ?object {
		$values = $this->validateAndCleanValue($value);
		return $values === null ? null :
			(object)$this->importValues($values, $nestedValueHydrator);
	}

	/**
	 * @param float|object|int|bool|array|string|null $value
	 * @return null|array<string, null|string|float|int|bool|array|object>
	 * @throws InvalidValueType|RequiredObjectPropertyMissing|TooFewObjectProperties|TooManyObjectProperties
	 */
	private function validateAndCleanValue(
		float|object|int|bool|array|string|null $value
	): ?array {
		$values = match(true) {
			is_object($value) => get_object_vars($value),
			is_array($value) && (!array_is_list($value) || $value === []) => $value,
			default => null
		};
		if ($values === null) {
			if ($value === null && $this->nullable) {
				return null;
			}
			throw new InvalidValueType('object', gettype($value));
		}
		$l = count($values);
		$this->tooFew($l)->tooMany($l)->requiredMissing($values);
		/**
		 * @var array<string, null|string|float|int|bool|array|object>
		 */
		return $values;
	}

	/**
	 * @param array<string, int|float|bool|string|array|object|null> $values
	 * @param CompositeValueHydrator $nestedValueImporter
	 * @return array<array-key, int|float|bool|string|array|object|null>
	 * @throws InvalidValue
	 */
	private function importValues(array $values, CompositeValueHydrator $nestedValueImporter): array {
		/**
		 * @var array<array-key, int|float|bool|string|array|object|null> $result
		 */
		$result = [];

		foreach($values as $prop => $value) {
			$propertyValueImporter = $this->properties[$prop] ?? $this->additionalProperties ??
				throw new UnsupportedObjectPropertyFound($prop);
			$importResult = $nestedValueImporter->importNestedValue($value, $propertyValueImporter, $prop);
			$result[$prop] = $importResult;
		}
		foreach($this->properties as $propertyName => $valueImporter) {
			if (!array_key_exists($propertyName, $result)) {
				$result[$propertyName] = $nestedValueImporter->importNestedValue(
					null, $valueImporter, $propertyName
				);
			}
		}
		return $result;
	}

	/**
	 * @throws TooFewObjectProperties
	 */
	private function tooFew(int $l): self {
		if (isset($this->minProperties) && $l < $this->minProperties) {
			throw new TooFewObjectProperties($this->minProperties, $l);
		}
		return $this;
	}

	/**
	 * @throws TooManyObjectProperties
	 */
	private function tooMany(int $l): self {
		if (isset($this->maxProperties) && $l > $this->maxProperties) {
			throw new TooManyObjectProperties($this->maxProperties, $l);
		}
		return $this;
	}

	/**
	 * @throws RequiredObjectPropertyMissing
	 */
	private function requiredMissing(array $value): self {
		foreach($this->required as $required) {
			if (!array_key_exists($required, $value)) {
				throw new RequiredObjectPropertyMissing($required);
			}
		}
		return $this;
	}

}
