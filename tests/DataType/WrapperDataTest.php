<?php

namespace Walnut\Lib\Test\Data\DataType;

use PHPUnit\Framework\TestCase;
use Walnut\Lib\Data\DataType\AnyData;
use Walnut\Lib\Data\Hydrator\CompositeValueHydrator;
use Walnut\Lib\Data\Exception\InvalidValueType;
use Walnut\Lib\Data\DataType\WrapperData;

final class WrapperDataTest extends TestCase {

	public function testOk(): void {
		$this->assertInstanceOf(WrapperData::class, new WrapperData);
	}

}
