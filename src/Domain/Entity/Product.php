<?php

namespace App\Domain\Entity;

use App\Domain\ValueObject\ProductCode;
use App\Domain\ValueObject\ProductStatus;
use App\Domain\ValueObject\ImportedDate;

class Product
{
    private ProductCode $code;
    private ProductStatus $status;
    private ImportedDate $importedDate;
    private ?string $url;
    private ?string $creator;
    private ?int $createdT;
    private ?int $lastModifiedT;
    private ?string $productName;
    private ?string $quantity;
    private ?string $brands;
    private ?string $categories;
    private ?string $labels;
    private ?string $cities;
    private ?string $purchasePlaces;
    private ?string $stores;
    private ?string $ingredientsText;
    private ?string $traces;
    private ?string $servingSize;
    private ?float $servingQuantity;
    private ?int $nutriscoreScore;
    private ?string $nutriscoreGrade;
    private ?string $mainCategory;
    private ?string $imageUrl;

    public function __construct(
        ProductCode $code,
        ProductStatus $status,
        ImportedDate $importedDate,
        ?string $url = null,
        ?string $creator = null,
        ?int $createdT = null,
        ?int $lastModifiedT = null,
        ?string $productName = null,
        ?string $quantity = null,
        ?string $brands = null,
        ?string $categories = null,
        ?string $labels = null,
        ?string $cities = null,
        ?string $purchasePlaces = null,
        ?string $stores = null,
        ?string $ingredientsText = null,
        ?string $traces = null,
        ?string $servingSize = null,
        ?float $servingQuantity = null,
        ?int $nutriscoreScore = null,
        ?string $nutriscoreGrade = null,
        ?string $mainCategory = null,
        ?string $imageUrl = null
    ) {
        $this->code = $code;
        $this->status = $status;
        $this->importedDate = $importedDate;
        $this->url = $url;
        $this->creator = $creator;
        $this->createdT = $createdT;
        $this->lastModifiedT = $lastModifiedT;
        $this->productName = $productName;
        $this->quantity = $quantity;
        $this->brands = $brands;
        $this->categories = $categories;
        $this->labels = $labels;
        $this->cities = $cities;
        $this->purchasePlaces = $purchasePlaces;
        $this->stores = $stores;
        $this->ingredientsText = $ingredientsText;
        $this->traces = $traces;
        $this->servingSize = $servingSize;
        $this->servingQuantity = $servingQuantity;
        $this->nutriscoreScore = $nutriscoreScore;
        $this->nutriscoreGrade = $nutriscoreGrade;
        $this->mainCategory = $mainCategory;
        $this->imageUrl = $imageUrl;
    }

    public static function create(
        ProductCode $code,
        array $data = []
    ): self {
        return new self(
            $code,
            ProductStatus::draft(),
            ImportedDate::now(),
            $data['url'] ?? null,
            $data['creator'] ?? null,
            $data['created_t'] ?? null,
            $data['last_modified_t'] ?? null,
            $data['product_name'] ?? null,
            $data['quantity'] ?? null,
            $data['brands'] ?? null,
            $data['categories'] ?? null,
            $data['labels'] ?? null,
            $data['cities'] ?? null,
            $data['purchase_places'] ?? null,
            $data['stores'] ?? null,
            $data['ingredients_text'] ?? null,
            $data['traces'] ?? null,
            $data['serving_size'] ?? null,
            isset($data['serving_quantity']) ? (float)$data['serving_quantity'] : null,
            $data['nutriscore_score'] ?? null,
            $data['nutriscore_grade'] ?? null,
            $data['main_category'] ?? null,
            $data['image_url'] ?? null
        );
    }

    public function publish(): void
    {
        $this->status = ProductStatus::published();
    }

    public function moveToTrash(): void
    {
        $this->status = ProductStatus::trash();
    }

    public function update(array $data): void
    {
        if (isset($data['url'])) {
            $this->url = $data['url'];
        }
        if (isset($data['creator'])) {
            $this->creator = $data['creator'];
        }
        if (isset($data['product_name'])) {
            $this->productName = $data['product_name'];
        }
        if (isset($data['quantity'])) {
            $this->quantity = $data['quantity'];
        }
        if (isset($data['brands'])) {
            $this->brands = $data['brands'];
        }
        if (isset($data['categories'])) {
            $this->categories = $data['categories'];
        }
        if (isset($data['labels'])) {
            $this->labels = $data['labels'];
        }
        if (isset($data['cities'])) {
            $this->cities = $data['cities'];
        }
        if (isset($data['purchase_places'])) {
            $this->purchasePlaces = $data['purchase_places'];
        }
        if (isset($data['stores'])) {
            $this->stores = $data['stores'];
        }
        if (isset($data['ingredients_text'])) {
            $this->ingredientsText = $data['ingredients_text'];
        }
        if (isset($data['traces'])) {
            $this->traces = $data['traces'];
        }
        if (isset($data['serving_size'])) {
            $this->servingSize = $data['serving_size'];
        }
        if (isset($data['serving_quantity'])) {
            $this->servingQuantity = (float)$data['serving_quantity'];
        }
        if (isset($data['nutriscore_score'])) {
            $this->nutriscoreScore = (int)$data['nutriscore_score'];
        }
        if (isset($data['nutriscore_grade'])) {
            $this->nutriscoreGrade = $data['nutriscore_grade'];
        }
        if (isset($data['main_category'])) {
            $this->mainCategory = $data['main_category'];
        }
        if (isset($data['image_url'])) {
            $this->imageUrl = $data['image_url'];
        }
    }

    // Getters
    public function getCode(): ProductCode
    {
        return $this->code;
    }

    public function getStatus(): ProductStatus
    {
        return $this->status;
    }

    public function getImportedDate(): ImportedDate
    {
        return $this->importedDate;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getCreator(): ?string
    {
        return $this->creator;
    }

    public function getCreatedT(): ?int
    {
        return $this->createdT;
    }

    public function getLastModifiedT(): ?int
    {
        return $this->lastModifiedT;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function getBrands(): ?string
    {
        return $this->brands;
    }

    public function getCategories(): ?string
    {
        return $this->categories;
    }

    public function getLabels(): ?string
    {
        return $this->labels;
    }

    public function getCities(): ?string
    {
        return $this->cities;
    }

    public function getPurchasePlaces(): ?string
    {
        return $this->purchasePlaces;
    }

    public function getStores(): ?string
    {
        return $this->stores;
    }

    public function getIngredientsText(): ?string
    {
        return $this->ingredientsText;
    }

    public function getTraces(): ?string
    {
        return $this->traces;
    }

    public function getServingSize(): ?string
    {
        return $this->servingSize;
    }

    public function getServingQuantity(): ?float
    {
        return $this->servingQuantity;
    }

    public function getNutriscoreScore(): ?int
    {
        return $this->nutriscoreScore;
    }

    public function getNutriscoreGrade(): ?string
    {
        return $this->nutriscoreGrade;
    }

    public function getMainCategory(): ?string
    {
        return $this->mainCategory;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code->getValue(),
            'status' => $this->status->getValue(),
            'imported_t' => $this->importedDate->format(),
            'url' => $this->url,
            'creator' => $this->creator,
            'created_t' => $this->createdT,
            'last_modified_t' => $this->lastModifiedT,
            'product_name' => $this->productName,
            'quantity' => $this->quantity,
            'brands' => $this->brands,
            'categories' => $this->categories,
            'labels' => $this->labels,
            'cities' => $this->cities,
            'purchase_places' => $this->purchasePlaces,
            'stores' => $this->stores,
            'ingredients_text' => $this->ingredientsText,
            'traces' => $this->traces,
            'serving_size' => $this->servingSize,
            'serving_quantity' => $this->servingQuantity,
            'nutriscore_score' => $this->nutriscoreScore,
            'nutriscore_grade' => $this->nutriscoreGrade,
            'main_category' => $this->mainCategory,
            'image_url' => $this->imageUrl,
        ];
    }
}
