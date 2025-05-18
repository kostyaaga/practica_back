<?php

namespace Validators;

use Src\Validator\AbstractValidator;

class CyrillicValidator extends AbstractValidator
{
    protected string $message = "Имя должно содержать только кириллицу.";

    public function rule(): bool
    {
        return preg_match('/^[а-яёА-ЯЁ\s\-]+$/u', $this->value) === 1;
    }
}
