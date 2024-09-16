<?php

namespace Walnut\Lib\Test\Data\DataType;

use PHPUnit\Framework\TestCase;
use Walnut\Lib\Data\Hydrator\ClassRefHydrator;
use Walnut\Lib\Data\Exception\InvalidValueType;
use Walnut\Lib\Data\DataType\RefValue;

final class RefValueTest extends TestCase {

	private ClassRefHydrator $importer;

	protected function setUp(): void {
		$this->importer = $this->createMock(ClassRefHydrator::class);
	}

	public function testAllowNull(): void {
		$this->assertNull((new RefValue(targetClass: \stdClass::class, nullable: true))->importValue(null, $this->importer));
	}

	public function testDisallowNull(): void {
		$this->expectException(InvalidValueType::class);
		(new RefValue(targetClass: \stdClass::class))->importValue(null, $this->importer);
	}

	public function testValues(): void {
		$this->assertIsObject((new RefValue(targetClass: \stdClass::class))->importValue(new \stdClass, $this->importer));
	}

}
