<?php

namespace Walnut\Lib\Data\DataType;

use Attribute;
use Walnut\Lib\Data\Exception\{InvalidValueRange};
use Walnut\Lib\Data\Exception\InvalidValueType;
use Walnut\Lib\Data\Exception\StringType\{StringTooLong};
use Walnut\Lib\Data\Exception\StringType\StringIncorrectlyFormatted;
use Walnut\Lib\Data\Exception\StringType\StringNotInEnum;
use Walnut\Lib\Data\Exception\StringType\StringTooShort;

#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class StringData implements DirectValue {
	private const DATE_REGEXP = '#^(\d+)-(0[1-9]|1[012])-(0[1-9]|[12]\d|3[01])T([01]\d|2[0-3])$#';
	private const DATE_TIME_REGEXP = '#^(\d+)-(0[1-9]|1[012])-(0[1-9]|[12]\d|3[01])T([01]\d|2[0-3]):([0-5]\d):([0-5]\d|60)(\.\d+)?(([Zz])|([\+|\-]?([01]\d|2[0-3])(\:[0-5]\d)?))$#';
	private const EMAIL_REGEXP = '#^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$#';
	public const FORMAT_DATE = 'date';
	public const FORMAT_DATE_TIME = 'date-time';
	public const FORMAT_EMAIL = 'email';

	/**
	 * @param bool $nullable
	 * @param int|null $minLength
	 * @param int|null $maxLength
	 * @param string|null $format
	 * @param string|null $pattern
	 * @param string[]|null $enum
	 */
	public function __construct(
		public bool $nullable = false,
		public ?int $minLength = null,
		public ?int $maxLength = null,
		public ?string $format = null,
		public ?string $pattern = null,
		public ?array $enum = null
	) {
		if (isset($this->minLength, $this->maxLength) && $this->minLength > $this->maxLength) {
			throw new InvalidValueRange($this->minLength, $this->maxLength);
		}
	}

	/**
	 * @throws InvalidValueType|StringTooShort|StringTooLong|StringIncorrectlyFormatted|StringNotInEnum
	 */
	public function importValue(
		null|string|float|int|bool|array|object $value
	): ?string {
		if (!is_string($value) && !($value === null && $this->nullable)) {
			throw new InvalidValueType('string', gettype($value));
		}
		if (!isset($value)) {
			return null;
		}
		$l = mb_strlen($value);
		$this->tooShort($l)->tooLong($l)->notInEnum($value)->wrongFormat($value);
		return $value;
	}

	/**
	 * @throws StringTooShort
	 */
	private function tooShort(int $l): self {
		if (isset($this->minLength) && $l < $this->minLength) {
			throw new StringTooShort($this->minLength, $l);
		}
		return $this;
	}

	/**
	 * @throws StringTooLong
	 */
	private function tooLong(int $l): self {
		if (isset($this->maxLength) && $l > $this->maxLength) {
			throw new StringTooLong($this->maxLength, $l);
		}
		return $this;
	}

	/**
	 * @throws StringNotInEnum
	 */
	private function notInEnum(string $value): self {
		if (isset($this->enum) && !in_array($value, $this->enum, true)) {
			throw new StringNotInEnum($this->enum, $value);
		}
		return $this;
	}

	/**
	 * @throws StringIncorrectlyFormatted
	 */
	private function wrongFormat(string $value): self {
		if (isset($this->format)) {
			$regexp = [
				self::FORMAT_DATE => self::DATE_REGEXP,
				self::FORMAT_DATE_TIME => self::DATE_TIME_REGEXP,
				self::FORMAT_EMAIL => self::EMAIL_REGEXP,
			][$this->format] ?? null;
			if ($regexp && !preg_match($regexp, $value)) {
				throw new StringIncorrectlyFormatted($this->format, $value);
			}
		}
		return $this;
	}

}
