<?php

namespace App\Tests\Feature\Stock;

use App\Document\ProductView;
use App\Entity\Product;
use App\Entity\Shop;
use App\Entity\Stock;
use App\Tests\Feature\FeatureTestCase;
use Symfony\Component\HttpFoundation\Response;

class SetProductsToShopTest extends FeatureTestCase
{
    public function test_addProductsToShop_WHEN_creatingAssociation_THEN_success(): void
    {
        $shop = $this->prepareShop();
        $product = $this->prepareProduct();

        $payload = ['quantity' => 4];

        $this->client->request(
            method: 'Put',
            uri: '/api/shops/' . $shop->getId() . '/products/' . $product->getId(),
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($payload)
        );

        //Good https status
        $this->assertEquals(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
        $this->assertEmpty($this->client->getResponse()->getContent());

        //Quantity saved correctly
        $stock = $this->em->getRepository(Stock::class)->findOneBy(['shop' => $shop, 'product' => $product]);
        $this->assertEquals(4, $stock->getQuantity());

        //check data saved on mongodb
        $productView = $this->dm->getRepository(ProductView::class)->findOneBy([
            'productId' => $product->getId(),
        ]);
        $this->dm->refresh($productView);

        $ProductViewShops = current($productView->getShops());
        $this->assertEquals($shop->getId(), $ProductViewShops['shopId']);
        $this->assertEquals(4, $ProductViewShops['quantity']);
    }

    public function test_addProductsToShop_WHEN_updatingAssociation_THEN_success(): void
    {
        $shop = $this->prepareShop();
        $product = $this->prepareProduct();
        $this->prepareStock($shop, $product);

        $stock = $this->em->getRepository(Stock::class)->findOneBy(['shop' => $shop, 'product' => $product]);
        $this->assertEquals(1, $stock->getQuantity());

        $payload = ['quantity' => 4];
        $this->client->request(
            method: 'Put',
            uri: '/api/shops/' . $shop->getId() . '/products/' . $product->getId(),
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($payload)
        );

        $this->assertEquals(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
        $this->assertEmpty($this->client->getResponse()->getContent());

        //Check stock updated correctly
        $this->em->refresh($stock);
        $this->assertEquals(4, $stock->getQuantity());
        $this->assertCount(1, $this->em->getRepository(Stock::class)->findAll());

        //Check ProductLog is updated too.
        $productView = $this->dm->getRepository(ProductView::class)->findOneBy([
            'productId' => $product->getId(),
        ]);
        $this->dm->refresh($productView);

        $this->assertCount(1, $productView->getShops());

        $ProductViewShops = current($productView->getShops());
        $this->assertEquals($shop->getId(), $ProductViewShops['shopId']);
        $this->assertEquals(4, $ProductViewShops['quantity']);
    }

    public function test_addProductsToShop_WHEN_manyShopsForAProduct_THEN_success(): void
    {
        $i = 1;
        $shop1 = $this->prepareShop($i++);
        $shop2 = $this->prepareShop($i++);
        $product = $this->prepareProduct();

        $this->client->request(
            method: 'Put',
            uri: '/api/shops/' . $shop1->getId() . '/products/' . $product->getId(),
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['quantity' => 1])
        );

        $this->client->request(
            method: 'Put',
            uri: '/api/shops/' . $shop2->getId() . '/products/' . $product->getId(),
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['quantity' => 2])
        );

        $productView = $this->dm->getRepository(ProductView::class)->findOneBy([
            'productId' => $product->getId(),
        ]);
        $this->dm->refresh($productView);

        $this->assertCount(2, $productView->getShops());
    }

    /*
     * Need to prepare some data first, dunno what are best practice with symfony for that.
     */
    private function prepareShop(int $id = 0): Shop
    {
        $shop = new Shop();
        $shop->setName("Shop test #$id");
        $shop->setLatitude('48.8566');
        $shop->setLongitude('2.3522');
        $shop->setAddress('Paris, France');
        $shop->setManager('John Doe');

        $this->em->getRepository(Shop::class)->save($shop, true);

        return $shop;
    }

    private function prepareProduct(): Product
    {
        $product = new Product();
        $product->setName("Product test");
        $product->setPhotoUrl('url test');

        $this->em->getRepository(Product::class)->save($product, true);

        return $product;
    }

    private function prepareStock(Shop $shop, Product $product, int $quantity = 1): void
    {
        $stock = new Stock();
        $stock->setShop($shop);
        $stock->setProduct($product);
        $stock->setQuantity($quantity);

        $this->em->getRepository(Stock::class)->save($stock, true);
    }
}
