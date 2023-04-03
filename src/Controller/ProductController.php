<?php

namespace App\Controller;

use App\Service\CQSBus\CommandBus;
use App\Service\CQSBus\QueryBus;
use App\UseCase\Command\CreateProductCommand;
use App\UseCase\Query\SearchProductQuery;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractFOSRestController
{
    public function __construct(
        private QueryBus $queryBus,
        private CommandBus $commandBus,
    ) {
    }

    /**
     * @Rest\Post("/products")
     */
    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $command = new CreateProductCommand(
            $data['name'],
            $data['photo_url'] ?? null,
        );

        $this->commandBus->dispatch($command);

        return $this->handleView($this->view(null, Response::HTTP_CREATED));
    }

    /**
     * @Rest\Get("/products")
     *
     * @Rest\QueryParam(name="page", requirements="\[1-9]+", nullable=false)
     */
    public function search(Request $request): Response
    {
        $name = $request->get('name');
        $shops = $request->get('shops', []);
        $page = $request->get('page', 1);

        $shops = $this->queryBus->handle(
            new SearchProductQuery($name, $shops, $page - 1, 10),
        );

        return $this->handleView($this->view($shops, Response::HTTP_OK));
    }
}
