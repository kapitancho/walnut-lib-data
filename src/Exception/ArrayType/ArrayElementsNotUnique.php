<?php

namespace Walnut\Lib\Data\Exception\ArrayType;

use Walnut\Lib\Data\Exception\InvalidValue;

final class ArrayElementsNotUnique extends InvalidValue {
	private const ERROR_MESSAGE = "All elements should be unique. '%d' of them are duplicated.";
	public function __construct(
		public readonly int $duplicatedCount
	) {
		parent::__construct();
	}

	public function __toString(): string {
		return sprintf(self::ERROR_MESSAGE, $this->duplicatedCount);
	}
}
