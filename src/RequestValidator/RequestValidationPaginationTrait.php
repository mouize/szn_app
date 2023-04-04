<?php

namespace App\RequestValidator;

use Symfony\Component\Validator\Constraints as Assert;

trait RequestValidationPaginationTrait
{
    #[Assert\Positive]
    protected $page;
}