<?php declare(strict_types=1);

namespace App;

class BasketItem extends Product {
    private int $quantity = 0;

    public function __construct(mixed $idOrSku, \PDO $pdo = new \PDO()) {
        parent::__construct($idOrSku, $pdo);
    }

    public function getQuantity(): int {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void {
        $this->quantity = $quantity;
    }
}
