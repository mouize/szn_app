<?php

namespace App\RequestValidator;

use Symfony\Component\Validator\Constraints as Assert;

class ShopSearchRequestValidator extends BaseRequestValidator
{
    use RequestValidationPaginationTrait;
}