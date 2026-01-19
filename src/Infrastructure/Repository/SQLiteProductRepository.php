<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Product;
use App\Domain\Repository\ProductRepositoryInterface;
use App\Domain\ValueObject\ProductCode;
use App\Domain\ValueObject\ProductStatus;
use App\Domain\ValueObject\ImportedDate;
use PDO;

class SQLiteProductRepository implements ProductRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(Product $product): void
    {
        $sql = <<<SQL
        INSERT INTO products (
            code, status, imported_t, url, creator, created_t, last_modified_t,
            product_name, quantity, brands, categories, labels, cities,
            purchase_places, stores, ingredients_text, traces, serving_size,
            serving_quantity, nutriscore_score, nutriscore_grade, main_category, image_url
        ) VALUES (
            :code, :status, :imported_t, :url, :creator, :created_t, :last_modified_t,
            :product_name, :quantity, :brands, :categories, :labels, :cities,
            :purchase_places, :stores, :ingredients_text, :traces, :serving_size,
            :serving_quantity, :nutriscore_score, :nutriscore_grade, :main_category, :image_url
        )
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $this->bindProductParameters($stmt, $product);
        $stmt->execute();
    }

    public function update(Product $product): void
    {
        $sql = <<<SQL
        UPDATE products SET
            status = :status,
            imported_t = :imported_t,
            url = :url,
            creator = :creator,
            created_t = :created_t,
            last_modified_t = :last_modified_t,
            product_name = :product_name,
            quantity = :quantity,
            brands = :brands,
            categories = :categories,
            labels = :labels,
            cities = :cities,
            purchase_places = :purchase_places,
            stores = :stores,
            ingredients_text = :ingredients_text,
            traces = :traces,
            serving_size = :serving_size,
            serving_quantity = :serving_quantity,
            nutriscore_score = :nutriscore_score,
            nutriscore_grade = :nutriscore_grade,
            main_category = :main_category,
            image_url = :image_url
        WHERE code = :code
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $this->bindProductParameters($stmt, $product);
        $stmt->execute();
    }

    public function findByCode(ProductCode $code): ?Product
    {
        $sql = 'SELECT * FROM products WHERE code = :code LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':code', $code->getValue(), PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function findAll(int $page = 1, int $limit = 20): array
    {
        $offset = ($page - 1) * $limit;

        $sql = 'SELECT * FROM products ORDER BY code DESC LIMIT :limit OFFSET :offset';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $products = [];
        while ($row = $stmt->fetch()) {
            $products[] = $this->hydrate($row);
        }

        return $products;
    }

    public function count(): int
    {
        $sql = 'SELECT COUNT(*) as total FROM products';
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch();
        return (int)$result['total'];
    }

    public function delete(ProductCode $code): void
    {
        $sql = 'UPDATE products SET status = :status WHERE code = :code';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':status', ProductStatus::trash()->getValue(), PDO::PARAM_STR);
        $stmt->bindValue(':code', $code->getValue(), PDO::PARAM_INT);
        $stmt->execute();
    }

    public function exists(ProductCode $code): bool
    {
        $sql = 'SELECT 1 FROM products WHERE code = :code LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':code', $code->getValue(), PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch() !== false;
    }

    private function bindProductParameters(\PDOStatement $stmt, Product $product): void
    {
        $stmt->bindValue(':code', $product->getCode()->getValue(), PDO::PARAM_INT);
        $stmt->bindValue(':status', $product->getStatus()->getValue(), PDO::PARAM_STR);
        $stmt->bindValue(':imported_t', $product->getImportedDate()->format(), PDO::PARAM_STR);
        $stmt->bindValue(':url', $product->getUrl(), PDO::PARAM_STR);
        $stmt->bindValue(':creator', $product->getCreator(), PDO::PARAM_STR);
        $stmt->bindValue(':created_t', $product->getCreatedT(), PDO::PARAM_INT);
        $stmt->bindValue(':last_modified_t', $product->getLastModifiedT(), PDO::PARAM_INT);
        $stmt->bindValue(':product_name', $product->getProductName(), PDO::PARAM_STR);
        $stmt->bindValue(':quantity', $product->getQuantity(), PDO::PARAM_STR);
        $stmt->bindValue(':brands', $product->getBrands(), PDO::PARAM_STR);
        $stmt->bindValue(':categories', $product->getCategories(), PDO::PARAM_STR);
        $stmt->bindValue(':labels', $product->getLabels(), PDO::PARAM_STR);
        $stmt->bindValue(':cities', $product->getCities(), PDO::PARAM_STR);
        $stmt->bindValue(':purchase_places', $product->getPurchasePlaces(), PDO::PARAM_STR);
        $stmt->bindValue(':stores', $product->getStores(), PDO::PARAM_STR);
        $stmt->bindValue(':ingredients_text', $product->getIngredientsText(), PDO::PARAM_STR);
        $stmt->bindValue(':traces', $product->getTraces(), PDO::PARAM_STR);
        $stmt->bindValue(':serving_size', $product->getServingSize(), PDO::PARAM_STR);
        $stmt->bindValue(':serving_quantity', $product->getServingQuantity());
        $stmt->bindValue(':nutriscore_score', $product->getNutriscoreScore(), PDO::PARAM_INT);
        $stmt->bindValue(':nutriscore_grade', $product->getNutriscoreGrade(), PDO::PARAM_STR);
        $stmt->bindValue(':main_category', $product->getMainCategory(), PDO::PARAM_STR);
        $stmt->bindValue(':image_url', $product->getImageUrl(), PDO::PARAM_STR);
    }

    private function hydrate(array $row): Product
    {
        return new Product(
            ProductCode::fromInt((int)$row['code']),
            ProductStatus::fromString($row['status']),
            ImportedDate::fromString($row['imported_t']),
            $row['url'],
            $row['creator'],
            $row['created_t'] !== null ? (int)$row['created_t'] : null,
            $row['last_modified_t'] !== null ? (int)$row['last_modified_t'] : null,
            $row['product_name'],
            $row['quantity'],
            $row['brands'],
            $row['categories'],
            $row['labels'],
            $row['cities'],
            $row['purchase_places'],
            $row['stores'],
            $row['ingredients_text'],
            $row['traces'],
            $row['serving_size'],
            $row['serving_quantity'] !== null ? (float)$row['serving_quantity'] : null,
            $row['nutriscore_score'] !== null ? (int)$row['nutriscore_score'] : null,
            $row['nutriscore_grade'],
            $row['main_category'],
            $row['image_url']
        );
    }
}
