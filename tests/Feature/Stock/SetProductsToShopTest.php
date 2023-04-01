<?php

namespace App\Tests\Feature\Stock;

use App\Document\ProductLog;
use App\Entity\Product;
use App\Entity\Shop;
use App\Entity\Stock;
use App\Tests\Feature\FeatureTestCase;
use Symfony\Component\HttpFoundation\Response;

class SetProductsToShopTest extends FeatureTestCase
{
    public function test_addProductsToShop_WHEN_creatingAssociation_THEN_success(): void
    {
        [$shop, $product] = $this->prepareShopAndProducts();

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
        $productLog = $this->dm->getRepository(ProductLog::class)->findOneBy([
            'productId' => $product->getId(),
        ]);
        $this->dm->refresh($productLog);

        $shopLog = current($productLog->getShops());
        $this->assertEquals($shop->getId(), $shopLog['id']);
        $this->assertEquals(4, $shopLog['quantity']);
    }

    public function test_addProductsToShop_WHEN_updatingAssociation_THEN_success(): void
    {
        [$shop, $product] = $this->prepareShopAndProducts();
        //Add a stock
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
        $productLog = $this->dm->getRepository(ProductLog::class)->findOneBy([
            'productId' => $product->getId(),
        ]);
        $this->dm->refresh($productLog);

        $this->assertCount(1, $productLog->getShops());

        $shopLog = current($productLog->getShops());
        $this->assertEquals($shop->getId(), $shopLog['id']);
        $this->assertEquals(4, $shopLog['quantity']);
    }

    /*
     * Need to prepare some data first, dunno what are best practice with symfony for that.
     */
    private function prepareShopAndProducts(): array
    {
        $shop = new Shop();
        $shop->setName('Shop test');
        $shop->setLatitude('48.8566');
        $shop->setLongitude('2.3522');
        $shop->setAddress('Paris, France');
        $shop->setManager('John Doe');

        $this->em->getRepository(Shop::class)->save($shop, true);

        $product = new Product();
        $product->setName('Product test');
        $product->setPhotoUrl('url test');

        $this->em->getRepository(Product::class)->save($product, true);

        return [$shop, $product];
    }

    private function prepareStock(Shop $shop, Product $product): void
    {
        $stock = new Stock();
        $stock->setShop($shop);
        $stock->setProduct($product);
        $stock->setQuantity(1);

        $this->em->getRepository(Stock::class)->save($stock, true);
    }
}
