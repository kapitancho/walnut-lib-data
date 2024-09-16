<?php

namespace Walnut\Lib\Test\Data\DataType;

use PHPUnit\Framework\TestCase;
use Walnut\Lib\Data\DataType\AnyData;
use Walnut\Lib\Data\DataType\ClassData;
use Walnut\Lib\Data\Hydrator\CompositeValueHydrator;
use Walnut\Lib\Data\Exception\InvalidValueType;
use Walnut\Lib\Data\Exception\ObjectType\RequiredObjectPropertyMissing;
use Walnut\Lib\Data\Exception\ObjectType\UnsupportedObjectPropertyFound;
use Walnut\Lib\Data\DataType\IntegerData;
use Walnut\Lib\Data\DataType\WrapperClassData;

final class WrapperClassDataTestMockInteger {
	public function __construct(
		public int $value
	) {}
}

final class WrapperClassDataTest extends TestCase {

	private CompositeValueHydrator $importer;

	protected function setUp(): void {
		$this->importer = $this->createMock(CompositeValueHydrator::class);
	}

	public function testEmptyObject(): void {
		$this->importer->method('importNestedValue')->willReturn(3);
		$obj = (new WrapperClassData(
			WrapperClassDataTestMockInteger::class,
			'value',
			new IntegerData
		))->importValue(3, $this->importer);
		$this->assertInstanceOf(WrapperClassDataTestMockInteger::class, $obj);
		$this->assertEquals(3, $obj->value);
	}

	public function testInvalidValueType(): void {
		$this->expectException(InvalidValueType::class);
		$this->importer->method('importNestedValue')->willThrowException(
			new InvalidValueType('a', 'b')
		);
		(new WrapperClassData(
			WrapperClassDataTestMockInteger::class,
			'value',
			new IntegerData
		))->importValue(3, $this->importer);
	}

	/*
	public function testRequiredOk(): void {
		$this->assertIsObject((new ClassData(additionalProperties: new AnyData, required: ['a', 'b']))->importValue((object)['a' => 1, 'b' => 2, 'c' => 3], $this->importer));
	}
	*/
	public function testRequiredError(): void {
		$this->importer->method('importNestedValue')->willReturn(1);
		$this->expectException(RequiredObjectPropertyMissing::class);
		(new ClassData((new class(1, 2) {
			public function __construct(
				public readonly int $a,
				public readonly int $b,
			) {}
		})::class,
		properties: ['a' => new AnyData, 'b' => new AnyData],
		required: ['a', 'b']
		))->importValue((object)['a' => 1], $this->importer);
	}
	/*
	public function testRequiredNull(): void {
		$this->importer->method('importNestedValue')->willReturn(null);
		$this->assertIsObject((new ClassData(properties: ['a' => new AnyData, 'b' => new AnyData, 'c' => new AnyData,
		], required: ['a']))->importValue((object)['a' => 1, 'c' => 3], $this->importer));
	}
	*/
	public function testUnsupportedObjectPropertyFound(): void {
		$this->expectException(UnsupportedObjectPropertyFound::class);
		(new ClassData(\stdClass::class))->importValue((object)['a' => 1], $this->importer);
	}
	/*
	public function testItemsOk(): void {
		$this->importer->method('importNestedValue')->willReturn(1);
		$this->assertIsObject((new ClassData(additionalProperties: new AnyData))->importValue((object)[1, 1, 2], $this->importer));
	}

	public function testInvalidItems(): void {
		$this->importer->method('importNestedValue')->willThrowException(new InvalidData("path", 'value', new InvalidValueType('a', 'b')));
		$this->expectException(InvalidData::class);
		(new ClassData(properties: ['a' => new AnyData]))->importValue(['a' => 1], $this->importer);
	}
	*/

}
