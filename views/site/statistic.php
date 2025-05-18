<div class="filter-container">
    <h2>Общая статистика</h2>
    <form method="GET" action="/statistic" class="filter-form">
        <input type="text" name="name" placeholder="Название" value="<?= htmlspecialchars($_GET['name'] ?? '') ?>" class="filter-input">

        <div class="filter-group">
            <label>Общая площадь:</label>
            <input type="number" name="total_area_min" placeholder="От" value="<?= htmlspecialchars($_GET['total_area_min'] ?? '') ?>" class="filter-input">
            <input type="number" name="total_area_max" placeholder="До" value="<?= htmlspecialchars($_GET['total_area_max'] ?? '') ?>" class="filter-input">
        </div>

        <div class="filter-group">
            <label>Используемая площадь:</label>
            <input type="number" name="used_area_min" placeholder="От" value="<?= htmlspecialchars($_GET['used_area_min'] ?? '') ?>" class="filter-input">
            <input type="number" name="used_area_max" placeholder="До" value="<?= htmlspecialchars($_GET['used_area_max'] ?? '') ?>" class="filter-input">
        </div>

        <input type="text" name="address" placeholder="Адрес" value="<?= htmlspecialchars($_GET['address'] ?? '') ?>" class="filter-input">
        <input type="number" name="total_floors" placeholder="Этажность" value="<?= htmlspecialchars($_GET['total_floors'] ?? '') ?>" class="filter-input">

        <div class="filter-actions">
            <button type="submit" class="filter-button">Фильтровать</button>
            <button type="submit" class="clear-button"> <a href="/statistic" class="clear-button">Сбросить</a></button>
        </div>
    </form>
</div>

<?php if (empty($filteredBuildings)): ?>
    <p>Здания не найдены по заданным критериям.</p>
<?php else: ?>

    <?php foreach ($filteredBuildings as $building): ?>
        <div class="stat-block">
            <p><strong>Название:</strong> <?= htmlspecialchars($building->name) ?></p>
            <p><strong>Адрес:</strong> <?= htmlspecialchars($building->address) ?></p>
            <p><strong>Площадь здания:</strong> <?= htmlspecialchars($building->total_area) ?> м²</p>
            <p><strong>Кол-во мест:</strong> <?= htmlspecialchars($building->total_seats) ?></p>
            <p><strong>Этажей:</strong> <?= htmlspecialchars($building->total_floors) ?></p>
            <form method="POST" action="" onsubmit="return confirm('Точно удалить корпус <?= htmlspecialchars($building->name) ?>?');" style="display:inline-block; margin-bottom:10px;">
                <input name="csrf_token" type="hidden" value="<?= app()->auth::generateCSRF() ?>"/>
                <input type="hidden" name="delete_id" value="<?= $building->id ?>">
                <button type="submit" class="delete-btn">Удалить корпус</button>
            </form>
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

<?php endif; ?>
