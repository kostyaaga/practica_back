<form method="POST" action="/add_building" class="form-container">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(app()->auth::generateCSRF()) ?>" />

    <section class="block">
        <h2>Информация о корпусе</h2>

        <label>Название корпуса</label>
        <input name="name" value="<?= htmlspecialchars($name ?? '') ?>" />

        <label>Адрес</label>
        <input name="address" value="<?= htmlspecialchars($address ?? '') ?>" />

        <label>Количество этажей</label>
        <input type="number" name="total_floors" value="<?= htmlspecialchars($total_floors ?? 1) ?>" min="1" />

        <label>Общая площадь</label>
        <input type="number" step="0.01" name="total_area" value="<?= htmlspecialchars($total_area ?? 0) ?>" />

        <label>Используемая площадь</label>
        <input type="number" step="0.01" name="used_area" value="<?= htmlspecialchars($used_area ?? 0) ?>" />

        <button type="submit" name="action_update_floors">Обновить этажи</button>
    </section>

    <section class="block">
        <h3>Этажи</h3>
        <?php foreach ($floors as $floor): ?>
            <div class="floor">Этаж <?= $floor ?></div>
        <?php endforeach; ?>
    </section>

    <section class="block">
        <h3>Кабинеты</h3>
        <?php if (empty($roomsData)) : $roomsData = [['number'=>'', 'area'=>'', 'seats'=>'', 'floor'=>1, 'type'=>1]]; endif; ?>
        <?php foreach ($roomsData as $i => $room) : ?>
            <fieldset>
                <legend>Кабинет #<?= $i+1 ?></legend>

                <label>Номер</label>
                <input type="number" name="rooms[<?= $i ?>][number]" value="<?= htmlspecialchars($room['number'] ?? '') ?>" />

                <label>Площадь</label>
                <input type="number" step="0.01" name="rooms[<?= $i ?>][area]" value="<?= htmlspecialchars($room['area'] ?? '') ?>" />

                <label>Места</label>
                <input type="number" name="rooms[<?= $i ?>][seats]" value="<?= htmlspecialchars($room['seats'] ?? '') ?>" />

                <label>Этаж</label>
                <select name="rooms[<?= $i ?>][floor]">
                    <?php foreach ($floors as $floor): ?>
                        <option value="<?= $floor ?>" <?= (int)($room['floor'] ?? 1) === $floor ? 'selected' : '' ?>><?= $floor ?></option>
                    <?php endforeach; ?>
                </select>

                <label>Тип</label>
                <select name="rooms[<?= $i ?>][type]">
                    <?php foreach ($types as $type): ?>
                        <option value="<?= $type->id ?>" <?= ((int)($room['type'] ?? 1) === $type->id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($type->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </fieldset>
        <?php endforeach; ?>

        <button type="submit" name="action_add_room">Добавить кабинет</button>
    </section>

    <section class="form-container">
        <div class="block">
            <h3>Кафедры</h3>
            <?php if (empty($departmentsData)) : $departmentsData = [['name'=>'', 'existing_name'=>'', 'users_id'=>0]]; endif; ?>
            <?php foreach ($departmentsData as $i => $dept): ?>
                <fieldset>
                    <legend>Кафедра #<?= $i+1 ?></legend>

                    <label>
                        <input type="radio" name="departments[<?= $i ?>][type]" value="new" <?= (!isset($dept['type']) || $dept['type'] === 'new') ? 'checked' : '' ?> />
                        Создать новую кафедру
                    </label>
                    <input type="text"
                           name="departments[<?= $i ?>][name]"
                           value="<?= htmlspecialchars($dept['name'] ?? '') ?>"
                        <?= (isset($dept['type']) && $dept['type'] !== 'new') ? 'disabled' : '' ?>
                           placeholder="Название новой кафедры" />

                    <label>
                        <input type="radio" name="departments[<?= $i ?>][type]" value="existing" <?= (isset($dept['type']) && $dept['type'] === 'existing') ? 'checked' : '' ?> />
                        Выбрать существующую кафедру
                    </label>
                    <select name="departments[<?= $i ?>][existing_name]" <?= (!isset($dept['type']) || $dept['type'] !== 'existing') ? 'disabled' : '' ?>>
                        <option value="">-- Выберите кафедру --</option>
                        <?php foreach ($departments as $existingDept): ?>
                            <option value="<?= htmlspecialchars($existingDept->name) ?>" <?= (isset($dept['existing_name']) && $dept['existing_name'] === $existingDept->name) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($existingDept->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label>Ответственный</label>
                    <select name="departments[<?= $i ?>][users_id]">
                        <option value="0">— Выберите пользователя —</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user->id ?>" <?= ($dept['users_id'] ?? 0) == $user->id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </fieldset>
            <?php endforeach; ?>
            <button type="submit" name="action_add_department">Добавить кафедру</button>
        </div>
    </section>

    <div class="submit-area">
        <button type="submit" name="action_save">Сохранить корпус</button>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <?php foreach ($errors as $e): ?>
                <div><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</form>
