<?php

namespace App\Application\DTO;

class PaginationDTO
{
    public int $page;
    public int $limit;
    public int $total;
    public int $totalPages;

    public function __construct(int $page, int $limit, int $total)
    {
        $this->page = max(1, $page);
        $this->limit = $limit;
        $this->total = $total;
        $this->totalPages = $limit > 0 ? (int)ceil($total / $limit) : 0;
    }

    public function toArray(): array
    {
        return [
            'page' => $this->page,
            'limit' => $this->limit,
            'total' => $this->total,
            'total_pages' => $this->totalPages,
        ];
    }
}
