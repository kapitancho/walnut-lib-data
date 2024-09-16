<?php

namespace Walnut\Lib\Data\Hydrator;

use Walnut\Lib\Data\Exception\InvalidValue;

interface ClassHydrator {
	/**
	 * @template T of object
	 * @param null|string|float|int|bool|array|object $value
	 * @param class-string<T> $className
	 * @return T
	 * @throws InvalidValue
	 */
	public function importValue(
		null|string|float|int|bool|array|object $value,
		string $className
	): object;
}
