<?php

namespace Walnut\Lib\Test\Data\DataType;

use PHPUnit\Framework\TestCase;
use Walnut\Lib\Data\Hydrator\CompositeValueHydrator;
use Walnut\Lib\Data\Exception\InvalidValueRange;
use Walnut\Lib\Data\Exception\InvalidValueType;
use Walnut\Lib\Data\Exception\NumberType\NumberAboveMaximum;
use Walnut\Lib\Data\Exception\NumberType\NumberBelowMinimum;
use Walnut\Lib\Data\Exception\NumberType\NumberNotMultipleOf;
use Walnut\Lib\Data\DataType\IntegerData;

final class IntegerDataTest extends TestCase {

	private CompositeValueHydrator $importer;

	protected function setUp(): void {
		$this->importer = $this->createMock(CompositeValueHydrator::class);
	}

	public function testInteger(): void {
		$this->assertEquals(987, (new IntegerData)->importValue(987, $this->importer));
	}

	public function testInvalidValueRange(): void {
		$this->expectException(InvalidValueRange::class);
		(new IntegerData(minimum: 5, maximum: 3));
	}

	public function testInvalidValueType(): void {
		$this->expectException(InvalidValueType::class);
		(new IntegerData)->importValue("TEST", $this->importer);
	}

	public function testAllowNull(): void {
		$this->assertNull((new IntegerData(nullable: true))->importValue(null, $this->importer));
	}

	public function testDisallowNull(): void {
		$this->expectException(InvalidValueType::class);
		(new IntegerData)->importValue(null, $this->importer);
	}

	public function testNumberBelowMinimumOk(): void {
		$this->assertIsInt((new IntegerData(minimum: 3))->importValue(3, $this->importer));
		$this->assertIsInt((new IntegerData(minimum: 3))->importValue(4, $this->importer));
		$this->assertIsInt((new IntegerData(minimum: 3, exclusiveMinimum: true))->importValue(4, $this->importer));
	}

	public function testNumberBelowMinimumError(): void {
		$this->expectException(NumberBelowMinimum::class);
		(new IntegerData(minimum: 3))->importValue(2, $this->importer);
	}

	public function testNumberBelowMinimumExclusiveError(): void {
		$this->expectException(NumberBelowMinimum::class);
		(new IntegerData(minimum: 3, exclusiveMinimum: true))->importValue(3, $this->importer);
	}

	public function testNumberAboveMaximumOk(): void {
		$this->assertIsInt((new IntegerData(maximum: 3))->importValue(3, $this->importer));
		$this->assertIsInt((new IntegerData(maximum: 3))->importValue(2, $this->importer));
		$this->assertIsInt((new IntegerData(maximum: 3, exclusiveMaximum: true))->importValue(2, $this->importer));
	}

	public function testNumberAboveMaximumError(): void {
		$this->expectException(NumberAboveMaximum::class);
		(new IntegerData(maximum: 3))->importValue(4, $this->importer);
	}

	public function testNumberAboveMaximumExclusiveError(): void {
		$this->expectException(NumberAboveMaximum::class);
		(new IntegerData(maximum: 3, exclusiveMaximum: true))->importValue(3, $this->importer);
	}

	public function testInRange(): void {
		$this->assertIsInt((new IntegerData(minimum: 1, maximum: 3))->importValue(2, $this->importer));
	}

	public function testNumberNotMultipleOfOk(): void {
		$this->assertIsInt((new IntegerData(multipleOf: 5))->importValue(15, $this->importer));
	}

	public function testNumberNotMultipleOfError(): void {
		$this->expectException(NumberNotMultipleOf::class);
		(new IntegerData(multipleOf: 5))->importValue(8, $this->importer);
	}

	//@TODO - test format

}
