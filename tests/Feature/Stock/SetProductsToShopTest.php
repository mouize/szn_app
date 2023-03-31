<?php

namespace App\Tests\Feature\Stock;

use App\Entity\Product;
use App\Entity\Shop;
use App\Entity\Stock;
use App\Repository\ORM\ProductRepository;
use App\Repository\ORM\ShopRepository;
use App\Repository\ORM\StockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SetProductsToShopTest extends WebTestCase
{
    private ShopRepository $shopRepository;
    private ProductRepository $productRepository;
    private StockRepository $stockRepository;
    private KernelBrowser $client;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $kernel = self::bootKernel();
        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->shopRepository = $this->em->getRepository(Shop::class);
        $this->productRepository = $this->em->getRepository(Product::class);
        $this->stockRepository = $this->em->getRepository(Stock::class);
    }

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

        $this->assertEquals(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
        $this->assertEmpty($this->client->getResponse()->getContent());

        $stock = $this->stockRepository->findOneBy(['shop' => $shop, 'product' => $product]);
        $this->assertEquals(4, $stock->getQuantity());
    }

    public function test_addProductsToShop_WHEN_updatingAssociation_THEN_success(): void
    {
        [$shop, $product] = $this->prepareShopAndProducts();
        $this->prepareStock($shop, $product);

        $stock = $this->stockRepository->findOneBy(['shop' => $shop, 'product' => $product]);
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

        $this->em->refresh($stock);
        $this->assertEquals(4, $stock->getQuantity());
        $this->assertCount(1, $this->stockRepository->findAll());
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

        $this->shopRepository->save($shop, true);

        $product = new Product();
        $product->setName('Product test');
        $product->setPhotoUrl('url test');

        $this->productRepository->save($product, true);

        return [$shop, $product];
    }

    private function prepareStock(Shop $shop, Product $product)
    {
        $stock = new Stock();
        $stock->setShop($shop);
        $stock->setProduct($product);
        $stock->setQuantity(1);

        $this->stockRepository->save($stock, true);
    }
}
