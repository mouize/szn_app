<?php

namespace App\Tests\Feature\Product;

use App\Entity\Product;
use App\Repository\ORM\ProductRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProductCreateTest extends WebTestCase
{
    private ProductRepository $repository;
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $kernel = self::bootKernel();

        $this->repository = $kernel->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository(Product::class);
    }

    public function test_create_WHEN_goodParameters_THEN_success(): void
    {
        $payload = [
            'name' => 'Shop test',
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
        $this->assertEquals(1, $this->repository->count([
            'name' => $payload['name'],
            'photoUrl' => $payload['photo_url'],
        ]));
    }
}
