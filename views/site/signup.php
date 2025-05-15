<h2>Регистрация нового пользователя</h2>
<h3><?= $message ?? ''; ?></h3>
<form method="post">
    <label>Имя <input type="text" name="name"></label>
    <label>Логин <input type="text" name="login"></label>
    <label>Пароль <input type="password" name="password"></label>
    <label for="role">Роль:</label>
    <select name="role" id="role" required>
        <option value="Сотрудник">Сотрудник</option>
        <option value="Админ">Админ</option>
    </select>
    <button>Зарегистрироваться</button>
</form>
