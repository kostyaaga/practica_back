<?php

namespace Validators;

use kostyaaga\Validator\Core\AbstractValidator;

class PasswordComplexityValidator extends AbstractValidator
{
    protected string $message = "Пароль должен содержать хотя бы одну заглавную букву и один спецсимвол.";

    public function rule(): bool
    {
        $hasUpper = preg_match('/[A-ZА-ЯЁ]/', $this->value);
        $hasSpecial = preg_match('/[\W_]/', $this->value);

        return $hasUpper && $hasSpecial;
    }
}
