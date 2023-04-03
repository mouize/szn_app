<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Shop;
use App\Entity\Stock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $setProductsToShop = function (array $products, Shop $shop, int $quantity) use ($manager) {
            foreach ($products as $product) {
                $stock = new Stock();
                $stock->setProduct($product);
                $stock->setShop($shop);
                $stock->setQuantity($quantity);

                $manager->persist($stock);
            }

            $manager->flush();
        };

        $shop = new Shop();
        $shop->setName('Shop Lyon');
        $shop->setLatitude('45.7580052');
        $shop->setLongitude('24.8001108');
        $shop->setAddress('Lyon, France');
        $shop->setManager('Jean michel');

        $manager->persist($shop);

        $shop1 = new Shop();
        $shop1->setName('Shop Paris Chatelet');
        $shop1->setLatitude('48.85913');
        $shop1->setLongitude('2.2769957');
        $shop1->setAddress('Chatelet, Paris, France');
        $shop1->setManager('Jean francois');

        $manager->persist($shop1);

        $shop2 = new Shop();
        $shop2->setName('Shop Paris - La Defense');
        $shop2->setLatitude('48.8910037');
        $shop2->setLongitude('2.238988');
        $shop2->setAddress('La Defense, Paris, France');
        $shop2->setManager('Jean francois');


        $manager->persist($shop2);
        $manager->flush();

        $products = [];
        for ($i = 1; $i <= 5; $i++) {
            $product = new Product();
            $product->setName("Product #$i");
            $product->setPhotoUrl("photo url #$i");

            $manager->persist($product, true);

            $products[] = $product;
        }

        //1 product with only shop 1
        $setProductsToShop(array_slice($products, 0, 3), $shop1, 2);
        //2 products with both shops and 1 with only shop 2
        $setProductsToShop(array_slice($products, 1, 3), $shop2, 3);
    }
}
