<?php

namespace Walnut\Lib\Data\Hydrator;

use Walnut\Lib\Data\DataType\ClassRef;
use Walnut\Lib\Data\DataType\CompositeValue;
use Walnut\Lib\Data\DataType\DirectValue;
use Walnut\Lib\Data\Exception\InvalidData;
use Walnut\Lib\Data\Exception\InvalidValue;
use Walnut\Lib\Data\Hydrator\Builder\ClassDataBuilder;

final readonly class DefaultImporter implements CompositeValueHydrator, ClassRefHydrator {

	public function __construct(
		private ClassDataBuilder $classDataBuilder,
		private string           $importPath
	) {}

	private function buildImportPath(string|int|null $pathAddition): string {
		$addition = '';
		if (isset($pathAddition)) {
			$addition = is_int($pathAddition) ? "[$pathAddition]" : ".$pathAddition";
		}
		return $this->importPath . $addition;
	}

	/**
	 * @throws InvalidData
	 */
	public function importNestedValue(
		null|string|float|int|bool|array|object $value,
		DirectValue|CompositeValue|ClassRef     $importer,
		string|int|null                         $key = null
	): null|string|float|int|bool|array|object {
		$currentPath = $this->buildImportPath($key);
		try {
			return $importer->importValue($value,
				new self($this->classDataBuilder, $currentPath));
		} catch (InvalidValue $ex) {
			throw new InvalidData($currentPath, $value,$ex);
		}
	}

	/**
	 * @template T of object
	 * @param string|float|int|bool|array|object|null $value
	 * @param class-string<T> $targetClass
	 * @return T
	 * @throws InvalidData
	 */
	public function importRefValue(
		null|string|float|int|bool|array|object $value, string $targetClass
	): object {
		/**
		 * @var T
		 */
		return $this->importNestedValue($value, $this->classDataBuilder->buildForClass($targetClass));
	}
}



