<?php

namespace Walnut\Lib\Data\Hydrator\Builder;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionEnum;
use ReflectionEnumBackedCase;
use ReflectionEnumUnitCase;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionUnionType;
use RuntimeException;
use Walnut\Lib\Data\DataType\AnyData;
use Walnut\Lib\Data\DataType\AnyOfData;
use Walnut\Lib\Data\DataType\ArrayData;
use Walnut\Lib\Data\DataType\BooleanData;
use Walnut\Lib\Data\DataType\ClassData;
use Walnut\Lib\Data\DataType\ClassRef;
use Walnut\Lib\Data\DataType\CompositeValue;
use Walnut\Lib\Data\DataType\CustomClassData;
use Walnut\Lib\Data\DataType\DirectValue;
use Walnut\Lib\Data\DataType\EnumData;
use Walnut\Lib\Data\DataType\EnumDataType;
use Walnut\Lib\Data\DataType\IntegerData;
use Walnut\Lib\Data\DataType\NumberData;
use Walnut\Lib\Data\DataType\ObjectData;
use Walnut\Lib\Data\DataType\RefValue;
use Walnut\Lib\Data\DataType\StringData;
use Walnut\Lib\Data\DataType\WrapperClassData;
use Walnut\Lib\Data\DataType\WrapperData;

final readonly class ReflectionClassDataBuilder implements ClassDataBuilder {
	/**
	 * @template T of object
	 * @param class-string<T> $className
	 * @return ClassData<T>|EnumData<T>|WrapperClassData<T>|CustomClassData<T>
	 * @throws ReflectionException
	 */
	public function buildForClass(string $className): ClassData|EnumData|WrapperClassData|CustomClassData {
		$r = new ReflectionClass($className);
		if ($r->isEnum()) {
			return $this->buildForEnum($className);
		}
		if ($r->getAttributes(WrapperData::class)) {
			$singleProperty = count($r->getProperties()) === 1 ? $r->getProperties()[0] : null;
			if ($singleProperty) {
				return new WrapperClassData(
					$className,
					$singleProperty->getName(),
					$this->getPropertyValueImporter($singleProperty)
				);
			}
		}
		if ($a = $r->getAttributes(DirectValue::class, ReflectionAttribute::IS_INSTANCEOF)) {
			return new CustomClassData($className, $a[0]->newInstance());
		}

		return new ClassData(
			$className,
			$this->getRequiredProperties($r),
			$this->getAllPropertyData($r)
		);
	}

	/**
	 * @template T of object
	 * @param class-string<T> $className
	 * @return EnumData<T>
	 * @throws ReflectionException
	 */
	private function buildForEnum(string $className): EnumData {
		$r = new ReflectionEnum($className);
		$cases = $r->getCases();
		if (!$cases) {
			throw new RuntimeException("Cannot import values from an empty Enum");
		}
		return new EnumData(
			$className,
			match(true) {
				!$r->isBacked() => EnumDataType::UNIT,
				(($backingType = $r->getBackingType()) instanceof ReflectionNamedType) &&
					$backingType->getName() === 'int' => EnumDataType::INT,
				default => EnumDataType::STRING
			},
			$r->isBacked() ?
				array_map(static fn(ReflectionEnumBackedCase $backedCase): int|string =>
				$backedCase->getBackingValue(), $cases) :
				array_map(static fn(ReflectionEnumUnitCase $unitCase): string =>
				$unitCase->getName(), $cases)
		);
	}

	/**
	 * @param ReflectionClass $reflectionClass
	 * @return string[]
	 */
	private function getRequiredProperties(ReflectionClass $reflectionClass): array {
		$result = [];

		/** @var array<string, ReflectionParameter> $constructorParameters */
		$constructorParameters = [];
		$constructor = $reflectionClass->getConstructor();
		if ($constructor) {
			foreach($constructor->getParameters() as $reflectionParameter) {
				$constructorParameters[$reflectionParameter->getName()] = $reflectionParameter;
			}
		}
		foreach($reflectionClass->getProperties() as $property) {
			if ($property->hasDefaultValue()) {
				continue;
			}
			$constructorParameter = $constructorParameters[$property->getName()] ?? null;
			if ($constructorParameter && $property->isPromoted() &&
					$constructorParameter->isDefaultValueAvailable()) {
				continue;
			}
			$result[] = $property->getName();
		}
		return $result;
	}

	/**
	 * @return array<string, DirectValue|CompositeValue|ClassRef>
	 */
	private function getAllPropertyData(ReflectionClass $reflectionClass): array {
		/**
		 * @var array<string, DirectValue|CompositeValue|ClassRef> $result
		 */
		$result = [];
		foreach($reflectionClass->getProperties() as $property) {
			$result[$property->getName()] = $this->getPropertyValueImporter($property);
		}
		return $result;
	}

	private function getPropertyValueImporter(ReflectionProperty $reflectionProperty): DirectValue|CompositeValue|ClassRef {
		$valueImporter =
			$reflectionProperty->getAttributes(DirectValue::class,ReflectionAttribute::IS_INSTANCEOF)[0] ??
			$reflectionProperty->getAttributes(CompositeValue::class,ReflectionAttribute::IS_INSTANCEOF)[0] ??
			$reflectionProperty->getAttributes(ClassRef::class,ReflectionAttribute::IS_INSTANCEOF)[0] ??
			null;
		/* *
		 * @var ?ObjectData $result
		 */
		$result = $valueImporter?->newInstance();
		return $result ?? $this->getDefaultImporter($reflectionProperty);
	}

	private function getDefaultImporter(ReflectionProperty $reflectionProperty): DirectValue|CompositeValue|ClassRef {
		$t = $reflectionProperty->getType();
		if ($t instanceof ReflectionUnionType) {
			$valuesImporters = array_map($this->getNamedTypeImporter(...), $t->getTypes());
			return new AnyOfData($t->allowsNull(), ...$valuesImporters);
		}
		if ($t instanceof ReflectionNamedType) {
			return $this->getNamedTypeImporter($t);
		}
		return new AnyData(true);
	}

	private function getNamedTypeImporter(ReflectionNamedType $t): DirectValue|CompositeValue|ClassRef {
		if (!$t->isBuiltin()) {
			$type = $t->getName();
			return new RefValue($type, nullable: $t->allowsNull());
		}
		return match($t->getName()) {
			'int' => new IntegerData(nullable: $t->allowsNull()),
			'float' => new NumberData(nullable: $t->allowsNull()),
			'string' => new StringData(nullable: $t->allowsNull()),
			'bool' => new BooleanData(nullable: $t->allowsNull()),
			'array' => new ArrayData(nullable: $t->allowsNull()),
			'object' => new ObjectData(nullable: $t->allowsNull(), additionalProperties: new AnyData()),
			default => new AnyData(nullable: true)
		};
	}

}