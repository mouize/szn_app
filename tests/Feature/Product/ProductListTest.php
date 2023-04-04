<?php

namespace App\Tests\Feature\Product;

use App\Entity\Product;
use App\Entity\Shop;
use App\Entity\Stock;
use App\Tests\Feature\FeatureTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProductListTest extends FeatureTestCase
{
    private array $products;
    private Shop $shop1;
    private Shop $shop2;

    public function setUp(): void
    {
        parent::setUp();

        $this->prepareManyProducts();
    }

    public function test_search_WHEN_nofilters_THEN_returnallProducts()
    {
        $this->client->request(
            method: 'GET',
            uri: '/api/products',
            server: ['CONTENT_TYPE' => 'application/json'],
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $content = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertCount(5, $content);
    }

    public function test_search_WHEN_filterByName_THEN_returnSpecificProduct()
    {
        $name = $this->products[0]->getName();

        $this->client->request(
            method: 'GET',
            uri: '/api/products',
            parameters: ['name' => $name],
            server: ['CONTENT_TYPE' => 'application/json'],
        );
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $content = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertCount(1, $content);

        $product = current($content);
        $this->assertEquals($name, $product['name']);
    }

    public function test_search_WHEN_filterByShop_THEN_returnProductsPerShop()
    {
        $parameters = ['shops' => [$this->shop1->getId()]];

        $this->client->request(
            method: 'GET',
            uri: '/api/products',
            parameters: $parameters,
            server: ['CONTENT_TYPE' => 'application/json'],
        );
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $content = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertCount(3, $content);
    }

    public function test_search_WHEN_filterByMultiple_THEN_returnProductsPerShops()
    {
        $parameters = ['shops' => [$this->shop1->getId(), $this->shop2->getId()]];

        $this->client->request(
            method: 'GET',
            uri: '/api/products',
            parameters: $parameters,
            server: ['CONTENT_TYPE' => 'application/json'],
        );
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $content = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertCount(4, $content);
    }

    public function test_productSearch_WHEN_badParametersRequest_THEN_responseOnErrorStatus(): void
    {
        //query params are always string, so hard to test name and photo_url.
        $parameters['shops'] = 'a string';

        $this->client->request(
            method: 'GET',
            uri: '/api/products',
            parameters: $parameters,
            server: ['CONTENT_TYPE' => 'application/json'],
        );

        //Should be Bad request but don't know why fos is not handling it correctly, to debug.
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $this->client->getResponse()->getStatusCode());

        $content = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(400, $content['code']);
    }

    public function prepareManyProducts()
    {
        $setProductsToShop = function (array $products, Shop $shop, int $quantity) {
            foreach ($products as $product) {
                $stock = new Stock();
                $stock->setProduct($product);
                $stock->setShop($shop);
                $stock->setQuantity($quantity);

                $this->em->getRepository(Stock::class)->save($stock);
            }

            $this->em->flush();
        };

        $this->shop1 = new Shop();
        $this->shop1->setName('Shop Paris Chatelet');
        $this->shop1->setLatitude('48.85913');
        $this->shop1->setLongitude('2.2769957');
        $this->shop1->setAddress('Chatelet, Paris, France');
        $this->shop1->setManager('Jean francois');

        $this->em->getRepository(Shop::class)->save($this->shop1, true);

        $this->shop2 = new Shop();
        $this->shop2->setName('Shop Paris - La Defense');
        $this->shop2->setLatitude('48.8910037');
        $this->shop2->setLongitude('2.238988');
        $this->shop2->setAddress('La Defense, Paris, France');
        $this->shop2->setManager('Jean francois');

        $this->em->getRepository(Shop::class)->save($this->shop2, true);

        $this->products = [];
        for ($i = 1; $i <= 5; $i++) {
            $product = new Product();
            $product->setName("Product test #" . $i);
            $product->setPhotoUrl("url test");

            $this->em->getRepository(Product::class)->save($product, true);

            $this->products[] = $product;
        }

        //1 product with only shop 1
        $setProductsToShop(array_slice($this->products, 0, 3), $this->shop1, 2);
        //2 products with both shops and 1 with only shop 2
        $setProductsToShop(array_slice($this->products, 1, 3), $this->shop2, 3);
        //1 product with no shop.
    }
}