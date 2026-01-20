<?php

namespace Tests\Unit\Application\UseCase;

use App\Application\UseCase\UpdateProduct;
use App\Domain\Entity\Product;
use App\Domain\Repository\ProductRepositoryInterface;
use App\Domain\ValueObject\ProductCode;
use App\Domain\ValueObject\ProductStatus;
use App\Domain\ValueObject\ImportedDate;
use PHPUnit\Framework\TestCase;

class UpdateProductTest extends TestCase
{
    public function testExecuteUpdatesProduct(): void
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
            'Old Product Name',
            '100g',
            'Test Brand'
        );

        $repository = $this->createMock(ProductRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('findByCode')
            ->with($this->equalTo($productCode))
            ->willReturn($product);

        $repository->expects($this->once())
            ->method('update')
            ->with($this->equalTo($product));

        $useCase = new UpdateProduct($repository);
        $dto = $useCase->execute(123, [
            'product_name' => 'New Product Name',
            'quantity' => '200g',
        ]);

        $this->assertNotNull($dto);
        $this->assertEquals('New Product Name', $dto->productName);
        $this->assertEquals('200g', $dto->quantity);
    }

    public function testExecuteReturnsNullWhenProductNotFound(): void
    {
        $productCode = ProductCode::fromInt(999);

        $repository = $this->createMock(ProductRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('findByCode')
            ->with($this->equalTo($productCode))
            ->willReturn(null);

        $repository->expects($this->never())
            ->method('update');

        $useCase = new UpdateProduct($repository);
        $dto = $useCase->execute(999, ['product_name' => 'Test']);

        $this->assertNull($dto);
    }
}
