# Lab exercise 6.1 bonus for A5.2 live session

This repository contains the following classes in src/

* Basket     -> Simulates a shopping basket that can contain various items
* BasketItem -> Simulates an item in a shopping basket, extends the Product class and adds quantity as a field
* Product    -> Simulates a single entry in a products table within an SQL database

...and a test class that fullfills all of the test cases specified by my tutor for this exercise below. I have modified some test cases where I deemed it worthwhile, which have been striked through:

* ~~When new item is added, the count of items in the basket is increased by one~~ When a new item is added with a positive integer quantity to an empty basket, the total quantity of that basket mates the given integer
* ~~When the same item is added, the count of items in the basket remains the same and the added item quantity is increased by 1~~ When an item already in the basket gets added to it again, the new item quantity is equal to the sum of the old quantity and the quantity just added
* Attempting to add an item with the quantity of zero or less should throw ValueError
* Attempting to add an item with the price of less than zero should throw ValueError
* Attempting to remove an item that does not exist should throw NoResultsException
* Removing a number of the same item which is greater than zero and less than the quantity in the basket should decrease the quantity
* Attempting to remove a number of the same item which is greater than its quantity should should throw ValueError
* Removing a number of the same item which is equal to its quantity should remove the item fromm the basket
* When a few items are added, the total price must be the sum of each item times its quantity

I have also added an extra test case to get 100% class coverage for the Basket class

* Removing a number of an item not in the basket should throw NoResultsException

This was completed as part of a level 4 apprenticeship in Software Development. Any feedback will be appreciated.

## This exercise has been developed and tested with:
* PHP 8.3.9 (available on PATH) - PHP 8.2+ is required for PHPUnit
* Xdebug 3.3.2 - required IF generating code coverage reports with PHPUnit
* Composer 2.7.7

## Setup
The dependencies for this project can be installed by running the following command, assuming you have composer available on PATH:

`composer install`

## Run
Note: When generating code coverage reports with PHPUnit, the .gitignore file assumes you're saving the reports to tests/coverage.
