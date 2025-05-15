<?php
namespace Controller;

use Model\Room;
use Model\Type;
use Model\User;
use Model\Building;
use Src\Request;
use Src\View;
use Src\Auth\Auth;
class Site
{
    public function list(): string
    {
        $buildings = Building::all();
        $rooms = Room::all();
        $types = Type::all();

        return (new View())->render('site.list', compact('buildings', 'rooms', 'types'));
    }

    public function statistic(): string
    {
        $buildings = Building::all();
        $rooms = Room::all();
        $types = Type::all();

        foreach ($buildings as $building) {
            $building->total_seats = 0;

            foreach ($rooms as $room) {
                if ($room->building_id == $building->id) {
                    $building->total_seats += $room->seats;
                }
            }
        }

        return (new View())->render('site.statistic', compact('buildings', 'rooms', 'types'));
    }

    public function signup(Request $request): string
    {

        if ($request->method==='POST' && User::create($request->all())){
            app()->route->redirect('/login');
        }
        return new View('site.signup');
    }

    public function login(Request $request): string
    {
        if ($request->method === 'GET') {
            return new View('site.login');
        }
        if (Auth::attempt($request->all())) {
            app()->route->redirect('/statistic');
        }
        return new View('site.login', ['message' => 'Неправильные логин или пароль']);
    }

    public function logout(): void
    {
        Auth::logout();
        app()->route->redirect('/statistic');
    }

}
