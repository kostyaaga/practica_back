<?php
namespace Controller;

use Model\Departments;
use Model\Room;
use Model\Type;
use Model\User;
use Model\Building;
use Src\Request;
use Src\Validator\Validator;
use Src\View;
use Src\Auth\Auth;
class Site
{
    public function list(): string
    {
        $buildings = Building::all();
        $rooms = Room::all();
        $types = Type::all();
        $departments = Departments::all();
        $users = User::all();

        return (new View())->render('site.list', compact('buildings', 'rooms', 'types', 'departments', 'users'));
    }

    public function statistic(): string
    {
        $buildings = Building::all();
        $rooms = Room::all();
        $types = Type::all();
        $departments = Departments::all();
        $users = User::all();

        foreach ($buildings as $building) {
            $building->total_seats = 0;

            foreach ($rooms as $room) {
                if ($room->building_id == $building->users_id) {
                    $building->total_seats += $room->seats;
                }
            }
        }

        return (new View())->render('site.statistic', compact('buildings', 'rooms', 'types', 'departments', 'users'));
    }

    public function signup(Request $request): string
    {
        if ($request->method === 'POST') {

            $validator = new Validator($request->all(), [
                'name' => ['required'],
                'login' => ['required', 'unique:users,login'],
                'password' => ['required']
            ], [
                'required' => 'Поле :field пусто',
                'unique' => 'Поле :field должно быть уникально'
            ]);

            if($validator->fails()){
                return new View('site.signup',
                    ['message' => json_encode($validator->errors(), JSON_UNESCAPED_UNICODE)]);
            }

            if (User::create($request->all())) {
                app()->route->redirect('/login');
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
