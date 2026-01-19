<?php

namespace App\Domain\ValueObject;

use InvalidArgumentException;

class ProductCode
{
    private int $value;

    public function __construct(int $value)
    {
        if ($value <= 0) {
            throw new InvalidArgumentException("Product code must be a positive integer");
        }
        $this->value = $value;
    }

    public static function fromInt(int $value): self
    {
        return new self($value);
    }

    public static function fromString(string $value): self
    {
        if (!is_numeric($value)) {
            throw new InvalidArgumentException("Product code must be numeric");
        }
        return new self((int)$value);
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public function equals(ProductCode $other): bool
    {
        return $this->value === $other->value;
    }
}
