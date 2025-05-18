
<h3><?= $message ?? ''; ?></h3>
<h3><?= app()->auth->user()->name ?? ''; ?></h3>

<?php if (!app()->auth::check()): ?>
    <form method="post" class="form">
        <input name="csrf_token" type="hidden" value="<?= app()->auth::generateCSRF() ?>"/>
        <h2>Авторизация</h2>
        <label class="form-label">Логин
            <input type="text" name="login" class="form-input">
        </label>
        <label class="form-label">Пароль
            <input type="password" name="password" class="form-input">
        </label>
        <button class="form-button">Войти</button>
    </form>
<?php endif; ?>
