<div class="filter-container">
    <h2>Общая статистика</h2>
    <form method="GET" action="/buildings" class="filter-form">
        <input type="text" name="name" placeholder="Название" value="<?= $_GET['name'] ?? '' ?>" class="filter-input">

        <div class="filter-group">
            <label>Общая площадь:</label>
            <input type="number" name="total_area_min" placeholder="От" value="<?= $_GET['total_area_min'] ?? '' ?>" class="filter-input">
            <input type="number" name="total_area_max" placeholder="До" value="<?= $_GET['total_area_max'] ?? '' ?>" class="filter-input">
        </div>

        <div class="filter-group">
            <label>Используемая площадь:</label>
            <input type="number" name="used_area_min" placeholder="От" value="<?= $_GET['used_area_min'] ?? '' ?>" class="filter-input">
            <input type="number" name="used_area_max" placeholder="До" value="<?= $_GET['used_area_max'] ?? '' ?>" class="filter-input">
        </div>

        <input type="text" name="address" placeholder="Адрес" value="<?= $_GET['address'] ?? '' ?>" class="filter-input">
        <input type="number" name="total_floors" placeholder="Этажность" value="<?= $_GET['total_floors'] ?? '' ?>" class="filter-input">

        <div class="filter-actions">
            <button type="submit" class="filter-button">Фильтровать</button>
            <button type="submit" class="clear-button">Сбросить</button>
        </div>
    </form>
</div>

<?php
$building = $buildings[1];
?>
<div class="stat-block">
    <p><strong>Название:</strong> <?= htmlspecialchars($building->name) ?></p>
    <p><strong>Адрес:</strong> <?= htmlspecialchars($building->address) ?></p>
    <p><strong>Площадь здания:</strong> <?= htmlspecialchars($building->total_area) ?> м²</p>
    <p><strong>Кол-во мест:</strong> <?= htmlspecialchars($building->total_seats) ?></p>
    <p><strong>Этажей:</strong> <?= htmlspecialchars($building->total_floors) ?></p>

    <h3>Отделы в этом здании:</h3>
    <?php
    $hasDepartments = false;
    foreach ($departments as $department) {
        if ($department->building_id == $building->id) {
            $hasDepartments = true;
            $userName = 'Неизвестный сотрудник';
            if (!empty($users)) {
                foreach ($users as $user) {
                    if ($user->id == $department->user_id) {
                        $userName = htmlspecialchars($user->name);
                        break;
                    }
                }
            }
            ?>
            <p>• <?= htmlspecialchars($department->name) ?> (Ответственный: <?= $userName ?>)</p>
            <?php
        }
    }
    if (!$hasDepartments) {
        echo '<p>Отделы не найдены</p>';
    }
    ?>
</div>
