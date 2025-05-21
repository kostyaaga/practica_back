<?php

namespace Validators;

use kostyaaga\Validator\Core\AbstractValidator;

class CyrillicValidator extends AbstractValidator
{
    protected string $message = "Имя должно содержать только кириллицу.";

    public function rule(): bool
    {
        return preg_match('/^[А-ЯЁ][а-яёА-ЯЁ\s-]*$/u', $this->value) === 1;
    }
}
