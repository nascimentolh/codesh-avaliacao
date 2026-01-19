<?php

namespace App\Domain\ValueObject;

use InvalidArgumentException;

class ProductStatus
{
    private const DRAFT = 'draft';
    private const TRASH = 'trash';
    private const PUBLISHED = 'published';

    private const VALID_STATUSES = [
        self::DRAFT,
        self::TRASH,
        self::PUBLISHED,
    ];

    private string $value;

    private function __construct(string $value)
    {
        if (!in_array($value, self::VALID_STATUSES, true)) {
            throw new InvalidArgumentException(
                "Invalid status: {$value}. Valid statuses are: " . implode(', ', self::VALID_STATUSES)
            );
        }
        $this->value = $value;
    }

    public static function draft(): self
    {
        return new self(self::DRAFT);
    }

    public static function trash(): self
    {
        return new self(self::TRASH);
    }

    public static function published(): self
    {
        return new self(self::PUBLISHED);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isDraft(): bool
    {
        return $this->value === self::DRAFT;
    }

    public function isTrash(): bool
    {
        return $this->value === self::TRASH;
    }

    public function isPublished(): bool
    {
        return $this->value === self::PUBLISHED;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(ProductStatus $other): bool
    {
        return $this->value === $other->value;
    }
}
