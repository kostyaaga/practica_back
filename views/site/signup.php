<h3 class="form-message"><?= $message ?? ''; ?></h3>
<form method="post" class="register-form">
    <input name="csrf_token" type="hidden" value="<?= app()->auth::generateCSRF() ?>"/>
    <h2>Регистрация нового пользователя</h2>
    <label>Имя
        <input type="text" name="name" class="form-input">
    </label>
    <label>Логин
        <input type="text" name="login" class="form-input">
    </label>
    <label>Пароль
        <input type="password" name="password" class="form-input">
    </label>
    <label for="role">Роль:</label>
    <select name="role" id="role" required class="form-select">
        <option value="Сотрудник">Сотрудник</option>
        <option value="Админ">Админ</option>
    </select>
    <button type="submit" class="form-button">Зарегистрироваться</button>
</form>

