<?php declare(strict_types=1);

namespace App;

class Basket {
    public array $items = [];

    public function addItemToBasket(BasketItem $itemToAdd): void {
        if ($itemToAdd->getPrice() < 0) {
            throw new \ValueError("Cannot add item with a negative price to the basket");
        }

        if ($itemToAdd->getQuantity() === 0) {
            throw new \ValueError("Cannot add item with a quantity of zero to the basket");
        }

        $itemKeys = array_keys($this->items);
        if (in_array($itemToAdd->getId(), $itemKeys)) {
            $currentItemInBasket = $this->items[$itemToAdd->getId()];

            $currentQuantityOfItem = $currentItemInBasket->getQuantity();
            $quantityOfItemToAdd = $itemToAdd->getQuantity();

            $newQuantityOfItem = $currentQuantityOfItem + $quantityOfItemToAdd;

            $currentItemInBasket->setQuantity($newQuantityOfItem);

            return;
        }

        $this->items[$itemToAdd->getId()] = $itemToAdd;
    }

    public function removeGivenNumberOfItemFromBasket(BasketItem $itemToRemove, int $quantityToRemove): void {
        $itemKeys = array_keys($this->items);
        if (in_array($itemToRemove->getId(), $itemKeys)) {
            $currentItemInBasket = $this->items[$itemToRemove->getId()];
            $currentQuantityOfItemInBasket = $currentItemInBasket->getQuantity();

            if ($currentQuantityOfItemInBasket > $quantityToRemove) {
                $currentItemInBasket->setQuantity($currentQuantityOfItemInBasket - $quantityToRemove);
                return;
            }

            if ($currentQuantityOfItemInBasket === $quantityToRemove) {
                $this->deleteItemFromBasket($itemToRemove);
                return;
            }

            throw new \ValueError("Cannot delete more items than what is present in basket");
        }

        throw new NoResultsException("Cannot remove nonexistent item from basket");
    }

    public function deleteItemFromBasket(BasketItem $itemToDelete): void {
        $itemKeys = array_keys($this->items);
        if (in_array($itemToDelete->getId(), $itemKeys)) {
            unset(
                $this->items[$itemToDelete->getId()]
            );

            return;
        }

        throw new NoResultsException("Cannot delete nonexistent item from basket");
    }

    public function countNumberOfAllItemsInBasket(): int {
        $totalItemCount = 0;

        foreach ($this->items as $item) {
            $totalItemCount += $item->getQuantity();
        }

        return $totalItemCount;
    }

    public function countNumberOfSpecificItemInBasket(BasketItem $itemToCount): int {
        foreach ($this->items as $item) {
            if ($item->getId() === $itemToCount->getId()) {
                return $item->getQuantity();
            }
        }

        throw new NoResultsException("No matching item in basket");
    }

    public function getTotalPriceOfAllItemsInBasket(): float {
        $totalPrice = 0.00;

        foreach ($this->items as $item) {
            $totalPrice += ($item->getPrice() * $item->getQuantity());
        }

        return $totalPrice;
    }
}
