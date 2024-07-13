<?php declare(strict_types=1);

namespace App;

class BasketItem {
    private int $id;
    private float $price;
    private int $quantity = 0;

    public function __construct(int $id, Product $product) {
        [ $this->id, $this->price ] = $product->getProductPriceById($id);
    }

    public function getId(): int {
        return $this->id;
    }

    public function getPrice(): float {
        return $this->price;
    }

    public function getQuantity(): int {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void {
        $this->quantity = $quantity;
    }
}
