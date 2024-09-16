<?php

namespace Walnut\Lib\Test\Data\DataType;

use PHPUnit\Framework\TestCase;
use Walnut\Lib\Data\DataType\AnyData;
use Walnut\Lib\Data\DataType\AnyOfData;
use Walnut\Lib\Data\Hydrator\CompositeValueHydrator;
use Walnut\Lib\Data\Exception\InvalidData;
use Walnut\Lib\Data\Exception\InvalidValueType;

final class AnyOfDataTest extends TestCase {

	private CompositeValueHydrator $importer;

	protected function setUp(): void {
		$this->importer = $this->createMock(CompositeValueHydrator::class);
	}
	
	public function testAllowNull(): void {
		$this->assertNull((new AnyOfData(true, new AnyData))
			->importValue(null, $this->importer));
	}

	public function testDisallowNull(): void {
		$this->expectException(InvalidValueType::class);
		(new AnyOfData(false, new AnyData))->importValue(null, $this->importer);
	}

	public function testAllValid(): void {
		$this->importer->method('importNestedValue')->willReturn(null);
		$this->assertNull((new AnyOfData(false, new AnyData))->importValue(1, $this->importer));
	}

	public function testAllInvalid(): void {
		$this->expectException(InvalidData::class);
		$this->importer->method('importNestedValue')->willThrowException(new InvalidData("path", 'value', new InvalidValueType('a', 'b')));
		$this->assertNull((new AnyOfData(false, new AnyData))->importValue(1, $this->importer));
	}

}
