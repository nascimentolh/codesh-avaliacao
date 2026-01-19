<?php

namespace App\Domain\Entity;

use DateTimeImmutable;

class ImportHistory
{
    private const STATUS_RUNNING = 'running';
    private const STATUS_COMPLETED = 'completed';
    private const STATUS_FAILED = 'failed';

    private ?int $id;
    private string $filename;
    private int $productsImported;
    private DateTimeImmutable $startedAt;
    private ?DateTimeImmutable $completedAt;
    private string $status;
    private ?string $errorMessage;

    public function __construct(
        string $filename,
        int $productsImported = 0,
        ?DateTimeImmutable $startedAt = null,
        ?DateTimeImmutable $completedAt = null,
        string $status = self::STATUS_RUNNING,
        ?string $errorMessage = null,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->filename = $filename;
        $this->productsImported = $productsImported;
        $this->startedAt = $startedAt ?? new DateTimeImmutable();
        $this->completedAt = $completedAt;
        $this->status = $status;
        $this->errorMessage = $errorMessage;
    }

    public static function start(string $filename): self
    {
        return new self($filename, 0, new DateTimeImmutable(), null, self::STATUS_RUNNING);
    }

    public function complete(int $productsImported): void
    {
        $this->productsImported = $productsImported;
        $this->completedAt = new DateTimeImmutable();
        $this->status = self::STATUS_COMPLETED;
    }

    public function fail(string $errorMessage): void
    {
        $this->completedAt = new DateTimeImmutable();
        $this->status = self::STATUS_FAILED;
        $this->errorMessage = $errorMessage;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getProductsImported(): int
    {
        return $this->productsImported;
    }

    public function getStartedAt(): DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function getCompletedAt(): ?DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function isRunning(): bool
    {
        return $this->status === self::STATUS_RUNNING;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'filename' => $this->filename,
            'products_imported' => $this->productsImported,
            'started_at' => $this->startedAt->format('Y-m-d H:i:s'),
            'completed_at' => $this->completedAt?->format('Y-m-d H:i:s'),
            'status' => $this->status,
            'error_message' => $this->errorMessage,
        ];
    }
}
