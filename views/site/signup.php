<form method="post" class="register-form">
    <input name="csrf_token" type="hidden" value="<?= app()->auth::generateCSRF() ?>"/>
    <h2>Регистрация нового пользователя</h2>

    <label>Имя
        <input type="text" name="name" class="form-input <?= isset($errors['name']) ? 'input-error' : '' ?>" value="<?= htmlspecialchars($old['name'] ?? '') ?>">
    </label>
    <?php if (isset($errors['name'])): ?>
        <div class="error"><?= implode('<br>', $errors['name']) ?></div>
    <?php endif; ?>

    <label>Логин
        <input type="text" name="login" class="form-input <?= isset($errors['login']) ? 'input-error' : '' ?>" value="<?= htmlspecialchars($old['login'] ?? '') ?>">
    </label>
    <?php if (isset($errors['login'])): ?>
        <div class="error"><?= implode('<br>', $errors['login']) ?></div>
    <?php endif; ?>

    <label>Пароль
        <input type="password" name="password" class="form-input <?= isset($errors['password']) ? 'input-error' : '' ?>">
    </label>
    <?php if (isset($errors['password'])): ?>
        <div class="error"><?= implode('<br>', $errors['password']) ?></div>
    <?php endif; ?>

    <label for="role">Роль:</label>
    <select name="role" id="role" required class="form-select <?= isset($errors['role']) ? 'input-error' : '' ?>">
        <option value="Сотрудник" <?= (isset($old['role']) && $old['role'] === 'Сотрудник') ? 'selected' : '' ?>>Сотрудник</option>
        <option value="Администратор" <?= (isset($old['role']) && $old['role'] === 'Администратор') ? 'selected' : '' ?>>Админ</option>
    </select>
    <?php if (isset($errors['role'])): ?>
        <div class="error"><?= implode('<br>', $errors['role']) ?></div>
    <?php endif; ?>

    <button type="submit" class="form-button">Зарегистрироваться</button>
</form>
