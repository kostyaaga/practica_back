<?php

namespace Src\Validator;

class Validator
{
    //Разрешенные валидаторы
    private array $validators = [];
    //Итоговые ошибки
    private array $errors = [];
    //Проверяемые поля
    private array $fields = [];
    //Массив правил
    private array $rules = [];
    //Кастомные сообщения
    private array $messages = [];

    public function __construct(array $fields, array $rules, array $messages = [])
    {
        $this->validators = app()->settings->app['validators'] ?? [];
        $this->fields = $fields;
        $this->rules = $rules;
        $this->messages = $messages;
        $this->validate();
    }

    //Перебираем список всех валидируемых полей и для
    //каждого поля вызываем метод validateField()
    private function validate(): void
    {
        foreach ($this->rules as $fieldName => $fieldValidators) {
            $this->validateField($fieldName, $fieldValidators);
        }
    }

    //Валидация отдельного поля
    private function validateField(string $fieldName, array $fieldValidators): void
    {
        //Перебираем все валидаторы, ассоциированные с полем
        foreach ($fieldValidators as $validatorName) {
            $tmp = explode(':', $validatorName);
            [$validatorName, $args] = count($tmp) > 1 ? $tmp : [$validatorName, null];
            $args = isset($args) ? explode(',', $args) : [];

            // ПРОВЕРКА — есть ли такой валидатор
            if (!isset($this->validators[$validatorName])) {
                continue;
            }

            $validatorClass = $this->validators[$validatorName];
            if (!class_exists($validatorClass)) {
                continue;
            }

            $validator = new $validatorClass(
                $fieldName,
                $this->fields[$fieldName],
                $args,
                $this->messages[$validatorName] ?? null
            );

            if (!$validator->rule()) {
                $this->errors[$fieldName][] = $validator->validate();
            }
        }

    }

    //Возврат массива найденных ошибок
    public function errors(): array
    {
        return $this->errors;
    }

    //Признак успешной валидации
    public function fails(): bool
    {
        return (bool)count($this->errors);
    }
}
