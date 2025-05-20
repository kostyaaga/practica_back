<?php

namespace Validators;

use kostyaaga\Validator\Core\AbstractValidator;

class LatinValidator extends AbstractValidator
{
    protected string $message = "Логин должен содержать только латинские буквы и цифры.";

    public function rule(): bool
    {
        return preg_match('/^[a-zA-Z0-9\-_]+$/', $this->value) === 1;
    }
}
