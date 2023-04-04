<?php

namespace App\RequestValidator;

use Symfony\Component\Validator\Constraints as Assert;

class ProductCreateRequestValidator extends BaseRequestValidator
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    protected $name;

    #[Assert\Type('string')]
    protected $photo_url;
}