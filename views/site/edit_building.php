<h1 class="page-title">Редактирование здания: <?= htmlspecialchars($building->name) ?></h1>

<!-- Основная форма для редактирования здания -->
<form method="POST" action="<?= app()->route->getUrl('/edit_building') ?>" class="building-form">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(app()->auth::generateCSRF()) ?>" />
    <input type="hidden" name="csrf_token" value="<?= app()->auth::generateCSRF() ?>">
    <input type="hidden" name="id" value="<?= $building->id ?>">

    <!-- Основные поля здания -->
    <div class="form-group">
        <label class="form-label">Название корпуса:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($building->name) ?>" required class="form-input">
    </div>

    <div class="form-group">
        <label class="form-label">Адрес:</label>
        <input type="text" name="address" value="<?= htmlspecialchars($building->address) ?>" required class="form-input">
    </div>

    <div class="form-group">
        <label class="form-label">Этажность:</label>
        <input type="number" name="total_floors"
               value="<?= htmlspecialchars($building->total_floors) ?>"
               min="1" required class="form-input">
        <button type="submit" name="action_update_floors" value="1" class="btn btn-secondary">
            Обновить этажи
        </button>
    </div>

    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(app()->auth::generateCSRF()) ?>" />
    <div class="form-actions">
        <button type="submit" name="action_save" value="1" class="btn btn-primary">Сохранить здание</button>
        <a href="<?= app()->route->getUrl('/') ?>" class="btn">Отмена</a>
    </div>
</form>

<!-- Форма для добавления комнаты -->
<div class="room-form">
    <h3>Добавить новую комнату</h3>
    <form method="POST" action="<?= app()->route->getUrl('/edit_building') ?>">
        <input type="hidden" name="csrf_token" value="<?= app()->auth::generateCSRF() ?>">
        <input type="hidden" name="id" value="<?= $building->id ?>">

        <div class="form-group">
            <label>Номер комнаты:</label>
            <input type="text" name="new_room_number" required class="form-input">
        </div>

        <div class="form-group">
            <label>Площадь:</label>
            <input type="number" name="new_room_area" step="0.01" min="0.01" required class="form-input">
        </div>

        <div class="form-group">
            <label>Количество мест:</label>
            <input type="number" name="new_room_seats" min="0" required class="form-input">
        </div>

        <div class="form-group">
            <label>Этаж:</label>
            <select name="new_room_floor" required class="form-input">
                <?php foreach ($floors as $floor): ?>
                    <option value="<?= $floor ?>"><?= $floor ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Тип комнаты:</label>
            <select name="new_room_type" required class="form-input">
                <?php foreach ($types as $type): ?>
                    <option value="<?= $type->id ?>"><?= htmlspecialchars($type->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" name="action_add_room" value="1" class="btn btn-secondary">Добавить комнату</button>
    </form>
</div>

<!-- Список текущих комнат -->
<div class="current-rooms">
    <h3>Текущие комнаты</h3>
    <?php if (empty($roomsData)): ?>
        <p>Нет добавленных комнат</p>
    <?php else: ?>
        <table class="room-table">
            <thead>
            <tr>
                <th>Номер</th>
                <th>Площадь</th>
                <th>Мест</th>
                <th>Этаж</th>
                <th>Тип</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($roomsData as $room): ?>
                <tr>
                    <td><?= htmlspecialchars($room['number']) ?></td>
                    <td><?= $room['area'] ?> м²</td>
                    <td><?= $room['seats'] ?></td>
                    <td><?= $room['floor'] ?></td>
                    <td>
                        <?php
                        $typeName = '';
                        foreach ($types as $type) {
                            if ($type->id == $room['type']) {
                                $typeName = $type->name;
                                break;
                            }
                        }
                        echo htmlspecialchars($typeName);
                        ?>
                    </td>
                    <td>
                        <form method="POST" action="<?= app()->route->getUrl('/edit_building') ?>" style="display: inline;">
                            <input type="hidden" name="csrf_token" value="<?= app()->auth::generateCSRF() ?>">
                            <input type="hidden" name="id" value="<?= $building->id ?>">
                            <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                            <button type="submit" name="action_delete_room" value="1" class="btn btn-danger btn-sm">Удалить</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Форма для добавления кафедры -->
<div class="department-form">
    <h3>Добавить новую кафедру</h3>
    <form method="POST" action="<?= app()->route->getUrl('/edit_building') ?>">
        <input type="hidden" name="csrf_token" value="<?= app()->auth::generateCSRF() ?>">
        <input type="hidden" name="id" value="<?= $building->id ?>">

        <div class="form-group">
            <label>Название кафедры:</label>
            <input type="text" name="new_department_name" required class="form-input">
        </div>

        <div class="form-group">
            <label>Ответственный:</label>
            <select name="new_department_user" required class="form-input">
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user->id ?>"><?= htmlspecialchars($user->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" name="action_create_department" value="1" class="btn btn-secondary">Создать кафедру</button>
    </form>
</div>

<!-- Список текущих кафедр -->
<div class="current-departments">
    <h3>Текущие кафедры</h3>
    <?php if ($currentDepartments->isEmpty()): ?>
        <p>Нет привязанных кафедр</p>
    <?php else: ?>
        <ul>
            <?php
            $userName = "не известно";
            foreach ($currentDepartments as $department):
                foreach ($users as $user) {
                if ($user->id == $department->users_id) {
                $userName = htmlspecialchars($user->name);
                break;
                    }
            }?>
                <li>
                    <p>• <?= htmlspecialchars($department->name) ?> Ответственный: <?= $userName ?></p>

                    <form method="POST" action="<?= app()->route->getUrl('/edit_building') ?>" style="display: inline;">
                        <input type="hidden" name="csrf_token" value="<?= app()->auth::generateCSRF() ?>">
                        <input type="hidden" name="id" value="<?= $building->id ?>">
                        <input type="hidden" name="department_id" value="<?= $department->id ?>">
                        <button type="submit" name="action_delete_department" value="1" class="btn btn-danger btn-sm">Удалить</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<form method="POST" action="<?= app()->route->getUrl('/edit_building') ?>" class="building-form">

</form>
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?>
            <p><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>