#!/bin/bash
# Resetting the test database and running tests
echo "Running script to remove database"
php src/Scripts/Database/Test/DropTestDatabase.php
echo " "
echo "Removing the test schema directory manually"
mkdir ./silexCarsTest
mv ./silexCarsTest database/carsApp
rmdir database/carsApp/silexCarsTest
echo " "
echo "Running the seed file"
php src/Scripts/Database/Test/InitialiseTestDatabase.php
echo "Seed file run."
echo " "
echo "Running tests"
./vendor/bin/phpunit tests
