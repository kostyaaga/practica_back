<?php
namespace Validators;

use Src\Validator\AbstractValidator;

class MinLengthValidator extends AbstractValidator
{
    protected string $message = 'Поле :field должно быть не короче :min символов';

    public function rule(): bool
    {
        $minLength = (int)$this->args[0] ?? 0;
        return mb_strlen((string)$this->value) >= $minLength;
    }

    protected function messageError(): string
    {
        return str_replace(
            [':field', ':min'],
            [$this->field, $this->args[0]],
            $this->message
        );
    }
}