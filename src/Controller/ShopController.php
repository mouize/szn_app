<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Shop;
use App\RequestValidator\SetProductQuantityRequestValidator;
use App\RequestValidator\ShopCreateRequestValidator;
use App\RequestValidator\ShopSearchRequestValidator;
use App\Service\CQSBus\CommandBus;
use App\Service\CQSBus\QueryBus;
use App\UseCase\Command\CreateShopCommand;
use App\UseCase\Command\SetProductQuantityCommand;
use App\UseCase\Query\SearchShopQuery;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

class ShopController extends AbstractFOSRestController
{
    public function __construct(
        private QueryBus $queryBus,
        private CommandBus $commandBus,
    ) {
    }

    /**
     * @Rest\Post("/shops")
     */
    public function create(Request $request, ShopCreateRequestValidator $validator): Response
    {
        $validator->validate();

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
     * @Rest\Put("/shops/{id}/products/{product_id}")
     * @Entity("product", expr="repository.find(product_id)")
     */
    public function setProductQuantity(
        Shop $shop,
        Product $product,
        Request $request,
        SetProductQuantityRequestValidator $validator,
    ): Response {
        $validator->validate();

        $data = json_decode($request->getContent(), true);
        $quantity = $data['quantity'];

        $command = new SetProductQuantityCommand($shop->getId(), $product->getId(), $quantity);
        $this->commandBus->dispatch($command);

        return $this->handleView($this->view(null, Response::HTTP_NO_CONTENT));
    }

    /**
     * @Rest\Get("/shops")
     */
    public function search(Request $request, ShopSearchRequestValidator $validator): Response
    {
        $validator->validate();

        $name = $request->get('name');
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $distance = $request->get('distance');
        $page = $request->get('page', 1);

        $shops = $this->queryBus->handle(
            new SearchShopQuery($name, $latitude, $longitude, $distance, $page - 1, 10)
        );

        return $this->handleView($this->view($shops, Response::HTTP_OK));
    }
}
