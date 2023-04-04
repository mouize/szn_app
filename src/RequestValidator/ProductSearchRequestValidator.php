<?php

namespace App\RequestValidator;

use Symfony\Component\Validator\Constraints as Assert;

class ProductSearchRequestValidator extends BaseRequestValidator
{
    use RequestValidationPaginationTrait;

    #[Assert\Type('array')]
    protected $shops;
}