<?php

namespace App\Controller;

use App\UseCase\Command\CreateShopCommand;
use App\UseCase\Command\SetProductToShopCommand;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

class ShopController extends AbstractFOSRestController
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    /**
     * @Rest\Post("/shops")
     *
     * @Rest\View(serializerGroups={"shop"})
     */
    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $command = new CreateShopCommand(
            $data['name'],
            $data['latitude'],
            $data['longitude'],
            $data['address'],
            $data['manager']
        );

        $this->bus->dispatch($command);

        return $this->handleView($this->view(null, Response::HTTP_CREATED));
    }

    /**
     * @Rest\Put("/shops/{shopId}/products/{productId}")
     *
     * @Rest\View(serializerGroups={"shop"})
     *
     * @Rest\RequestParam(name="stock", requirements="\d+", nullable=false)
     */
    public function setProductToShop(
        int $shopId,
        int $productId,
        Request $request,
    ): Response {
        $data = json_decode($request->getContent(), true);
        $quantity = $data['quantity'];

        $command = new SetProductToShopCommand($shopId, $productId, $quantity);
        $this->bus->dispatch($command);

        return $this->handleView($this->view(null, Response::HTTP_NO_CONTENT));
    }
}
