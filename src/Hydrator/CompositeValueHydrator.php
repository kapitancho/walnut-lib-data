<?php

namespace Walnut\Lib\Data\Hydrator;

use Walnut\Lib\Data\DataType\ClassRef;
use Walnut\Lib\Data\DataType\CompositeValue;
use Walnut\Lib\Data\DataType\DirectValue;
use Walnut\Lib\Data\Exception\InvalidData;

interface CompositeValueHydrator {
	/**
	 * @throws InvalidData
	 */
	public function importNestedValue(
		null|string|float|int|bool|array|object $value,
		DirectValue|CompositeValue|ClassRef     $importer,
		string|int|null                         $key = null
	): null|string|float|int|bool|array|object;
}