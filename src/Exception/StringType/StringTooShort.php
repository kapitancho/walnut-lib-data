<?php

namespace Walnut\Lib\Data\Exception\StringType;

use Walnut\Lib\Data\Exception\InvalidValue;

final class StringTooShort extends InvalidValue {
	private const ERROR_MESSAGE = "The length of the string '%d' is less than the minimal allowed length of '%d'.";
	public function __construct(
		public readonly int $minLength,
		public readonly int $actualLength
	) {
		parent::__construct();
	}

	public function __toString(): string {
		return sprintf(self::ERROR_MESSAGE, $this->actualLength, $this->minLength);
	}

}
