<?php

namespace Validators;

use kostyaaga\Validator\Core\AbstractValidator;
class RequireValidator extends AbstractValidator
{

    protected string $message = 'Field :field is required';

    public function rule(): bool
    {
        return !empty($this->value);
    }
}
