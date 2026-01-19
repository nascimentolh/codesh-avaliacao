<?php

namespace App\Domain\Repository;

use App\Domain\Entity\ImportHistory;

interface ImportHistoryRepositoryInterface
{
    /**
     * Save import history record
     */
    public function save(ImportHistory $history): void;

    /**
     * Update import history record
     */
    public function update(ImportHistory $history): void;

    /**
     * Find the last import record
     */
    public function findLast(): ?ImportHistory;

    /**
     * Find all import history records
     *
     * @return ImportHistory[]
     */
    public function findAll(int $limit = 100): array;
}
