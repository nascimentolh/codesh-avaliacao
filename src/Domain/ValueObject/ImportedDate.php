<?php

namespace App\Domain\ValueObject;

use DateTime;
use DateTimeImmutable;
use InvalidArgumentException;

class ImportedDate
{
    private DateTimeImmutable $value;

    private function __construct(DateTimeImmutable $value)
    {
        $this->value = $value;
    }

    public static function now(): self
    {
        return new self(new DateTimeImmutable());
    }

    public static function fromString(string $dateString): self
    {
        try {
            $date = new DateTimeImmutable($dateString);
            return new self($date);
        } catch (\Exception $e) {
            throw new InvalidArgumentException("Invalid date format: {$dateString}", 0, $e);
        }
    }

    public static function fromDateTime(DateTime $dateTime): self
    {
        return new self(DateTimeImmutable::createFromMutable($dateTime));
    }

    public function getValue(): DateTimeImmutable
    {
        return $this->value;
    }

    public function format(string $format = 'Y-m-d\TH:i:s\Z'): string
    {
        return $this->value->format($format);
    }

    public function __toString(): string
    {
        return $this->format();
    }

    public function equals(ImportedDate $other): bool
    {
        return $this->value == $other->value;
    }
}
