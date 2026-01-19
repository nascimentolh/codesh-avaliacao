<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\ImportHistory;
use App\Domain\Repository\ImportHistoryRepositoryInterface;
use DateTimeImmutable;
use PDO;

class SQLiteImportHistoryRepository implements ImportHistoryRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(ImportHistory $history): void
    {
        $sql = <<<SQL
        INSERT INTO import_history (
            filename, products_imported, started_at, completed_at, status, error_message
        ) VALUES (
            :filename, :products_imported, :started_at, :completed_at, :status, :error_message
        )
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':filename', $history->getFilename(), PDO::PARAM_STR);
        $stmt->bindValue(':products_imported', $history->getProductsImported(), PDO::PARAM_INT);
        $stmt->bindValue(':started_at', $history->getStartedAt()->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->bindValue(':completed_at', $history->getCompletedAt()?->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->bindValue(':status', $history->getStatus(), PDO::PARAM_STR);
        $stmt->bindValue(':error_message', $history->getErrorMessage(), PDO::PARAM_STR);
        $stmt->execute();
    }

    public function update(ImportHistory $history): void
    {
        if ($history->getId() === null) {
            throw new \RuntimeException('Cannot update import history without ID');
        }

        $sql = <<<SQL
        UPDATE import_history SET
            filename = :filename,
            products_imported = :products_imported,
            started_at = :started_at,
            completed_at = :completed_at,
            status = :status,
            error_message = :error_message
        WHERE id = :id
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $history->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':filename', $history->getFilename(), PDO::PARAM_STR);
        $stmt->bindValue(':products_imported', $history->getProductsImported(), PDO::PARAM_INT);
        $stmt->bindValue(':started_at', $history->getStartedAt()->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->bindValue(':completed_at', $history->getCompletedAt()?->format('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->bindValue(':status', $history->getStatus(), PDO::PARAM_STR);
        $stmt->bindValue(':error_message', $history->getErrorMessage(), PDO::PARAM_STR);
        $stmt->execute();
    }

    public function findLast(): ?ImportHistory
    {
        $sql = 'SELECT * FROM import_history ORDER BY started_at DESC LIMIT 1';
        $stmt = $this->pdo->query($sql);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function findAll(int $limit = 100): array
    {
        $sql = 'SELECT * FROM import_history ORDER BY started_at DESC LIMIT :limit';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $histories = [];
        while ($row = $stmt->fetch()) {
            $histories[] = $this->hydrate($row);
        }

        return $histories;
    }

    private function hydrate(array $row): ImportHistory
    {
        return new ImportHistory(
            $row['filename'],
            (int)$row['products_imported'],
            new DateTimeImmutable($row['started_at']),
            $row['completed_at'] ? new DateTimeImmutable($row['completed_at']) : null,
            $row['status'],
            $row['error_message'],
            (int)$row['id']
        );
    }
}
