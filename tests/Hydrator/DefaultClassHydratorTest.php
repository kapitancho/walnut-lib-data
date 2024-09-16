<?php

namespace Walnut\Lib\Test\Data\Hydrator;

use PHPUnit\Framework\TestCase;
use Walnut\Lib\Data\Hydrator\ClassRefHydrator;
use Walnut\Lib\Data\Hydrator\DefaultClassHydrator;

/**
 * @package Walnut\Lib\DataType
 */
final class DefaultClassHydratorTest extends TestCase {

	public function testImportValue(): void {
		$refValueImporter = $this->createMock(ClassRefHydrator::class);
		$hydrator = new DefaultClassHydrator($refValueImporter);
		$this->assertIsObject($hydrator->importValue([
			'a' => 1,
			'b' => 2
		], \stdClass::class));
	}
}
