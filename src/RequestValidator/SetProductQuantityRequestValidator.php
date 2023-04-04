<?php

namespace App\RequestValidator;

use Symfony\Component\Validator\Constraints as Assert;

class SetProductQuantityRequestValidator extends BaseRequestValidator
{
    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    protected $quantity;
}