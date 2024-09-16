<?php

namespace Walnut\Lib\Test\Data\Builder;

use PHPUnit\Framework\TestCase;
use Walnut\Lib\Data\DataType\ClassData;
use Walnut\Lib\Data\Hydrator\Builder\ClassDataBuilder;
use Walnut\Lib\Data\Hydrator\Builder\ClassDataBuilderCache;

final class ClassDataBuilderCacheTest extends TestCase {

	public function testCaching(): void {
		$classDataBuilder = $this->createMock(ClassDataBuilder::class);
		$classDataBuilder->expects($this->once())->method('buildForClass')
			->willReturn(new ClassData(\stdClass::class));
		$builder = new ClassDataBuilderCache($classDataBuilder);
		$builder->buildForClass(\stdClass::class);
		$builder->buildForClass(\stdClass::class);
	}
}
