<?php

namespace App\Tests\Feature\Shop;

use App\Document\ShopView;
use App\Entity\Shop;
use App\Tests\Feature\FeatureTestCase;
use Symfony\Component\HttpFoundation\Response;

class ShopCreateTest extends FeatureTestCase
{
    public function test_create_WHEN_goodParameters_THEN_success(): void
    {
        $payload = [
            'name' => 'Shop test',
            'latitude' => '48.8566',
            'longitude' => '2.3522',
            'address' => 'Paris, France',
            'manager' => 'John Doe'
        ];

        $this->client->request(
            method: 'POST',
            uri: '/api/shops',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($payload)
        );

        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
        $this->assertEmpty($this->client->getResponse()->getContent());

        $shops = $this->em->getRepository(Shop::class)->findBy($payload);
        $this->assertCount(1, $shops);

        $shop = current($shops);
        $shopView = $this->dm->getRepository(ShopView::class)->findOneBy(['shopId' => $shop->getId()]);
        $this->assertNotNull($shopView);
    }
}
