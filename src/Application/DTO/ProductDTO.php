<?php

namespace App\Application\DTO;

use App\Domain\Entity\Product;

class ProductDTO
{
    public int $code;
    public string $status;
    public string $importedT;
    public ?string $url;
    public ?string $creator;
    public ?int $createdT;
    public ?int $lastModifiedT;
    public ?string $productName;
    public ?string $quantity;
    public ?string $brands;
    public ?string $categories;
    public ?string $labels;
    public ?string $cities;
    public ?string $purchasePlaces;
    public ?string $stores;
    public ?string $ingredientsText;
    public ?string $traces;
    public ?string $servingSize;
    public ?float $servingQuantity;
    public ?int $nutriscoreScore;
    public ?string $nutriscoreGrade;
    public ?string $mainCategory;
    public ?string $imageUrl;

    public static function fromEntity(Product $product): self
    {
        $dto = new self();
        $dto->code = $product->getCode()->getValue();
        $dto->status = $product->getStatus()->getValue();
        $dto->importedT = $product->getImportedDate()->format();
        $dto->url = $product->getUrl();
        $dto->creator = $product->getCreator();
        $dto->createdT = $product->getCreatedT();
        $dto->lastModifiedT = $product->getLastModifiedT();
        $dto->productName = $product->getProductName();
        $dto->quantity = $product->getQuantity();
        $dto->brands = $product->getBrands();
        $dto->categories = $product->getCategories();
        $dto->labels = $product->getLabels();
        $dto->cities = $product->getCities();
        $dto->purchasePlaces = $product->getPurchasePlaces();
        $dto->stores = $product->getStores();
        $dto->ingredientsText = $product->getIngredientsText();
        $dto->traces = $product->getTraces();
        $dto->servingSize = $product->getServingSize();
        $dto->servingQuantity = $product->getServingQuantity();
        $dto->nutriscoreScore = $product->getNutriscoreScore();
        $dto->nutriscoreGrade = $product->getNutriscoreGrade();
        $dto->mainCategory = $product->getMainCategory();
        $dto->imageUrl = $product->getImageUrl();

        return $dto;
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'status' => $this->status,
            'imported_t' => $this->importedT,
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
