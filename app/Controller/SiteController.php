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

class SiteController
{
    public function list(): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
            $deleteId = (int)$_POST['delete_id'];

            $building = Building::find($deleteId);
            if ($building) {
                $building->delete();
            }

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

        $filterName = $_GET['name'] ?? null;
        $filterTotalAreaMin = $_GET['total_area_min'] ?? null;
        $filterTotalAreaMax = $_GET['total_area_max'] ?? null;
        $filterUsedAreaMin = $_GET['used_area_min'] ?? null;
        $filterUsedAreaMax = $_GET['used_area_max'] ?? null;
        $filterAddress = $_GET['address'] ?? null;
        $filterTotalFloors = $_GET['total_floors'] ?? null;

        // Фильтруем здания по условиям
        $filteredBuildings = $buildings->filter(function ($building) use (
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

        $filteredBuildings = $filteredBuildings->values()->all();

        return (new View())->render('site.statistic', compact('filteredBuildings', 'rooms', 'types', 'departments', 'users'));
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
                // Рассчитываем занятую площадь
                $usedArea = array_reduce($roomsData, function($carry, $room) {
                    return $carry + (float)($room['area'] ?? 0);
                }, 0);

                // Проверяем свободную площадь
                $availableArea = $total_area - $used_area;
                if ($availableArea > 0) {
                    $roomsData[] = [
                        'number' => '',
                        'area' => '',
                        'seats' => '',
                        'floor' => 1,
                        'type' => 1
                    ];
                } else {
                    $errors[] = 'Недостаточно свободной площади для добавления новой комнаты';
                }
            } elseif (isset($_POST['action_add_department'])) {
                $departmentsData[] = ['type' => 'new', 'name' => '', 'users_id' => 0];
            } elseif (isset($_POST['action_save'])) {
                if ($name === '') $errors[] = 'Название корпуса обязательно';
                if ($address === '') $errors[] = 'Адрес обязателен';
                if ($total_floors < 1) $errors[] = 'Количество этажей должно быть больше 0';

                // Проверка площади комнат
                $totalRoomsArea = 0;
                foreach ($roomsData as $index => $room) {
                    if (empty($room['number'])) continue;

                    $roomArea = (float)($room['area'] ?? 0);
                    $totalRoomsArea += $roomArea;

                    if ((int)$room['floor'] < 1 || (int)$room['floor'] > $total_floors) {
                        $errors[] = "Кабинет #$index: Этаж указан неверно";
                    }
                }

                if ($totalRoomsArea > $total_area) {
                    $errors[] = 'Общая площадь комнат ('.$totalRoomsArea.') превышает общую площадь здания ('.$total_area.')';
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

    public function editBuilding(\Src\Request $request): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $token = $request->body['csrf_token'] ?? '';
        Auth::checkCSRF($token);

        $types = Type::all();
        $departmentsList = Departments::all();
        $users = User::all();

        $errors = [];
        $isEdit = isset($request->body['id']);
        $building = $isEdit ? Building::find((int)$request->body['id']) : new Building();

        if (!$building && $isEdit) {
            throw new \Exception('Здание не найдено');
        }

        // Получаем данные из запроса
        $name = trim($request->body['name'] ?? '');
        $address = trim($request->body['address'] ?? '');
        $total_floors = (int)($request->body['total_floors'] ?? 1);
        $total_area = (float)($request->body['total_area'] ?? 0);
        $used_area = (float)($request->body['used_area'] ?? 0);
        $roomsData = $request->body['rooms'] ?? [];
        $floors = range(1, max(1, $building->total_floors));

        if (isset($_SESSION['building_data'])) {
            $building->fill($_SESSION['building_data']);
            unset($_SESSION['building_data']);
        }

        // Восстанавливаем ошибки если они есть
        if (isset($_SESSION['errors'])) {
            $errors = $_SESSION['errors'];
            unset($_SESSION['errors']);
        } else {
            $errors = [];
        }

        // Обработка действия обновления этажей
        if (isset($request->body['action_update_floors'])) {
            if ($total_floors < 1) {
                $_SESSION['errors'] = ['Количество этажей должно быть больше 0'];
            } else {
                // Сохраняем данные в сессии
                $_SESSION['building_data'] = [
                    'name' => $name,
                    'address' => $address,
                    'total_floors' => $total_floors,
                    'total_area' => $total_area,
                    'used_area' => $used_area
                ];
            }

            app()->route->redirect('/edit_building?id=' . $building->id);
            return '';
        }

        if (isset($request->body['action_add_room'])) {
            $newRoom = [
                'number' => trim($request->body['new_room_number'] ?? ''),
                'area' => (float)($request->body['new_room_area'] ?? 0),
                'seats' => (int)($request->body['new_room_seats'] ?? 0),
                'floor' => (int)($request->body['new_room_floor'] ?? 1),
                'type' => (int)($request->body['new_room_type'] ?? 0)
            ];

            // Валидация
            if (empty($newRoom['number'])) {
                $errors[] = 'Номер комнаты не может быть пустым';
            }
            if ($newRoom['area'] <= 0) {
                $errors[] = 'Площадь комнаты должна быть больше 0';
            }
            if ($newRoom['seats'] < 0) {
                $errors[] = 'Количество мест не может быть отрицательным';
            }
            if ($newRoom['floor'] < 1 || $newRoom['floor'] > $building->total_floors) {
                $errors[] = 'Неверный этаж для комнаты';
            }

            if (empty($errors)) {
                $room = new Room();
                $room->building_id = $building->id;
                $room->number = $newRoom['number'];
                $room->area = $newRoom['area'];
                $room->seats = $newRoom['seats'];
                $room->floor = $newRoom['floor'];
                $room->type = $newRoom['type'];

                if ($room->save()) {
                    $_SESSION['message'] = 'Комната успешно добавлена';
                } else {
                    $errors[] = 'Не удалось добавить комнату';
                }
            }

            app()->route->redirect('/edit_building?id=' . $building->id);
            return '';
        }

        // Обработка удаления комнаты
        if (isset($request->body['action_delete_room'])) {
            $roomId = (int)($request->body['room_id'] ?? 0);
            if ($roomId > 0) {
                $room = Room::find($roomId);
                if ($room) {
                    $room->delete();
                    $_SESSION['message'] = 'Комната успешно удалена';
                }
            }

            app()->route->redirect('/edit_building?id=' . $building->id);
            return '';
        }

        // Обработка создания новой кафедры
        if (isset($request->body['action_create_department'])) {
            $newDeptName = trim($request->body['new_department_name'] ?? '');
            $newDeptUserId = (int)($request->body['new_department_user'] ?? 0);

            if (empty($newDeptName)) {
                $errors[] = 'Название кафедры не может быть пустым';
            }

            if ($newDeptUserId <= 0) {
                $errors[] = 'Не выбран ответственный за кафедру';
            }

            if (empty($errors)) {
                $department = new Departments();
                $department->name = $newDeptName;
                $department->users_id = $newDeptUserId;
                $department->building_id = $building->id;

                if (!$department->save()) {
                    $errors[] = 'Не удалось создать кафедру';
                }
            }

            app()->route->redirect('/edit_building?id=' . $building->id);
            return '';
        }

        // Обработка удаления кафедры
        if (isset($request->body['action_delete_department'])) {
            $deptId = (int)($request->body['department_id'] ?? 0);
            if ($deptId > 0) {
                $department = Departments::find($deptId);
                if ($department) {
                    $department->delete();
                }
            }

            // Редирект обратно на страницу редактирования этого здания
            app()->route->redirect('/edit_building?id=' . $building->id);
            return ''; // Возвращаем пустую строку после редиректа
        }

        // Обработка сохранения здания
        if (isset($request->body['action_save'])) {
            $building->name = $name;
            $building->address = $address;
            $building->total_floors = $total_floors;
            $building->total_area = $total_area;
            $building->used_area = $used_area;

            if (!$building->save()) {
                $errors[] = 'Не удалось сохранить данные здания';
            }

            // Сохраняем комнаты
            foreach ($roomsData as $roomData) {
                if (!empty($roomData['id'])) {
                    $room = Room::find((int)$roomData['id']);
                } else {
                    $room = new Room();
                    $room->building_id = $building->id;
                }

                $room->number = $roomData['number'];
                $room->area = (float)$roomData['area'];
                $room->seats = (int)$roomData['seats'];
                $room->floor = (int)$roomData['floor'];
                $room->type = (int)$roomData['type'];
                $room->save();
            }

            if (empty($errors)) {
                app()->route->redirect('/');
            }
        }

        $floors = range(1, max(1, $building->total_floors));
        $roomsData = $roomsData ?: Room::where('building_id', $building->id)->get()->toArray();
        $currentDepartments = Departments::where('building_id', $building->id)->get();

        return (new View())->render(
            $isEdit ? 'site.edit_building' : 'site.add_building',
            compact('building', 'types', 'departmentsList', 'users', 'errors', 'floors', 'roomsData', 'currentDepartments')
        );
    }
}
