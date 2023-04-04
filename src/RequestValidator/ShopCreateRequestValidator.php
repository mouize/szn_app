<?php

namespace App\RequestValidator;

use Symfony\Component\Validator\Constraints as Assert;

class ShopCreateRequestValidator extends BaseRequestValidator
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    protected $name;

    #[Assert\NotBlank]
    #[Assert\Type('float')]
    protected $latitude;

    #[Assert\NotBlank]
    #[Assert\Type('float')]
    protected $longitude;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    protected $address;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    protected $manager;
}