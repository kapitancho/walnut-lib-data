<?php

namespace Walnut\Lib\Data\Exception\StringType;

use Walnut\Lib\Data\Exception\InvalidValue;

final class StringTooLong extends InvalidValue {
	private const ERROR_MESSAGE = "The length of the string '%d' is more than the maximal allowed length of '%d'.";
	public function __construct(
		public readonly int $maxLength,
		public readonly int $actualLength
	) {
		parent::__construct();
	}

	public function __toString(): string {
		return sprintf(self::ERROR_MESSAGE, $this->actualLength, $this->maxLength);
	}

}
