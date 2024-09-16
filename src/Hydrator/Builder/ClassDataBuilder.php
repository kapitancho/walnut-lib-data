<?php

namespace Walnut\Lib\Data\Hydrator\Builder;

use Walnut\Lib\Data\DataType\ClassData;
use Walnut\Lib\Data\DataType\CustomClassData;
use Walnut\Lib\Data\DataType\EnumData;
use Walnut\Lib\Data\DataType\WrapperClassData;

interface ClassDataBuilder {
	/**
	 * @template T
	 * @param class-string<T> $className
	 * @return ClassData<T>|EnumData<T>|WrapperClassData<T>|CustomClassData<T>
	 */
	public function buildForClass(string $className): ClassData|EnumData|WrapperClassData|CustomClassData;
}