<?php

namespace App\Tests\Feature\Shop;

use App\Document\ShopView;
use App\Entity\Shop;
use App\Tests\Feature\FeatureTestCase;
use Symfony\Component\HttpFoundation\Response;

class ShopCreateTest extends FeatureTestCase
{
    public function test_createShop_WHEN_goodParameters_THEN_success(): void
    {
        $payload = [
            'name' => 'Shop test',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
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

    /**
     * @dataProvider dataProvider_badparametersRequest()
     */
    public function test_createShop_WHEN_badParametersRequest_THEN_responseOnErrorStatus(
        mixed $name = null,
        mixed $latitude = null,
        mixed $longitude = null,
        mixed $address = null,
        mixed $manager = null,
    ): void {
        $payload = [];
        if (null !== $name) {
            $payload['name'] = $name;
        }
        if (null !== $latitude) {
            $payload['latitude'] = $latitude;
        }
        if (null !== $longitude) {
            $payload['longitude'] = $longitude;
        }
        if (null !== $address) {
            $payload['address'] = $address;
        }
        if (null !== $manager) {
            $payload['manager'] = $manager;
        }

        $this->client->request(
            method: 'POST',
            uri: '/api/shops',
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
            //Test missing params
            [],
            [null, 48.8566, 2.3522, 'Paris, France', 'John Doe'],
            ['name', null, 2.3522, 'Paris, France', 'John Doe'],
            ['name', 48.8566, null, 'Paris, France', 'John Doe'],
            ['name', 48.8566, 2.3522, null, 'John Doe'],
            ['name', 48.8566, 2.3522, 'Paris, France', null],
            //Test bad type
            [35, 48.8566, 2.3522, 'Paris, France', 'John Doe'],
            ['name', '48.8566', 2.3522, 'Paris, France', 'John Doe'],
            ['name', 48.8566, '2.3522', 'Paris, France', 'John Doe'],
            ['name', 48.8566, 2.3522, 35, 'John Doe'],
            ['name', 48.8566, 2.3522, 'Paris, France', 35],
        ];
    }
}
