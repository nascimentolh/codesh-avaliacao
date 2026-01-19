<?php

namespace App\Presentation\Controller;

use App\Infrastructure\Database\Connection;
use App\Domain\Repository\ImportHistoryRepositoryInterface;

class HealthController
{
    private ImportHistoryRepositoryInterface $historyRepository;
    private float $startTime;

    public function __construct(
        ImportHistoryRepositoryInterface $historyRepository,
        float $startTime
    ) {
        $this->historyRepository = $historyRepository;
        $this->startTime = $startTime;
    }

    public function index(): array
    {
        $dbConnectionOk = Connection::testConnection();
        $dbWriteOk = Connection::testWrite();

        $lastImport = $this->historyRepository->findLast();
        $lastCronRun = $lastImport ? $lastImport->getStartedAt()->format('Y-m-d H:i:s') : null;

        $uptime = microtime(true) - $this->startTime;
        $memoryUsage = memory_get_usage(true);

        return [
            'status' => 'OK',
            'database' => [
                'connection' => $dbConnectionOk ? 'OK' : 'FAILED',
                'read' => $dbConnectionOk ? 'OK' : 'FAILED',
                'write' => $dbWriteOk ? 'OK' : 'FAILED',
            ],
            'last_cron_run' => $lastCronRun,
            'uptime_seconds' => round($uptime, 2),
            'memory_usage' => [
                'bytes' => $memoryUsage,
                'human' => $this->formatBytes($memoryUsage),
            ],
            'timestamp' => date('Y-m-d H:i:s'),
        ];
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
