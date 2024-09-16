<?php

namespace Walnut\Lib\Data\DataType;

use Walnut\Lib\Data\Exception\InvalidData;
use Walnut\Lib\Data\Hydrator\ClassRefHydrator;

interface ClassRef {
	/**
	 * @throws InvalidData
	 */
	public function importValue(
		null|string|float|int|bool|array|object $value,
		ClassRefHydrator $refValueHydrator
	): null|string|float|int|bool|array|object;
}



