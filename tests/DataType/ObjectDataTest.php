<?php

namespace Walnut\Lib\Test\Data\DataType;

use PHPUnit\Framework\TestCase;
use Walnut\Lib\Data\DataType\AnyData;
use Walnut\Lib\Data\Hydrator\CompositeValueHydrator;
use Walnut\Lib\Data\Exception\InvalidData;
use Walnut\Lib\Data\Exception\InvalidValueRange;
use Walnut\Lib\Data\Exception\InvalidValueType;
use Walnut\Lib\Data\Exception\ObjectType\RequiredObjectPropertyMissing;
use Walnut\Lib\Data\Exception\ObjectType\TooFewObjectProperties;
use Walnut\Lib\Data\Exception\ObjectType\TooManyObjectProperties;
use Walnut\Lib\Data\Exception\ObjectType\UnsupportedObjectPropertyFound;
use Walnut\Lib\Data\DataType\ObjectData;

final class ObjectDataTest extends TestCase {

	private CompositeValueHydrator $importer;

	protected function setUp(): void {
		$this->importer = $this->createMock(CompositeValueHydrator::class);
	}

	public function testEmptyObject(): void {
		$this->assertEquals((object)[],
			(new ObjectData(additionalProperties: new AnyData))->importValue([], $this->importer));
	}

	public function testInvalidValueRange(): void {
		$this->expectException(InvalidValueRange::class);
		(new ObjectData(minProperties: 5, maxProperties: 3));
	}

	public function testInvalidValueType(): void {
		$this->expectException(InvalidValueType::class);
		(new ObjectData)->importValue(5, $this->importer);
	}

	public function testAllowNull(): void {
		$this->assertNull((new ObjectData(nullable: true))->importValue(null, $this->importer));
	}

	public function testDisallowNull(): void {
		$this->expectException(InvalidValueType::class);
		(new ObjectData)->importValue(null, $this->importer);
	}

	public function testTooFewOk(): void {
		$this->assertIsObject((new ObjectData(additionalProperties: new AnyData, minProperties: 3))->importValue((object)[1, 2, 3], $this->importer));
		$this->assertIsObject((new ObjectData(additionalProperties: new AnyData, minProperties: 3))->importValue((object)[1, 2, 3, 4], $this->importer));
	}

	public function testTooFewError(): void {
		$this->expectException(TooFewObjectProperties::class);
		(new ObjectData(additionalProperties: new AnyData, minProperties: 3))->importValue((object)[1, 2], $this->importer);
	}

	public function testTooManyOk(): void {
		$this->assertIsObject((new ObjectData(additionalProperties: new AnyData, maxProperties: 3))->importValue((object)[1, 2, 3], $this->importer));
		$this->assertIsObject((new ObjectData(additionalProperties: new AnyData, maxProperties: 3))->importValue((object)[1, 2], $this->importer));
	}

	public function testTooManyError(): void {
		$this->expectException(TooManyObjectProperties::class);
		(new ObjectData(additionalProperties: new AnyData, maxProperties: 3))->importValue((object)[1, 2, 3, 4], $this->importer);
	}

	public function testInRange(): void {
		$this->assertIsObject((new ObjectData(additionalProperties: new AnyData))->importValue((object)[1, 2], $this->importer));
		$this->assertIsObject((new ObjectData(additionalProperties: new AnyData, minProperties: 1, maxProperties: 3))->importValue((object)[1, 2], $this->importer));
	}

	public function testRequiredOk(): void {
		$this->assertIsObject((new ObjectData(additionalProperties: new AnyData, required: ['a', 'b']))->importValue((object)['a' => 1, 'b' => 2, 'c' => 3], $this->importer));
	}

	public function testRequiredError(): void {
		$this->expectException(RequiredObjectPropertyMissing::class);
		(new ObjectData(required: ['a', 'b']))->importValue((object)['a' => 1, 'c' => 3], $this->importer);
	}

	public function testRequiredNull(): void {
		$this->importer->method('importNestedValue')->willReturn(null);
		$this->assertIsObject((new ObjectData(properties: ['a' => new AnyData, 'b' => new AnyData, 'c' => new AnyData,
		], required: ['a']))->importValue((object)['a' => 1, 'c' => 3], $this->importer));
	}

	public function testUnsupportedObjectPropertyFound(): void {
		$this->expectException(UnsupportedObjectPropertyFound::class);
		(new ObjectData())->importValue((object)['a' => 1], $this->importer);
	}

	public function testItemsOk(): void {
		$this->importer->method('importNestedValue')->willReturn(1);
		$this->assertIsObject((new ObjectData(additionalProperties: new AnyData))->importValue((object)[1, 1, 2], $this->importer));
	}

	public function testInvalidItems(): void {
		$this->importer->method('importNestedValue')->willThrowException(new InvalidData("path", 'value', new InvalidValueType('a', 'b')));
		$this->expectException(InvalidData::class);
		(new ObjectData(properties: ['a' => new AnyData]))->importValue(['a' => 1], $this->importer);
	}

}
