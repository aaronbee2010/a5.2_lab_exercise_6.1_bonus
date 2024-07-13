<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Basket;
use App\BasketItem;
use App\Product;
use App\NoResultsException;

#[CoversClass(Basket::class)]
#[CoversClass(BasketItem::class)]

class BasketTest extends TestCase {
    private Product $product;

    #[Before]
    public function setUp(): void {
        $map = [
            // Input      Output
            [   35,   [ 35, 299.99 ]   ],
            [   13,   [ 13, 39.99  ]   ],
            [   17,   [ 17, 39.99  ]   ],
            [   24,   [ 24, -6.49  ]   ]
        ];

        $this->product = $this->createMock(Product::class);
        $this->product
            ->method("getProductPriceById")
            ->willReturnMap($map);
    }

    public static function provider1(): array {
        return [[1], [5], [10], [50], [100], [500], [1000], [5000]];
    }

    #[Test]
    #[DataProvider("provider1")]
    public function addItemToBasket_addPositiveIntegerOfItem_itemCountInBasketMatchesThatInteger(int $value): void {
        // Arrange
        $item = new BasketItem(35, $this->product);
        $item->setQuantity($value);
        $basket = new Basket();
        // Act
        $basket->addItemToBasket($item);
        // Assert
        $this->assertSame($value, $basket->countNumberOfAllItemsInBasket());
    }

    public static function provider2(): array {
        return [
            [1, 2],
            [13, 2],
            [42, 35],
            [121, 22],
            [492, 4000],
            [8001, 9001],
            [34, 34],
            [3643, 643],
            [346, 6464],
        ];
    }

    #[Test]
    #[DataProvider("provider2")]
    public function addItemToBasket_addAnItemThenAddTheSameItemAgain_countInBasketForThatSpecificItemMatchesTheSumQuantities(int $q1, int $q2): void {
        // Arrange
        $item1 = new BasketItem(35, $this->product);
        $item1->setQuantity($q1);
        $item2 = new BasketItem(35, $this->product);
        $item2->setQuantity($q2);
        $basket = new Basket();
        // Act
        $basket->addItemToBasket($item1);
        $basket->addItemToBasket($item2);
        // Assert
        $this->assertSame(($q1 + $q2), $basket->countNumberOfSpecificItemInBasket($item1));
    }

    public static function provider3(): array {
        return [[35], [13], [17]];
    }

    #[Test]
    #[DataProvider("provider3")]
    public function addItemToBasket_addItemWithAQuantityOfZeroToBasket_throwsValueError(int $id): void {
        // Arrange
        $item = new BasketItem($id, $this->product);
        $basket = new Basket();
        // Act / Assert
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage("Cannot add item with a quantity of zero to the basket");
        $basket->addItemToBasket($item);
    }

    #[Test]
    public function addItemToBasket_addItemWithAPriceLessThanZeroToBasket_throwsValueError(): void {
        // Arrange
        $item = new BasketItem(24, $this->product);
        $basket = new Basket();
        // Act / Assert
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage("Cannot add item with a negative price to the basket");
        $basket->addItemToBasket($item);
    }

    public static function provider4(): array {
        return [[35], [13], [17], [24]];
    }

    #[Test]
    #[DataProvider("provider4")]
    public function deleteItemToBasket_tryToRemoveNonexistentItemFromBasket_throwsNoResultsException(int $id): void {
        // Arrange
        $item = new BasketItem($id, $this->product);
        $basket = new Basket();
        // Act / Assert
        $this->expectException(NoResultsException::class);
        $this->expectExceptionMessage("Cannot delete nonexistent item from basket");
        $basket->deleteItemFromBasket($item);
    }

    public static function provider5(): array {
        return [
            [13, 3438, 32],
            [17, 587, 452],
            [35, 9353, 532],
            [13, 4, 1],
            [17, 323, 23],
            [35, 434, 32],
            [13, 7853, 332],
            [17, 739, 45],
            [35, 6869, 5],
        ];
    }

    #[Test]
    #[DataProvider("provider5")]
    public function removeGivenNumberOfItemFromBasket_removeLesserQuantityOfItemsFromBasketThanWhatIsAlreadyPresent_newQuantityIsOriginalQuantityMinusQuantityRemoved(int $id, int $q1, int $q2): void {
        // Arrange
        $item = new BasketItem($id, $this->product);
        $item->setQuantity($q1);
        $basket = new Basket();
        // Act
        $basket->addItemToBasket($item);
        $basket->removeGivenNumberOfItemFromBasket($item, $q2);
        // Assert
        $this->assertSame(($q1 - $q2), $basket->countNumberOfSpecificItemInBasket($item));
    }

    public static function provider6(): array {
        return [
            [13, 833, 105253],
            [17, 7, 13],
            [35, 92, 1426],
            [13, 238, 3422],
            [17, 427, 2424],
            [35, 329, 532],
            [13, 4, 34],
            [17, 734, 2344],
            [35, 239, 542],
        ];
    }

    #[Test]
    #[DataProvider("provider6")]
    public function removeGivenNumberOfItemFromBasket_removeGreaterQuantityOfItemsFromBasketThanWhatIsAlreadyPresent_throwsValueError(int $id, int $q1, int $q2): void {
        // Arrange
        $item = new BasketItem($id, $this->product);
        $item->setQuantity($q1);
        $basket = new Basket();
        $basket->addItemToBasket($item);
        // Act / Assert
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage("Cannot delete more items than what is present in basket");
        $basket->removeGivenNumberOfItemFromBasket($item, $q2);
    }

    public static function provider7(): array {
        return [
            [35, 2, 13, 4],
            [13, 532, 35, 2310],
            [17, 3, 13, 8],
            [13, 215, 17, 205],
            [35, 100, 17, 25],
            [17, 2020, 35, 5003],
            [13, 35, 17, 3],
            [35, 52, 17, 25],
            [17, 553, 35, 5033],
        ];
    }

    #[Test]
    #[DataProvider("provider7")]
    public function removeGivenNumberOfItemFromBasket_attemptToLowerQuantityOfItemNotInBasket_throwsNoResultsException(int $id1, int $q1, int $id2, int $q2): void {
        // Arrange
        $item = new BasketItem($id1, $this->product);
        $item->setQuantity($q1);
        $nonExistentItem = new BasketItem($id2, $this->product);
        $basket = new Basket();
        $basket->addItemToBasket($item);
        // Act / Assert
        $this->expectException(NoResultsException::class);
        $this->expectExceptionMessage("Cannot remove nonexistent item from basket");
        $basket->removeGivenNumberOfItemFromBasket($nonExistentItem, $q2);
    }

    public static function provider8(): array {
        return [
            [35, 10],
            [13, 1],
            [17, 10001],
            [35, 120],
            [13, 16],
            [17, 1800],
            [35, 1074],
            [13, 14],
            [17, 1754],
        ];
    }

    #[Test]
    #[DataProvider("provider8")]
    public function removeGivenNumberOfItemFromBasket_removeEqualQuantityOfItemsFromBasketThanWhatIsAlreadyPresent_itemIsDeletedFromBasket(int $id, int $q): void {
        // Arrange
        $item = new BasketItem($id, $this->product);
        $item->setQuantity($q);

        $basket = new Basket();
        $basket->addItemToBasket($item);
        $basket->removeGivenNumberOfItemFromBasket($item, $q);
        // Act / Assert
        $this->expectException(NoResultsException::class);
        $this->expectExceptionMessage("No matching item in basket");

        $basket->countNumberOfSpecificItemInBasket($item);
    }

    public static function provider9(): array {
        return [
            [35, 3, 13, 6, 17, 6, 1379.85],
            [13, 5, 17, 4, 35, 12, 3959.79],
            [17, 8, 35, 30, 13, 21, 10159.41],
            [17, 4, 13, 1, 35, 6, 1999.89],
            [35, 82, 17, 12, 13, 66, 27718.40],
            [13, 3, 35, 78, 17, 25, 24518.94],
        ];
    }

    #[Test]
    #[DataProvider("provider9")]
    public function getTotalPriceOfAllItemsInBasket_addAFewItemsToBasket_returnCorrectTotalPriceForAllItemsInBasket(int $id1, int $q1, int $id2, int $q2, int $id3, int $q3, float $expected): void {
        // Arrange
        $item1 = new BasketItem($id1, $this->product);
        $item1->setQuantity($q1);
        $item2 = new BasketItem($id2, $this->product);
        $item2->setQuantity($q2);
        $item3 = new BasketItem($id3, $this->product);
        $item3->setQuantity($q3);

        $basket = new Basket();
        $basket->addItemToBasket($item1);
        $basket->addItemToBasket($item2);
        $basket->addItemToBasket($item3);
        // Act / Assert - Delta is added to work around floating point errors
        $this->assertEqualsWithDelta($expected, $basket->getTotalPriceOfAllItemsInBasket(), 0.001);
    }
}
