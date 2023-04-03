<?php

namespace App\Tests\Feature\Shop;

use App\Entity\Shop;
use App\Tests\Feature\FeatureTestCase;
use Symfony\Component\HttpFoundation\Response;

class ShopListTest extends FeatureTestCase
{
    /**
     * @dataProvider dataProvider_search_filters
     */
    public function test_search_WHEN_filters_THEN_returnConcernedShops(
        int $expectedCount,
        ?string $name = null,
        ?float $latitude = null,
        ?float $longitude = null,
        ?int $distance = null
    ): void {
        $this->prepareManyShops();

        $parameters = [];
        if ($name) {
            $parameters = ['name' => $name];
        }
        if ($latitude && $longitude && $distance) {
            $parameters['latitude'] = $latitude;
            $parameters['longitude'] = $longitude;
            $parameters['distance'] = $distance;
        }

        //48.8588548,2.347035
        $this->client->request(
            method: 'GET',
            uri: '/api/shops',
            parameters: $parameters,
            server: ['CONTENT_TYPE' => 'application/json'],
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $content = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertCount($expectedCount, $content);
    }

    public function dataProvider_search_filters(): array
    {
        return [
            [3], //All
            [1, 'Shop Paris Chatelet'], //By name
            [0, 'toto'], //Name not existing
            [2, null, 48.8566, 2.3522, 10], //Centered on Paris, distance 10 km
            [1, null, 45.758, 24.800, 10], //Centered on Lyon, 10km
            [1, 'Shop Paris Chatelet', 48.8566, 2.3522, 10], // mixing distance and specific name
        ];
    }

    private function prepareManyShops()
    {
        $shop = new Shop();
        $shop->setName('Shop Paris Chatelet');
        $shop->setLatitude('48.85913');
        $shop->setLongitude('2.2769957');
        $shop->setAddress('Chatelet, Paris, France');
        $shop->setManager('Jean francois');

        $this->em->getRepository(Shop::class)->save($shop);

        $shop = new Shop();
        $shop->setName('Shop Paris - La Defense');
        $shop->setLatitude('48.8910037');
        $shop->setLongitude('2.238988');
        $shop->setAddress('La Defense, Paris, France');
        $shop->setManager('Jean francois');

        $this->em->getRepository(Shop::class)->save($shop);

        $shop = new Shop();
        $shop->setName('Shop Lyon');
        $shop->setLatitude('45.7580052');
        $shop->setLongitude('24.8001108');
        $shop->setAddress('Lyon, France');
        $shop->setManager('Jean michel');

        $this->em->getRepository(Shop::class)->save($shop);

        $this->em->flush();
    }
}