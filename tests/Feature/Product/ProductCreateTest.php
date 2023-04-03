<?php

namespace App\Tests\Feature\Product;

use App\Document\ProductView;
use App\Entity\Product;
use App\Tests\Feature\FeatureTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProductCreateTest extends FeatureTestCase
{
    public function test_create_WHEN_goodParameters_THEN_success(): void
    {
        $payload = [
            'name' => 'Product test',
            'photo_url' => 'photo url test',
        ];

        $this->client->request(
            method: 'POST',
            uri: '/api/products',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($payload)
        );

        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
        $this->assertEmpty($this->client->getResponse()->getContent());

        $products = $this->em->getRepository(Product::class)->findBy([
            'name' => $payload['name'],
            'photoUrl' => $payload['photo_url'],
        ]);
        $this->assertCount(1, $products);

        //check data saved on mongodb
        $product = current($products);
        $productView = $this->dm->getRepository(ProductView::class)->findOneBy([
            'productId' => $product->getId(),
        ]);
        $this->assertNotNull($productView);
    }
}
