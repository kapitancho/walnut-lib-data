<?php

namespace Walnut\Lib\Test\Data\DataType;

use PHPUnit\Framework\TestCase;
use Walnut\Lib\Data\DataType\BooleanData;
use Walnut\Lib\Data\Hydrator\CompositeValueHydrator;
use Walnut\Lib\Data\Exception\InvalidValueType;

final class BooleanDataTest extends TestCase {

	private CompositeValueHydrator $importer;

	protected function setUp(): void {
		$this->importer = $this->createMock(CompositeValueHydrator::class);
	}

	public function testTrue(): void {
		$this->assertTrue( (new BooleanData)->importValue(true, $this->importer));
	}

	public function testFalse(): void {
		$this->assertFalse((new BooleanData)->importValue(false, $this->importer));
	}

	public function testInvalidValueType(): void {
		$this->expectException(InvalidValueType::class);
		(new BooleanData)->importValue("TEST", $this->importer);
	}

	public function testAllowNull(): void {
		$this->assertNull((new BooleanData(nullable: true))->importValue(null, $this->importer));
	}

	public function testDisallowNull(): void {
		$this->expectException(InvalidValueType::class);
		(new BooleanData)->importValue(null, $this->importer);
	}

}
