<?php
namespace Controller;


use Src\Auth\Auth;
use Src\Request;
use kostyaaga\Validator\Core\Validator;
use Src\View;

class UserController{
    public function signup(Request $request): string
    {
        if ($request->method === 'POST') {

            $validator = new Validator($request->all(), [
                'name' => ['required', 'cyrillic', 'min_length:3', 'max_length:30',],
                'login' => ['required', 'latin', 'unique:users,login', 'min_length:4', 'max_length:30',],
                'password' => ['required', 'password', 'min_length:6',]
            ], [
                'required' => 'Поле :field пусто',
                'unique' => 'Поле :field должно быть уникально',
                'min_length' => 'Поле :field содержит слишком мало символов',
                'max_length' => 'Поле :field содержит слишком много символов',
                'cyrillic' => 'Поле :field должно содержать только кириллические символы',
                'latin' => 'Поле :field должно содержать только латинские символы'
            ]);

            if($validator->fails()){
                return new View('site.signup',
                    ['message' => json_encode($validator->errors(), JSON_UNESCAPED_UNICODE),
                        'errors' => $validator->errors(),
                        'old' => $_POST
                    ]);

            }

            if (\Model\User::createUser($request->all())) {
                app()->route->redirect('/login');
                return '';
            }
        }
        return new View('site.signup');
    }


    public function login(Request $request): string
    {
        if ($request->method === 'GET') {
            return new View('site.login');
        }
        if (Auth::attempt($request->all())) {
            app()->route->redirect('/');
        }
        return new View('site.login', ['message' => 'Неправильные логин или пароль']);
    }

    public function logout(): void
    {
        Auth::logout();
        app()->route->redirect('/statistic');
    }
}
