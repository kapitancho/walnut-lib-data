<?php

namespace Walnut\Lib\Data\DataType;

use Walnut\Lib\Data\Exception\InvalidValue;
use Walnut\Lib\Data\Hydrator\CompositeValueHydrator;

interface CompositeValue {
	/**
	 * @throws InvalidValue
	 */
	public function importValue(
		null|string|float|int|bool|array|object $value,
		CompositeValueHydrator $nestedValueHydrator
	): null|string|float|int|bool|array|object;
}
