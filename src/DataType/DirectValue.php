<?php

namespace Walnut\Lib\Data\DataType;

use Walnut\Lib\Data\Exception\InvalidValue;

interface DirectValue {
	/**
	 * @throws InvalidValue
	 */
	public function importValue(
		null|string|float|int|bool|array|object $value
	): null|string|float|int|bool|array|object;
}
