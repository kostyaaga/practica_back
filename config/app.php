<?php
return [
    'auth' => \Src\Auth\Auth::class,
    'identity'=>\Model\User::class,
    'routeMiddleware' => [
        'auth' => \Middlewares\AuthMiddleware::class,
        'admin' => \Middlewares\AdminMiddleware::class,
    ],
    'validators' => [
        'required' => \Validators\RequireValidator::class,
        'unique' => \Validators\UniqueValidator::class,
        'name' => \Validators\CyrillicValidator::class,
        'login' => \Validators\LatinValidator::class,
        'password' => \Validators\PasswordComplexityValidator::class,
        'min_length' => \Validators\MinLengthValidator::class,
        'max_length' => \Validators\MaxLengthValidator::class,
    ],
    'routeAppMiddleware' => [
        'csrf' => \Middlewares\CSRFMiddleware::class,
        'trim' => \Middlewares\TrimMiddleware::class,
        'specialChars' => \Middlewares\SpecialCharsMiddleware::class,
    ],


];
