<?php

namespace App\RequestValidator;

use App\RequestValidator\Exception\RequestValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BaseRequestValidator
{
    public function __construct(
        protected RequestStack $requestStack,
        protected ValidatorInterface $validator,
    ) {
        $this->populate();
    }

    public function validate()
    {
        $errors = $this->validator->validate($this);

        $messages = [];
        foreach ($errors as $message) {
            $messages[] = json_encode([
                'property' => $message->getPropertyPath(),
                'value' => $message->getInvalidValue(),
                'message' => $message->getMessage(),
            ]);
        }

        if (count($messages) > 0) {
            throw new RequestValidationException($messages);
        }
    }

    protected function getRequest(): Request
    {
        return $this->requestStack->getCurrentRequest();
    }

    protected function populate(): void
    {
        $content = [];
        if ('GET' === $this->getRequest()->getMethod()) {
            $content = $this->getRequest()->query->all();
        } elseif (in_array($this->getRequest()->getMethod(), ['POST', 'PUT', 'PATCH'])) {
            $content = json_decode($this->getRequest()->getContent(), true);
        }

        if (!$content) {
            return;
        }

        foreach ($content as $property => $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }
    }
}