<?php

namespace Walnut\Lib\Test\Data\DataType;

use PHPUnit\Framework\TestCase;
use Walnut\Lib\Data\DataType\EnumData;
use Walnut\Lib\Data\Hydrator\CompositeValueHydrator;
use Walnut\Lib\Data\DataType\EnumDataType;
use Walnut\Lib\Data\Exception\InvalidValueType;
use Walnut\Lib\Data\Exception\StringType\StringNotInEnum;

enum EnumDataUnit { case A; case C;}
enum EnumDataInt: int { case A = 1; case C = 3;}
enum EnumDataString: string { case A = 'z'; case C = 'x';}

final class EnumDataTest extends TestCase {

	public function testUnitEnum(): void {
		$enumData = new EnumData(EnumDataUnit::class, EnumDataType::UNIT, ['A', 'C']);
		$this->assertEquals(EnumDataUnit::C, $enumData->importValue('C'));
	}

	public function testIntEnum(): void {
		$enumData = new EnumData(EnumDataInt::class, EnumDataType::INT, [1, 3]);
		$this->assertEquals(EnumDataInt::C, $enumData->importValue(3));
	}

	public function testStringEnum(): void {
		$enumData = new EnumData(EnumDataString::class, EnumDataType::STRING, ['z', 'x']);
		$this->assertEquals(EnumDataString::C, $enumData->importValue('x'));
	}

	public function testInvalidUnitEnum(): void {
		$this->expectException(InvalidValueType::class);
		$enumData = new EnumData(EnumDataInt::class, EnumDataType::UNIT, ['A', 'C']);
		$enumData->importValue(2);
	}

	public function testInvalidUnitEnumValue(): void {
		$this->expectException(StringNotInEnum::class);
		$enumData = new EnumData(EnumDataInt::class, EnumDataType::UNIT, ['A', 'C']);
		$enumData->importValue('B');
	}

	public function testInvalidIntEnum(): void {
		$this->expectException(InvalidValueType::class);
		$enumData = new EnumData(EnumDataInt::class, EnumDataType::INT, [1, 3]);
		$enumData->importValue('x');
	}

	public function testInvalidIntEnumValue(): void {
		$this->expectException(StringNotInEnum::class);
		$enumData = new EnumData(EnumDataInt::class, EnumDataType::INT, [1, 3]);
		$enumData->importValue(2);
	}

	public function testInvalidStringEnum(): void {
		$this->expectException(InvalidValueType::class);
		$enumData = new EnumData(EnumDataString::class, EnumDataType::STRING, ['z', 'x']);
		$enumData->importValue(2);
	}

	public function testInvalidStringEnumValue(): void {
		$this->expectException(StringNotInEnum::class);
		$enumData = new EnumData(EnumDataString::class, EnumDataType::STRING, ['z', 'x']);
		$enumData->importValue('y');
	}

}