<?php

namespace Walnut\Lib\Test\Data\DataType;

use PHPUnit\Framework\TestCase;
use Walnut\Lib\Data\DataType\ArrayData;
use Walnut\Lib\Data\Hydrator\CompositeValueHydrator;
use Walnut\Lib\Data\Exception\ArrayType\ArrayElementsNotUnique;
use Walnut\Lib\Data\Exception\ArrayType\TooFewArrayElements;
use Walnut\Lib\Data\Exception\ArrayType\TooManyArrayElements;
use Walnut\Lib\Data\Exception\InvalidData;
use Walnut\Lib\Data\Exception\InvalidValueRange;
use Walnut\Lib\Data\Exception\InvalidValueType;

final class ArrayDataTest extends TestCase {

	private CompositeValueHydrator $importer;

	protected function setUp(): void {
		$this->importer = $this->createMock(CompositeValueHydrator::class);
	}

	public function testEmptyArray(): void {
		$this->assertEquals([], (new ArrayData)->importValue([], $this->importer));
	}

	public function testInvalidValueRange(): void {
		$this->expectException(InvalidValueRange::class);
		(new ArrayData(minItems: 5, maxItems: 3));
	}

	public function testInvalidValueType(): void {
		$this->expectException(InvalidValueType::class);
		(new ArrayData)->importValue(5, $this->importer);
	}

	public function testInvalidArrayType(): void {
		$this->expectException(InvalidValueType::class);
		(new ArrayData)->importValue(['a' => 'b'], $this->importer);
	}

	public function testAllowNull(): void {
		$this->assertNull((new ArrayData(nullable: true))->importValue(null, $this->importer));
	}

	public function testDisallowNull(): void {
		$this->expectException(InvalidValueType::class);
		(new ArrayData)->importValue(null, $this->importer);
	}

	public function testTooFewOk(): void {
		$this->assertIsArray((new ArrayData(minItems: 3))->importValue([1, 2, 3], $this->importer));
		$this->assertIsArray((new ArrayData(minItems: 3))->importValue([1, 2, 3, 4], $this->importer));
	}

	public function testTooFewError(): void {
		$this->expectException(TooFewArrayElements::class);
		(new ArrayData(minItems: 3))->importValue([1, 2], $this->importer);
	}

	public function testTooManyOk(): void {
		$this->assertIsArray((new ArrayData(maxItems: 3))->importValue([1, 2, 3], $this->importer));
		$this->assertIsArray((new ArrayData(maxItems: 3))->importValue([1, 2], $this->importer));
	}

	public function testTooManyError(): void {
		$this->expectException(TooManyArrayElements::class);
		(new ArrayData(maxItems: 3))->importValue([1, 2, 3, 4], $this->importer);
	}

	public function testInRange(): void {
		$this->assertIsArray((new ArrayData(minItems: 1, maxItems: 3))->importValue([1, 2], $this->importer));
	}

	public function testUnique(): void {
		//No uniqueness requirement
		$this->assertIsArray((new ArrayData(uniqueItems: false))->importValue([1, 1, 2], $this->importer));
		//Unique elements
		$this->assertIsArray((new ArrayData(uniqueItems: true))->importValue([1, 2, 3], $this->importer));

	}

	public function testArrayElementsNotUnique(): void {
		$this->expectException(ArrayElementsNotUnique::class);
		(new ArrayData(uniqueItems: true))->importValue([1, 1, 2], $this->importer);
	}

	public function testItemsOk(): void {
		$this->importer->method('importNestedValue')->willReturn(1);
		$this->assertIsArray((new ArrayData)->importValue([1, 1, 2], $this->importer));
	}

	public function testInvalidItems(): void {
		$this->importer->method('importNestedValue')->willThrowException(new InvalidData("path", 'value', new InvalidValueType('a', 'b')));
		$this->expectException(InvalidData::class);
		(new ArrayData)->importValue([1, 2, 3], $this->importer);
	}

}
