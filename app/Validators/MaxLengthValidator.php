<?php
namespace Validators;

use Src\Validator\AbstractValidator;

class MaxLengthValidator extends AbstractValidator
{
    protected string $message = 'Поле :field должно быть не длиннее :max символов';

    public function rule(): bool
    {
        $maxLength = (int)$this->args[0] ?? 0;
        return mb_strlen((string)$this->value) <= $maxLength;
    }

    protected function messageError(): string
    {
        return str_replace(
            [':field', ':max'],
            [$this->field, $this->args[0]],
            $this->message
        );
    }
}