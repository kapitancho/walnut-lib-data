<?php

namespace Walnut\Lib\Test\Data\DataType;

use PHPUnit\Framework\TestCase;
use Walnut\Lib\Data\Hydrator\CompositeValueHydrator;
use Walnut\Lib\Data\Exception\InvalidValueRange;
use Walnut\Lib\Data\Exception\InvalidValueType;
use Walnut\Lib\Data\Exception\NumberType\NumberAboveMaximum;
use Walnut\Lib\Data\Exception\NumberType\NumberBelowMinimum;
use Walnut\Lib\Data\Exception\NumberType\NumberNotMultipleOf;
use Walnut\Lib\Data\DataType\NumberData;

final class NumberDataTest extends TestCase {

	private CompositeValueHydrator $importer;

	protected function setUp(): void {
		$this->importer = $this->createMock(CompositeValueHydrator::class);
	}

	public function testNumber(): void {
		$this->assertEquals(3.14, (new NumberData)->importValue(3.14, $this->importer));
	}

	public function testInvalidValueRange(): void {
		$this->expectException(InvalidValueRange::class);
		(new NumberData(minimum: 5.5, maximum: 3.5));
	}

	public function testInvalidValueType(): void {
		$this->expectException(InvalidValueType::class);
		(new NumberData)->importValue("TEST", $this->importer);
	}

	public function testAllowNull(): void {
		$this->assertNull((new NumberData(nullable: true))->importValue(null, $this->importer));
	}

	public function testDisallowNull(): void {
		$this->expectException(InvalidValueType::class);
		(new NumberData)->importValue(null, $this->importer);
	}

	public function testNumberBelowMinimumOk(): void {
		$this->assertIsNumeric((new NumberData(minimum: 3))->importValue(3, $this->importer));
		$this->assertIsNumeric((new NumberData(minimum: 3))->importValue(4.5, $this->importer));
		$this->assertIsNumeric((new NumberData(minimum: 3, exclusiveMinimum: true))->importValue(4.5, $this->importer));
	}

	public function testNumberBelowMinimumError(): void {
		$this->expectException(NumberBelowMinimum::class);
		(new NumberData(minimum: 3))->importValue(2, $this->importer);
	}

	public function testNumberBelowMinimumExclusiveError(): void {
		$this->expectException(NumberBelowMinimum::class);
		(new NumberData(minimum: 3, exclusiveMinimum: true))->importValue(3, $this->importer);
	}

	public function testNumberAboveMaximumOk(): void {
		$this->assertIsNumeric((new NumberData(maximum: 3))->importValue(3, $this->importer));
		$this->assertIsNumeric((new NumberData(maximum: 3))->importValue(2.5, $this->importer));
		$this->assertIsNumeric((new NumberData(maximum: 3, exclusiveMaximum: true))->importValue(2.5, $this->importer));
	}

	public function testNumberAboveMaximumError(): void {
		$this->expectException(NumberAboveMaximum::class);
		(new NumberData(maximum: 3))->importValue(4, $this->importer);
	}

	public function testNumberAboveMaximumExclusiveError(): void {
		$this->expectException(NumberAboveMaximum::class);
		(new NumberData(maximum: 3, exclusiveMaximum: true))->importValue(3, $this->importer);
	}

	public function testInRange(): void {
		$this->assertIsNumeric((new NumberData(minimum: 1.5, maximum: 3.5))->importValue(2, $this->importer));
	}

	public function testNumberNotMultipleOfOk(): void {
		$this->assertIsNumeric((new NumberData(multipleOf: 5))->importValue(15, $this->importer));
	}

	public function testNumberNotMultipleOfError(): void {
		$this->expectException(NumberNotMultipleOf::class);
		(new NumberData(multipleOf: 5))->importValue(8, $this->importer);
	}

	//@TODO - test format

}
