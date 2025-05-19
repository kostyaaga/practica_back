<h1>Редактирование здания: <?= htmlspecialchars($building->name) ?></h1>

<form method="POST" action="<?= app()->route->getUrl('/edit_building') ?>">
    <input name="csrf_token" type="hidden" value="<?= app()->auth::generateCSRF() ?>"/>
    <input type="hidden" name="csrf_token" value="<?= app()->auth::generateCSRF() ?>">
    <input type="hidden" name="id" value="<?= $building->id ?>">

    <label>
        Название корпуса:
        <input type="text" name="name" value="<?= htmlspecialchars($building->name) ?>" required>
    </label>

    <label>
        Адрес:
        <input type="text" name="address" value="<?= htmlspecialchars($building->address) ?>" required>
    </label>

    <label>
        Этажность:
        <input type="number" name="total_floors" value="<?= htmlspecialchars($building->total_floors) ?>" min="1" required>
    </label>

    <button type="submit">Сохранить</button>
    <a href="<?= app()->route->getUrl('/') ?>" style="color: black">Отмена</a>
</form>

<?php if (!empty($errors)): ?>
    <ul class="errors">
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>