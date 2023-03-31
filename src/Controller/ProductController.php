<?php

namespace App\Controller;

use App\UseCase\Command\CreateProductCommand;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

class ProductController extends AbstractFOSRestController
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    /**
     * @Rest\Post("/products")
     *
     * @Rest\View(serializerGroups={"shop"})
     */
    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $command = new CreateProductCommand(
            $data['name'],
            $data['photo_url'] ?? null,
        );

        $this->bus->dispatch($command);

        return $this->handleView($this->view(null, Response::HTTP_CREATED));
    }
}
