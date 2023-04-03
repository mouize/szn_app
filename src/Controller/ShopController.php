<?php

namespace App\Controller;

use App\Service\CQSBus\CommandBus;
use App\Service\CQSBus\QueryBus;
use App\UseCase\Command\CreateShopCommand;
use App\UseCase\Command\SetProductToShopCommand;
use App\UseCase\Query\SearchShopQuery;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShopController extends AbstractFOSRestController
{
    public function __construct(
        private QueryBus $queryBus,
        private CommandBus $commandBus,
    )
    {
    }

    /**
     * @Rest\Post("/shops")
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

        $this->commandBus->dispatch($command);

        return $this->handleView($this->view(null, Response::HTTP_CREATED));
    }

    /**
     * @Rest\Put("/shops/{shopId}/products/{productId}")
     */
    public function setProductToShop(
        int $shopId,
        int $productId,
        Request $request,
    ): Response {
        $data = json_decode($request->getContent(), true);
        $quantity = $data['quantity'];

        $command = new SetProductToShopCommand($shopId, $productId, $quantity);
        $this->commandBus->dispatch($command);

        return $this->handleView($this->view(null, Response::HTTP_NO_CONTENT));
    }

    /**
     * @Rest\Get("/shops")
     */
    public function search(Request $request): Response
    {
        $name = $request->get('name');
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $distance = $request->get('distance');
        $page = $request->get('page', 1);

        //Need to implement request validation

        $shops = $this->queryBus->handle(
            new SearchShopQuery($name, $latitude, $longitude, $distance, $page -1 , 10)
        );

        return $this->handleView($this->view($shops, Response::HTTP_OK));
    }
}
