<h1>Список зданий</h1>
<?php foreach ($buildings as $building): ?>
    <div>
        <h2>Название корпуса <?= htmlspecialchars($building->name) ?></h2>
        <h3>Адресс <?= htmlspecialchars($building->address) ?></h3>
        <h3>Этажей <?= htmlspecialchars($building->total_floors) ?></h3>
        <?php foreach ($rooms as $room): ?>
            <?php if ($room->building_id == $building->id): ?>
                <p>Номер кабинета <?= htmlspecialchars($room->number) ?></p>
                <?php foreach ($types as $type): ?>
                    <?php if ($room->type == $type->id): ?>
                        <p>Тип кабинета <?= htmlspecialchars($type->name) ?></p>
                    <?php endif; ?>
                <?php endforeach; ?>
                <p>Площадь <?= htmlspecialchars($room->area) ?></p>
                <p>Кол-во мест <?= htmlspecialchars($room->seats) ?></p>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>
