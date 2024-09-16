<?php

namespace Walnut\Lib\Test\Data\DataType;

use PHPUnit\Framework\TestCase;
use Walnut\Lib\Data\Hydrator\CompositeValueHydrator;
use Walnut\Lib\Data\Exception\InvalidValueRange;
use Walnut\Lib\Data\Exception\InvalidValueType;
use Walnut\Lib\Data\Exception\StringType\StringIncorrectlyFormatted;
use Walnut\Lib\Data\Exception\StringType\StringNotInEnum;
use Walnut\Lib\Data\Exception\StringType\StringTooLong;
use Walnut\Lib\Data\Exception\StringType\StringTooShort;
use Walnut\Lib\Data\DataType\StringData;

final class StringDataTest extends TestCase {

	private CompositeValueHydrator $importer;

	protected function setUp(): void {
		$this->importer = $this->createMock(CompositeValueHydrator::class);
	}

	public function testInvalidValueRange(): void {
		$this->expectException(InvalidValueRange::class);
		(new StringData(minLength: 5, maxLength: 3));
	}

	public function testInvalidValueType(): void {
		$this->expectException(InvalidValueType::class);
		(new StringData)->importValue(5, $this->importer);
	}

	public function testString(): void {
		$this->assertEquals('test', (new StringData)->importValue('test', $this->importer));
	}

	public function testAllowNull(): void {
		$this->assertNull((new StringData(nullable: true))->importValue(null, $this->importer));
	}

	public function testDisallowNull(): void {
		$this->expectException(InvalidValueType::class);
		(new StringData)->importValue(null, $this->importer);
	}

	public function testTooFewOk(): void {
		$this->assertIsString((new StringData(minLength: 3))->importValue("123", $this->importer));
		$this->assertIsString((new StringData(minLength: 3))->importValue("1234", $this->importer));
	}

	public function testTooFewError(): void {
		$this->expectException(StringTooShort::class);
		(new StringData(minLength: 3))->importValue("12", $this->importer);
	}

	public function testTooManyOk(): void {
		$this->assertIsString((new StringData(maxLength: 3))->importValue("123", $this->importer));
		$this->assertIsString((new StringData(maxLength: 3))->importValue("12", $this->importer));
	}

	public function testTooManyError(): void {
		$this->expectException(StringTooLong::class);
		(new StringData(maxLength: 3))->importValue("1234", $this->importer);
	}

	public function testInRange(): void {
		$this->expectNotToPerformAssertions();
		(new StringData(minLength: 1, maxLength: 3))->importValue("12", $this->importer);
	}

	public function testInEnum(): void {
		$this->expectNotToPerformAssertions();
		//No uniqueness requirement
		(new StringData(enum: ["1", "2", "3"]))->importValue("1", $this->importer);
	}

	public function testNotInEnum(): void {
		$this->expectException(StringNotInEnum::class);
		(new StringData(enum: ["1", "2", "3"]))->importValue("4", $this->importer);
	}

	public function testDateTimeFormatOk(): void {
		//No uniqueness requirement
		$this->assertIsString((new StringData(format: StringData::FORMAT_DATE_TIME))->importValue("2000-01-01T12:00:00Z", $this->importer));
	}

	public function testDateTimeFormatError(): void {
		$this->expectException(StringIncorrectlyFormatted::class);
		(new StringData(format: StringData::FORMAT_DATE_TIME))->importValue("2000-01-03", $this->importer);
	}

	public function testSkipUnknownFormat(): void {
		//No uniqueness requirement
		$this->assertIsString((new StringData(format: 'unknown'))->importValue("2000-01-01T12:00:00Z", $this->importer));
	}

	//@TODO - pattern

}
