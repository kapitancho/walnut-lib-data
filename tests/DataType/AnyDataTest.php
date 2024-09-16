<?php

namespace Walnut\Lib\Test\Data\DataType;

use PHPUnit\Framework\TestCase;
use Walnut\Lib\Data\DataType\AnyData;
use Walnut\Lib\Data\Hydrator\CompositeValueHydrator;
use Walnut\Lib\Data\Exception\InvalidValueType;

final class AnyDataTest extends TestCase {

	private CompositeValueHydrator $importer;

	protected function setUp(): void {
		$this->importer = $this->createMock(CompositeValueHydrator::class);
	}
	
	public function testAllowNull(): void {
		$this->assertNull((new AnyData(nullable: true))
			->importValue(null, $this->importer));
	}

	public function testDisallowNull(): void {
		$this->expectException(InvalidValueType::class);
		(new AnyData)->importValue(null, $this->importer);
	}

	public function testValues(): void {
		$this->expectNotToPerformAssertions();
		(new AnyData)->importValue(true, $this->importer);
		(new AnyData)->importValue(false, $this->importer);
		(new AnyData)->importValue(0, $this->importer);
		(new AnyData)->importValue(0.5, $this->importer);
		(new AnyData)->importValue("STRING", $this->importer);
		(new AnyData)->importValue([], $this->importer);
		(new AnyData)->importValue([1], $this->importer);
		(new AnyData)->importValue(new \stdClass, $this->importer);
	}

}
