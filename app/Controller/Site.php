<?php

namespace Controller;

use Exception;

use Model\Building;
use Model\Departments;
use Model\Room;
use Model\Type;
use Model\User;
use Src\Auth\Auth;
use Src\View;

class Site{
    public function list(): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
            $deleteId = (int)$_POST['delete_id'];

            // Находим модель корпуса по id и удаляем из базы
            $building = Building::find($deleteId);
            if ($building) {
                $building->delete();
            }

            // Перенаправляем, чтобы избежать повторного удаления
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }

        // Теперь грузим данные
        $buildings = Building::all();
        $rooms = Room::all();
        $types = Type::all();
        $departments = Departments::all();
        $users = User::all();

        $search = $_GET['search'] ?? '';

        if ($search !== '') {
            $buildings = $buildings->filter(function ($building) use ($search) {
                return mb_stripos($building->name, $search) !== false;
            })->all();
        }

        return (new View())->render('site.list', compact('buildings', 'rooms', 'types', 'departments', 'users'));
    }

    public function addBuilding(\Src\Request $request): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $token = $request->body['csrf_token'] ?? '';
        Auth::checkCSRF($token);

        $types = Type::all();
        $departments = Departments::all();
        $users = User::all();

        $errors = [];
        $name = '';
        $address = '';
        $total_floors = 0;
        $total_area = 0;
        $used_area = 0;
        $roomsData = [];
        $departmentsData = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $total_floors = (int)($_POST['total_floors'] ?? 0);
            $total_area = (float)($_POST['total_area'] ?? 0);
            $used_area = (float)($_POST['used_area'] ?? 0);
            $roomsData = $_POST['rooms'] ?? [];
            $departmentsData = $_POST['departments'] ?? [];

            if (!app()->auth::checkCSRF($_POST['csrf_token'] ?? '')) {
                $errors[] = 'Неверный CSRF токен';
            }

            if (isset($_POST['action_update_floors'])) {
                if ($total_floors < 1) {
                    $errors[] = 'Количество этажей должно быть больше 0';
                }
            } elseif (isset($_POST['action_add_room'])) {
                $roomsData[] = ['number' => '', 'area' => '', 'seats' => '', 'floor' => 1, 'type' => 1];
            } elseif (isset($_POST['action_add_department'])) {
                $departmentsData[] = ['type' => 'new', 'name' => '', 'users_id' => 0];
            } elseif (isset($_POST['action_save'])) {
                if ($name === '') $errors[] = 'Название корпуса обязательно';
                if ($address === '') $errors[] = 'Адрес обязателен';
                if ($total_floors < 1) $errors[] = 'Количество этажей должно быть больше 0';

                foreach ($roomsData as $index => $room) {
                    if (empty($room['number'])) continue;
                    if ((int)$room['floor'] < 1 || (int)$room['floor'] > $total_floors) {
                        $errors[] = "Кабинет #$index: Этаж указан неверно";
                    }
                }

                // Валидация кафедр
                foreach ($departmentsData as $index => $dept) {
                    if (!isset($dept['type']) || !in_array($dept['type'], ['new', 'existing'])) {
                        $errors[] = "Кафедра #$index: Не выбран тип кафедры";
                        continue;
                    }
                    if ($dept['type'] === 'new') {
                        if (empty(trim($dept['name'] ?? ''))) {
                            $errors[] = "Кафедра #$index: Название новой кафедры обязательно";
                        }
                    } else {
                        if (empty($dept['existing_name'])) {
                            $errors[] = "Кафедра #$index: Не выбрана существующая кафедра";
                        }
                    }
                }

                if (!$errors) {
                    $building = new Building();
                    $building->name = $name;
                    $building->address = $address;
                    $building->total_floors = $total_floors;
                    $building->total_area = $total_area;
                    $building->used_area = $used_area;
                    $building->save();

                    foreach ($roomsData as $roomData) {
                        if (empty($roomData['number'])) continue;

                        $room = new Room();
                        $room->number = $roomData['number'];
                        $room->area = $roomData['area'];
                        $room->seats = $roomData['seats'];
                        $room->floor = $roomData['floor'];
                        $room->type = $roomData['type'];
                        $room->building_id = $building->id;
                        $room->save();
                    }


                    foreach ($departmentsData as $deptData) {
                        if ($deptData['type'] === 'new') {
                            $department = new Departments();
                            $department->name = trim($deptData['name']);
                            $department->building_id = $building->id;
                            $department->users_id = $deptData['users_id'];
                            $department->save();
                        } else {
                            $department = Departments::where('name', $deptData['existing_name'])->first();
                            if ($department) {
                                $department->building_id = $building->id;
                                $department->users_id = $deptData['users_id'];
                                $department->save();
                            }
                        }
                    }


                    app()->route->redirect('/');
                }
            }
        }

        if ($total_floors < 1) $total_floors = 1;
        $floors = range(1, $total_floors);

        return (new View())->render('site.add_building', compact(
            'types', 'departments', 'errors', 'name', 'address', 'total_floors',
            'total_area', 'used_area', 'roomsData', 'departmentsData', 'floors', 'users'
        ));
    }


    public function statistic(): string
    {
        $buildings = Building::all();
        $rooms = Room::all();
        $types = Type::all();
        $departments = Departments::all();
        $users = User::all();

        // Считаем общее количество мест для каждого здания
        foreach ($buildings as $building) {
            $building->total_seats = 0;
            foreach ($rooms as $room) {
                if ($room->building_id == $building->id) {
                    $building->total_seats += $room->seats;
                }
            }
        }

        // Получаем параметры фильтра из GET-запроса
        $filterName = $_GET['name'] ?? null;
        $filterTotalAreaMin = $_GET['total_area_min'] ?? null;
        $filterTotalAreaMax = $_GET['total_area_max'] ?? null;
        $filterUsedAreaMin = $_GET['used_area_min'] ?? null;
        $filterUsedAreaMax = $_GET['used_area_max'] ?? null;
        $filterAddress = $_GET['address'] ?? null;
        $filterTotalFloors = $_GET['total_floors'] ?? null;

        // Фильтруем здания по условиям
        $filteredBuildings = $buildings->filter(function($building) use (
            $filterName, $filterTotalAreaMin, $filterTotalAreaMax,
            $filterUsedAreaMin, $filterUsedAreaMax, $filterAddress, $filterTotalFloors
        ) {
            if ($filterName && stripos($building->name, $filterName) === false) {
                return false;
            }
            if ($filterAddress && stripos($building->address, $filterAddress) === false) {
                return false;
            }
            if ($filterTotalFloors && $building->total_floors != (int)$filterTotalFloors) {
                return false;
            }
            if ($filterTotalAreaMin && $building->total_area < (float)$filterTotalAreaMin) {
                return false;
            }
            if ($filterTotalAreaMax && $building->total_area > (float)$filterTotalAreaMax) {
                return false;
            }
            if ($filterUsedAreaMin && $building->used_area < (float)$filterUsedAreaMin) {
                return false;
            }
            if ($filterUsedAreaMax && $building->used_area > (float)$filterUsedAreaMax) {
                return false;
            }
            return true;
        });

        // Сброс ключей и преобразование в массив
        $filteredBuildings = $filteredBuildings->values()->all();

        // Передаём в шаблон отфильтрованные здания и остальные данные
        return (new View())->render('site.statistic', compact('filteredBuildings', 'rooms', 'types', 'departments', 'users'));
    }


    public function editBuilding()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
        } else {
            $id = $_GET['id'] ?? null;
        }

        if (!$id) {
            throw new \Exception('Не указан ID здания');
        }

        $id = (int)$id;

        $building = Building::find($id);
        if (!$building) {
            throw new \Exception('Здание не найдено');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            if (!app()->auth::checkCSRF($token)) {
                throw new \Exception('Ошибка проверки CSRF');
            }

            // Получаем данные из формы
            $name = $_POST['name'] ?? '';
            $address = $_POST['address'] ?? '';
            $total_floors = $_POST['total_floors'] ?? '';

            // Валидация данных (при необходимости)
            $building->name = $name;
            $building->address = $address;
            $building->total_floors = (int)$total_floors;
            $building->save();

            header('Location: /');
            exit();
        }

        return (new View())->render('site.edit_building', compact('building'));
    }

}

