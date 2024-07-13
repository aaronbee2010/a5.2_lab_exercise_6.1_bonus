<?php declare(strict_types=1);

namespace App;

class Product {
    /**
     * @param int $id The product ID used for retrieving the products entry in the database
     * @return array An indexed array containing the following in order: * the integer id of the product in the database * the float of its cost
     */
    public function getProductPriceById(): array {
        throw new Exception("Not yet implemented");
    }
}
