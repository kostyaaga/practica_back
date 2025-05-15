<div>
    <h2>Общая статистика</h2>
    <form method="GET" action="/buildings">
        <input type="text" name="name" placeholder="Название" value="<?= $_GET['name'] ?? '' ?>">

        <div>
            <label>Общая площадь:</label>
            <input type="number" name="total_area_min" placeholder="От" value="<?= $_GET['total_area_min'] ?? '' ?>">
            <input type="number" name="total_area_max" placeholder="До" value="<?= $_GET['total_area_max'] ?? '' ?>">
        </div>

        <div>
            <label>Используемая площадь:</label>
            <input type="number" name="used_area_min" placeholder="От" value="<?= $_GET['used_area_min'] ?? '' ?>">
            <input type="number" name="used_area_max" placeholder="До" value="<?= $_GET['used_area_max'] ?? '' ?>">
        </div>

        <input type="text" name="address" placeholder="Адрес" value="<?= $_GET['address'] ?? '' ?>">

        <input type="number" name="total_floors" placeholder="Этажность" value="<?= $_GET['total_floors'] ?? '' ?>">

        <button type="submit">Фильтровать</button>
        <a href="">Сбросить</a>
    </form>
</div>
<div>
    <?php
        $building = $buildings[1];
    ?>
    <div>
        <p>Название</p>
        <p><?= htmlspecialchars($building->name) ?></p>
    </div>
    <div>
    <div>
        <p>Адресс</p>
        <p><?= htmlspecialchars($building->address) ?></p>
    </div>
    </div>
    <div>
        <p>Площадь здания</p>
        <p><?= htmlspecialchars($building->total_area) ?> m3</p>
    </div>
    <div>
        <p>Кол-во мест в здании</p>
        <p><?= htmlspecialchars($building->total_seats) ?></p>
    </div>
    <div>
        <p>Этажей в здании</p>
        <p><?= htmlspecialchars($building->total_floors) ?></p>
    </div>
</div>

