<?php

namespace Walnut\Lib\Data\Hydrator;

use Walnut\Lib\Data\Exception\InvalidData;

interface ClassRefHydrator {
	/**
	 * @template T of object
	 * @param string|float|int|bool|array|object|null $value
	 * @param class-string<T> $targetClass
	 * @return T
	 * @throws InvalidData
	 */
	public function importRefValue(
		null|string|float|int|bool|array|object $value,
		string $targetClass
	): object;
}



