<?php


namespace Walnut\Lib\Data\Exception;

use OutOfBoundsException;

final class InvalidValueRange extends OutOfBoundsException {
	private const ERROR_MESSAGE = "The range '%f' - '%f' is invalid.";
	public function __construct(
		public readonly int|float $rangeMin,
		public readonly int|float $rangeMax
	) {
		parent::__construct();
	}

	public function __toString(): string {
		return sprintf(self::ERROR_MESSAGE,  $this->rangeMin, $this->rangeMax);
	}
}