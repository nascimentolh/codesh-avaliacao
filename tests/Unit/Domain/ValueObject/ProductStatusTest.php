<?php

namespace Tests\Unit\Domain\ValueObject;

use App\Domain\ValueObject\ProductStatus;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ProductStatusTest extends TestCase
{
    public function testCreateDraftStatus(): void
    {
        $status = ProductStatus::draft();
        $this->assertEquals('draft', $status->getValue());
        $this->assertTrue($status->isDraft());
        $this->assertFalse($status->isTrash());
        $this->assertFalse($status->isPublished());
    }

    public function testCreateTrashStatus(): void
    {
        $status = ProductStatus::trash();
        $this->assertEquals('trash', $status->getValue());
        $this->assertTrue($status->isTrash());
        $this->assertFalse($status->isDraft());
        $this->assertFalse($status->isPublished());
    }

    public function testCreatePublishedStatus(): void
    {
        $status = ProductStatus::published();
        $this->assertEquals('published', $status->getValue());
        $this->assertTrue($status->isPublished());
        $this->assertFalse($status->isDraft());
        $this->assertFalse($status->isTrash());
    }

    public function testCreateFromString(): void
    {
        $status = ProductStatus::fromString('draft');
        $this->assertEquals('draft', $status->getValue());
    }

    public function testInvalidStatus(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ProductStatus::fromString('invalid');
    }

    public function testEquals(): void
    {
        $status1 = ProductStatus::draft();
        $status2 = ProductStatus::draft();
        $status3 = ProductStatus::published();

        $this->assertTrue($status1->equals($status2));
        $this->assertFalse($status1->equals($status3));
    }

    public function testToString(): void
    {
        $status = ProductStatus::draft();
        $this->assertEquals('draft', (string)$status);
    }
}
