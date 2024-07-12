<?php declare(strict_types=1);

/*
* When new item is added, the count of items in the basket is increased by one
* When the same item is added, the count of items in the basket remains the same and the added item quantity is increased by 1
* Attempting to add an item  with the quantity of zero or less should throw ValueError
* Attempting to add an item with the price of less than zero should throw ValueError
* Attempting to remove an item that does not exist should throw NoResultsException
* Removing a number of the same item which is greater than zero and less than the quantity in the basket should decrease the quantity
* Attempting to remove a number of the same item which is greater than its quantity should should throw ValueError
* Removing a number of the same item which is equal to its quantity should remove the item fromm the basket
* When a few items are added, the total price must be the sum of each item times its quantity
*/

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\Test;
use App\Basket;
use App\BasketItem;
use App\NoResultsException;

#[CoversClass(Basket::class)]
#[CoversClass(BasketItem::class)]
class BasketTest extends TestCase {
    private function generatePreparedPdoMock(): PDO {
        $map = [
            [
                [ "id" => 35 ],
                [
                    "id" => "35",
                    "sku" => "CFI-1215A",
                    "price" => "299.99"
                ]
            ],
            [
                [ "id" => 13 ],
                [
                    "id" => "13",
                    "sku" => "CFI-ZCT1W",
                    "price" => "39.99"
                ]
            ],
            [
                [ "id" => 17 ],
                [
                    "id" => "17",
                    "sku" => "XSX-M1914",
                    "price" => "39.99"
                ]
            ],
            [
                [ "id" => 24 ],
                [
                    "id" => "24",
                    "sku" => "CO-116-MS",
                    "price" => "-6.49"
                ]
            ]
        ];

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('execute')->willReturnCallback(function($params) use ($stmt, $map) {
            // Find the corresponding result in the map
            $result = false;
            foreach ($map as $entry) {
                if ($entry[0] == $params) {
                    $result = $entry[1];
                    break;
                }
            }

            // Set up fetch to return the found result or false
            $stmt->expects($this->once())
                ->method('fetch')
                ->willReturn($result);

            return true;
        });
        $pdo = $this->createStub(PDO::class);
        $pdo->method("prepare")->willReturn($stmt);

        return $pdo;
    }

    #[Test]
    public function addItemToBasket_addOneItem_itemCountInBasketIsOne(): void {
        // Arrange
        $pdo = $this->generatePreparedPdoMock();
        
        $item = new BasketItem(35, $pdo);
        $item->setQuantity(1);
        $basket = new Basket();
        // Act
        $basket->addItemToBasket($item);
        // Assert
        $this->assertSame(1, $basket->countNumberOfAllItemsInBasket());
    }

    #[Test]
    public function addItemToBasket_addOneItemThenAddOneOfTheSameItemAgain_countInBasketForThatSpecificItemIsTwo(): void {
        // Arrange
        $pdo = $this->generatePreparedPdoMock();

        $item = new BasketItem(35, $pdo);
        $item->setQuantity(1);
        $basket = new Basket();
        // Act
        $basket->addItemToBasket($item);
        $basket->addItemToBasket($item);
        // Assert
        $this->assertSame(2, $basket->countNumberOfSpecificItemInBasket($item));
    }

    #[Test]
    public function addItemToBasket_addItemWithAQuantityOfZeroToBasket_throwsValueError(): void {
        // Arrange
        $pdo = $this->generatePreparedPdoMock();

        $item = new BasketItem(35, $pdo);
        $basket = new Basket();

        // Act / Assert
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage("Cannot add item with a quantity of zero to the basket");
        $basket->addItemToBasket($item);
    }

    #[Test]
    public function addItemToBasket_addItemWithAPriceLessThanZeroToBasket_throwsValueError(): void {
        // Arrange
        $pdo = $this->generatePreparedPdoMock();

        $item = new BasketItem(24, $pdo);
        $basket = new Basket();

        // Act / Assert
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage("Cannot add item with a negative price to the basket");
        $basket->addItemToBasket($item);
    }

    #[Test]
    public function deleteItemToBasket_tryToRemoveNonexistentItemFromBasket_throwsNoResultsException(): void {
        // Arrange
        $pdo = $this->generatePreparedPdoMock();

        $item = new BasketItem(24, $pdo);
        $basket = new Basket();

        // Act / Assert
        $this->expectException(NoResultsException::class);
        $this->expectExceptionMessage("Cannot delete nonexistent item from basket");
        $basket->deleteItemFromBasket($item);
    }

    #[Test]
    public function removeGivenNumberOfItemFromBasket_removeLesserQuantityOfItemsFromBasketThanWhatIsAlreadyPresent_newQuantityIsOriginalQuantityMinusQuantityRemoved(): void {
        // Arrange
        $pdo = $this->generatePreparedPdoMock();

        $item = new BasketItem(35, $pdo);
        $item->setQuantity(8);
        $basket = new Basket();

        // Act
        $basket->addItemToBasket($item);
        $basket->removeGivenNumberOfItemFromBasket($item, 3);

        // Assert
        $this->assertSame(5, $basket->countNumberOfSpecificItemInBasket($item));
    }

    #[Test]
    public function removeGivenNumberOfItemFromBasket_removeGreaterQuantityOfItemsFromBasketThanWhatIsAlreadyPresent_throwsValueError(): void {
        // Arrange
        $pdo = $this->generatePreparedPdoMock();

        $item = new BasketItem(35, $pdo);
        $item->setQuantity(12);
        $basket = new Basket();
        $basket->addItemToBasket($item);

        // Act / Assert
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage("Cannot delete more items than what is present in basket");
        $basket->removeGivenNumberOfItemFromBasket($item, 15);
    }

    #[Test]
    public function removeGivenNumberOfItemFromBasket_removeEqualQuantityOfItemsFromBasketThanWhatIsAlreadyPresent_itemIsDeletedFromBasket(): void {
        // Arrange
        $pdo = $this->generatePreparedPdoMock();

        $item = new BasketItem(35, $pdo);
        $item->setQuantity(10);
        $basket = new Basket();

        $basket->addItemToBasket($item);
        $basket->removeGivenNumberOfItemFromBasket($item, 10);

        // Act / Assert
        $this->expectException(NoResultsException::class);
        $this->expectExceptionMessage("No matching item in basket");

        $basket->countNumberOfSpecificItemInBasket($item);
    }

    #[Test]
    public function getTotalPriceOfAllItemsInBasket_addAFewItemsToBasket_returnCorrectTotalPriceForAllItemsInBasket(): void {
        // Arrange
        $pdo = $this->generatePreparedPdoMock();
        $item1 = new BasketItem(35, $pdo);
        $item1->setQuantity(3);

        $pdo = $this->generatePreparedPdoMock();
        $item2 = new BasketItem(13, $pdo);
        $item2->setQuantity(6);

        $pdo = $this->generatePreparedPdoMock();
        $item3 = new BasketItem(17, $pdo);
        $item3->setQuantity(6);

        $basket = new Basket();
        $basket->addItemToBasket($item1);
        $basket->addItemToBasket($item2);
        $basket->addItemToBasket($item3);

        // Act / Assert
        $this->assertEqualsWithDelta(1379.85, $basket->getTotalPriceOfAllItemsInBasket(), 0.001);
    }
}
