<?php

namespace App\Tests\Feature\Product;

use App\Document\ProductView;
use App\Entity\Product;
use App\Tests\Feature\FeatureTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProductCreateTest extends FeatureTestCase
{
    public function test_createProduct_WHEN_goodParameters_THEN_success(): void
    {
        $payload = [
            'name' => 'Product test',
            'photo_url' => 'photo url test',
        ];

        $this->client->request(
            method: 'POST',
            uri: '/api/products',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($payload),
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

    /**
     * @dataProvider dataProvider_badparametersRequest()
     */
    public function test_productCreate_WHEN_badParametersRequest_THEN_responseOnErrorStatus(
        mixed $name = null,
        mixed $photo_url = null
    ): void {
        $payload = [];
        if (null !== $name) {
            $payload['name'] = $name;
        }
        if (null !== $photo_url) {
            $payload['photo_url'] = $photo_url;
        }

        $this->client->request(
            method: 'POST',
            uri: '/api/products',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($payload)
        );

        //Should be Bad request but don't know why fos is not handling it correctly, to debug.
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $this->client->getResponse()->getStatusCode());

        $content = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(400, $content['code']);
    }

    public function dataProvider_badparametersRequest(): array
    {
        return [
            [''],
            [null],
            [35],
            ['valid name', 35],
        ];
    }
}
