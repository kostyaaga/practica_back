<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Учебно-методическое управление</title>
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
<header class="header">
    <nav>
        <div class="">
            <a href="<?= app()->route->getUrl('/') ?>">Главная</a>
            <a href="<?= app()->route->getUrl('/statistic') ?>">Статистика</a>
        </div>
        <div class="">
            <?php
            if (!app()->auth::check()):
                ?>
                <a href="<?= app()->route->getUrl('/login') ?>">Вход</a>
            <?php
            else:
                ?>
                <a href="<?= app()->route->getUrl('/logout') ?>">Выход</a>
                <p><?= app()->auth::user()->name ?></p>
            <?php
            endif;
            ?>
        </div>
    </nav>
</header>
<main>
    <?= $content ?? '' ?>
</main>
</body>
</html>
