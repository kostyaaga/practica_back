<h1>Список зданий</h1>
<?php if (app()->auth::check() && app()->auth::user()->role === 'Администратор'): ?>
    <a class="button-link" href="<?= app()->route->getUrl('/signup') ?>">Добавить пользователя</a>
<?php endif; ?>
<a class="button-link" href="<?= app()->route->getUrl('/add_building') ?>">Добавить Здание</a>
<form method="GET" action="">
    <input name="csrf_token" type="hidden" value="<?= app()->auth::generateCSRF() ?>"/>
    <input
            type="text"
            name="search"
            value="<?= htmlspecialchars($search ?? '') ?>"
            placeholder="Поиск корпуса..."
            class="search-input"
    >
    <button type="submit" class="search-btn">Найти</button>
</form>

<?php foreach ($buildings as $building): ?>
    <div class="building">
        <h2>Название корпуса: <?= htmlspecialchars($building->name) ?></h2>
        <h3>Адрес: <?= htmlspecialchars($building->address) ?></h3>
        <h3>Этажей: <?= htmlspecialchars($building->total_floors) ?></h3>


        <a href="<? app()->route->getUrl('/edit_building') ?>" class="edit">Редактировать</a>
        <form method="POST" action="" onsubmit="return confirm('Точно удалить корпус <?= htmlspecialchars($building->name) ?>?');" style="display:inline-block; margin-bottom:10px;">
            <input name="csrf_token" type="hidden" value="<?= app()->auth::generateCSRF() ?>"/>
            <input type="hidden" name="delete_id" value="<?= $building->id ?>">
            <button type="submit" class="delete-btn">Удалить корпус</button>
        </form>

        <?php foreach ($rooms as $room): ?>
            <?php if ($room->building_id == $building->id): ?>
                <p>Номер кабинета: <?= htmlspecialchars($room->number) ?></p>
                <?php foreach ($types as $type): ?>
                    <?php if ($room->type == $type->id): ?>
                        <p>Тип кабинета: <?= htmlspecialchars($type->name) ?></p>
                    <?php endif; ?>
                <?php endforeach; ?>
                <p>Площадь: <?= htmlspecialchars($room->area) ?> м²</p>
                <p>Кол-во мест: <?= htmlspecialchars($room->seats) ?></p>
            <?php endif; ?>
        <?php endforeach; ?>

        <h3>Отделы в корпусе:</h3>
        <?php
        $hasDepartments = false;
        $userName = "не известно";
        foreach ($departments as $department) {
            if ($department->building_id == $building->id) {
                $hasDepartments = true;
                if (!empty($users)) {
                    foreach ($users as $user) {
                        if ($user->id == $department->users_id) {
                            $userName = htmlspecialchars($user->name);
                            break;
                        }
                    }
                }
                ?>
                <p>• <?= htmlspecialchars($department->name) ?> Ответственный: <?= $userName ?></p>
                <?php
            }
        }
        if (!$hasDepartments) {
            echo '<p>Отделы не найдены</p>';
        }
        ?>
    </div>
<?php endforeach; ?>

