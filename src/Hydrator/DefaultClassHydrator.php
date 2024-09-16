<?php

namespace Walnut\Lib\Data\Hydrator;

final readonly class DefaultClassHydrator implements ClassHydrator {
	public function __construct(
		private ClassRefHydrator $refValueImporter
	) {}

	/**
	 * @template T of object
	 * @param null|string|float|int|bool|array|object $value
	 * @param class-string<T> $className
	 * @return T
	 */
	public function importValue(
		null|string|float|int|bool|array|object $value,
		string $className
	): object {
		return $this->refValueImporter->importRefValue($value, $className);
	}
}
