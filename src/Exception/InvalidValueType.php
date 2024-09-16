<?php


namespace Walnut\Lib\Data\Exception;

final class InvalidValueType extends InvalidValue {
	private const ERROR_MESSAGE = "A value of type '%s' is expected. A value of type '%s' provided instead.";
	public function __construct(
		public readonly string $expectedType,
		public readonly string $actualType
	) {
		parent::__construct();
	}

	public function __toString(): string {
		return sprintf(self::ERROR_MESSAGE, $this->expectedType, $this->actualType);
	}
}