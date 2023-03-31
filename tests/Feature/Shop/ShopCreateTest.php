<?php

namespace App\Tests\Feature\Shop;

use App\Entity\Shop;
use App\Repository\ORM\ShopRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ShopCreateTest extends WebTestCase
{
    private ShopRepository $repository;
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $kernel = self::bootKernel();

        $this->repository = $kernel->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository(Shop::class);
    }

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
        $this->assertEquals(1, $this->repository->count($payload));
    }
}
