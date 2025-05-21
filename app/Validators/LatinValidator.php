<?php

namespace Validators;

use kostyaaga\Validator\Core\AbstractValidator;

class LatinValidator extends AbstractValidator
{
    protected string $message = "Логин должен содержать только латинские буквы и цифры. И цифры после буквы";

    public function rule(): bool
    {
        return preg_match('/^[a-zA-Z][a-zA-Z0-9\s-]*$/', $this->value) === 1;
    }
}
