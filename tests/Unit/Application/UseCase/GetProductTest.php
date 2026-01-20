<?php

namespace Tests\Unit\Application\UseCase;

use App\Application\UseCase\GetProduct;
use App\Domain\Entity\Product;
use App\Domain\Repository\ProductRepositoryInterface;
use App\Domain\ValueObject\ProductCode;
use App\Domain\ValueObject\ProductStatus;
use App\Domain\ValueObject\ImportedDate;
use PHPUnit\Framework\TestCase;

class GetProductTest extends TestCase
{
    public function testExecuteReturnsProductDTO(): void
    {
        $productCode = ProductCode::fromInt(123);
        $product = new Product(
            $productCode,
            ProductStatus::published(),
            ImportedDate::now(),
            'https://example.com',
            'test-creator',
            1234567890,
            1234567891,
            'Test Product',
            '100g',
            'Test Brand',
            'Snacks',
            'Organic',
            'Test City',
            'Test Store',
            'Store A',
            'Ingredient 1, Ingredient 2',
            'Traces of nuts',
            '50g',
            50.0,
            15,
            'c',
            'en:snacks',
            'https://example.com/image.jpg'
        );

        $repository = $this->createMock(ProductRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('findByCode')
            ->with($this->equalTo($productCode))
            ->willReturn($product);

        $useCase = new GetProduct($repository);
        $dto = $useCase->execute(123);

        $this->assertNotNull($dto);
        $this->assertEquals(123, $dto->code);
        $this->assertEquals('published', $dto->status);
        $this->assertEquals('Test Product', $dto->productName);
    }

    public function testExecuteReturnsNullWhenProductNotFound(): void
    {
        $productCode = ProductCode::fromInt(999);

        $repository = $this->createMock(ProductRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('findByCode')
            ->with($this->equalTo($productCode))
            ->willReturn(null);

        $useCase = new GetProduct($repository);
        $dto = $useCase->execute(999);

        $this->assertNull($dto);
    }
}
