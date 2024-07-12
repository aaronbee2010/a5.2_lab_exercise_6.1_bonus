<?php declare(strict_types=1);

namespace App;

abstract class Product {
    private readonly int $id;
    private readonly string $sku;
    private readonly float $price;

    protected function __construct(int $id, \PDO $pdo = new PDO()) {
        if (gettype($id) !== "integer") {
            throw new \TypeError("Identifier must be an integer");
        }

        $stmt = $pdo->prepare("SELECT id, sku, price FROM products WHERE id = :id");
        $stmt->execute(["id" => $id]);

        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (empty($data)) {
            throw new \NoResultsException("Product not found");
        }

        $this->id = (int) $data["id"];
        $this->sku = (string) $data["sku"];
        $this->price = (float) $data["price"];
    }

    public function getId(): int {
        return $this->id;
    }

    public function getSku(): string {
        return $this->sku;
    }

    public function getPrice(): float {
        return $this->price;
    }
}
