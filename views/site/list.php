<h1>Список зданий</h1>
<?php if (app()->auth::check() && app()->auth::user()->role === 'Администратор'): ?>
    <a class="button-link" href="<?= app()->route->getUrl('/signup') ?>">Добавить пользователя</a>
<?php endif; ?>

<?php foreach ($buildings as $building): ?>
    <div class="building">
        <h2>Название корпуса: <?= htmlspecialchars($building->name) ?></h2>
        <h3>Адрес: <?= htmlspecialchars($building->address) ?></h3>
        <h3>Этажей: <?= htmlspecialchars($building->total_floors) ?></h3>
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
    </div>
<?php endforeach; ?>
