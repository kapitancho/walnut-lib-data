<?php

namespace Walnut\Lib\Data\DataType;

use Attribute;
use Walnut\Lib\Data\Exception\InvalidData;
use Walnut\Lib\Data\Exception\InvalidValue;
use Walnut\Lib\Data\Exception\InvalidValueType;
use Walnut\Lib\Data\Hydrator\CompositeValueHydrator;

/**
 * 
 * 
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class AnyOfData implements CompositeValue {
	/**
	 * @var list<DirectValue|CompositeValue|ClassRef>
	 */
	private array $valueImporters;

	public function __construct(
		public bool                         $nullable,
		DirectValue|CompositeValue|ClassRef $valueImporter,
		DirectValue|CompositeValue|ClassRef ... $additionalValueImporters
	) {
		$this->valueImporters = [$valueImporter, ...$additionalValueImporters];
	}

	/**
	 * @throws InvalidValue
	 */
	public function importValue(
		null|string|float|int|bool|array|object $value,
		CompositeValueHydrator $nestedValueHydrator
	): null|string|float|int|bool|array|object {
		if ($value === null) {
			if ($this->nullable) {
				return null;
			}
			throw new InvalidValueType('AnyOf', gettype($value));
		}
		$exceptions = [];
		foreach($this->valueImporters as $valueImporter) {
			try {
				return $nestedValueHydrator->importNestedValue($value, $valueImporter);
			} catch (InvalidData $ex) {
				$exceptions[] = $ex;
			}
		}
		throw $exceptions[0]; //@TODO - all
	}
}