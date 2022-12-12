<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">
    <title>Пример авторизации через QAuth</title>
</head>

<body>
    <?php
    session_start();
    $user = new User();

    // Считываем пользователя сессии
    $usr = $user->logonBySession();

    $photo_src = '/images/empty_user_photo.png';

    // если в сессии нет - ищем в coockaх
    if (!$usr) {
        $usr = $user->logonByCookie();
    }
    // если зарегистрирован - возьмем фото
    if ($usr) {
        $photo_file = $usr['photo_file'];
        if ($photo_file) {
            $photo_src = '/images/face/'.$photo_file;
        }
    }



    if (!$user->loged) {
        $login = null;
    } else {
        $login = $usr['login'];
    }

    ?>
    <aside>

        <p style="margin: 0 auto; text-align: center;"><a href="/logon"><img src=".<?=$photo_src?>." width="70%" alt="Вход"></a>
            <?php
            if (!$login) {
                echo '<a href="/logon">Войти</a>';
            } else {
                $scr_name = screen_name($login);
                //echo '<a href="/logoff">' . $scr_name . '</a>';
                echo '<a href="/logoff">' . $usr['name'] . '</a>';
            }
            ?>
        </p>
        <hr>
        <a href="/chats">Чаты</a>'
        <a href="/groups">Группы</a>'
        <?php if ($login) {
            echo '<a href="/profile">Профиль</a>';
            echo '<a href="/settings">Настройки</a>';
         } ?>
        <!-- <a href="/">Журнал ошибок</a> -->
    </aside>
    <main>

        <?php include $content_view; ?>
    </main>
</body>

</html>