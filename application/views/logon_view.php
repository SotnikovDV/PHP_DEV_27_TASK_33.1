<?php

$errors = [];
$remember = false;

// Формируем ссылку для авторизации на VK
$oauth = OAuth::getInstance('vk');

// Для входа зарегестрированного пользователя
$vk_oauth = $oauth->requestAuthorizationCodeURI();

// Считываем пожелание "Запомнить меня"
if (isset($_COOKIE['remember'])) {
    $remember = $_COOKIE['remember'];
}
// Обработка ошибок 
if (isset($_GET['errors'])) {
    $errors[] = $_GET['errors'];
    //print_r($errors);
    //echo '<br>';
    // Обработка формы входа
} elseif (isset($_POST['logon'])) {
    $login = $_POST['login'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);
    // если пользователь не хочет, что бы его запоминали
    if (!$remember) {
        setcookie("remember", false, time() - 3600 * 24 * 30 * 12, "/");
    } else {
        setcookie("remember", true, time() + 60 * 60 * 24 * 30, "/");
    }
    // Вход
    $usr = $user->logon($login, $password, $remember);

    if (!$usr) {
        $errors = $user->lastErrors;
        //var_dump($errors);
    } else {
        header("Location: /");
    }
    // Обработка формы регистрации
} elseif (isset($_POST['register'])) {

    $login = $_POST['login'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    $userName = $_POST['name'];
    $remember = isset($_POST['remember']);

    // если пользователь не хочет, что бы его запоминали
    if (!$remember) {
        setcookie("remember", false, time() - 3600 * 24 * 30 * 12, "/");
    } else {
        setcookie("remember", true, time() + 60 * 60 * 24 * 30, "/");
    }

    // проверяем совпадение паролей
    if ($password !== $password2) {
        $notEqual = true;
        $errors[] = 'Пароли не совпадают. Попробуйте еще раз.';
        // header("Location: register?notequal");
    } else {
        // Добавляем нового пользователя
        $usr = $user->addUser($login, $password, $userName);

        if (!$usr) {
            $errors = $user->lastErrors;
        } else {
            // входим
            $usr = $user->logon($login, $password, $remember);
            
            // отправляем почту
            $subject = 'Регистрация на XXX.com';
            $message = 'Вы успешно зарегистрированы на XXX.com'.PHP_EOL.'Ваш пароль: '.$password;
            smtpmail($userName, $login, $subject, $message) ;

            // переходим на главную
            header("Location: /");
        }
    }
}

if (!empty($errors)) {
?>
    <div class="tab-dialog">
        <div id="errrors" class="logon-frm" style="display: block;">
            <div class="container">
                <h2>Произошла ошибка</h2>
                <hr>
                <p>
                    <?php foreach ($errors as $error) echo $error . '<br>'; ?>
                </p>
            </div>
            <div class="container" style="background-color:#f1f1f1; text-align: center;">
                <button type="button" name="logon" class="logon-frm-btn signupbtn" onclick="location='/logon'">К авторизации</button>
            </div>
        </div>
    </div>
<?php
} else {
?>
    <div class="tab-dialog">
        <div class="row-tablink">
            <button class="tablink" onclick="openPage('logon', this, '#111')" id="defaultOpen" style="margin-left: auto">Вход</button>
            <button class="tablink" onclick="openPage('register', this, '#111')" style="margin-right: auto">Регистрация</button>
        </div>
        <!-- Вход -->
        <div id="logon" class="tabcontent">
            <form class="logon-frm" action="/logon" method="post">

                <div class="container">
                    <h2 style="padding-top: 30px;">Вход</h2>
                    <hr>
                    <label for="login"><b>Логин (адрес эл.почты)</b></label>
                    <input type="text" placeholder="user@email.ru" name="login" required class="logon-frm-input">

                    <label for="password"><b>Пароль</b></label>
                    <input type="password" placeholder="Пароль" name="password" required class="logon-frm-input">

                    <label>
                        <input type="checkbox" <?php if ($remember) {
                                                    echo 'checked';
                                                } ?> name="remember"> Запомнить меня
                    </label>
                </div>

                <div class="container" style="background-color:#f1f1f1; text-align: center;">
                    <button type="submit" name="logon" class="logon-frm-btn signupbtn" style="margin-top: 20px;">Войти</button>
                    <!-- <button type="button" name="cancel" onclick="location='/'" class="logon-frm-btn cancelbtn">Отмена</button> -->
                    <br><br><span class="psw">Забыли <a href="#">пароль?</a></span>
                </div>

                <div class="container" style="border-top: 1px solid #888;">
                    <label><b>Вход с помощью социальных сетей</b></label><br>

                    <a href="<?= $vk_oauth ?>"><img src="/images/vk.svg" alt="Вконтакте" class="fa"></a>
                    <!-- <a href="#"> --><img src="/images/ya.svg" alt="Яндекс" class="fa" id="ya"><!-- </a> -->
                    <!-- <a href="#"> --><img src="/images/g.svg" alt="Google" class="fa" id="gg"><!-- </a> -->
                </div>
            </form>
        </div>
        <!-- Регистрация -->
        <div id="register" class="tabcontent">
            <form class="logon-frm" action="/register" method="post">
                <div class="container">
                    <h2 style="padding-top: 30px;">Регистрация</h2>
                    <hr>
                    <label for="login"><b>Логин (адрес эл.почты)</b></label>
                    <input type="text" placeholder="user@email.ru" name="login" required class="logon-frm-input">

                    <label for="password"><b>Пароль</b></label>
                    <input type="password" placeholder="Задайте пароль" name="password" required class="logon-frm-input">

                    <label for="password2"><b>Повторите пароль</b></label>
                    <input type="password" placeholder="тот же пароль" name="password2" required class="logon-frm-input">

                    <label for="name"><b>Имя пользователя</b></label>
                    <input type="text" placeholder="Вася Иванов" name="name" required class="logon-frm-input">

                    <label>
                        <input type="checkbox" <?php if ($remember) {
                                                    echo 'checked';
                                                } ?> name="remember" style="margin-bottom:15px"> Запомнить меня
                    </label>
                </div>

                <!-- <div class="clearfix"> -->
                <div class="container" style="background-color:#f1f1f1; text-align: center;">
                    <button type="submit" name="register" class="logon-frm-btn signupbtn">Зарегистрировать</button>
                    <!-- <button type="button" name="cancel" onclick="location='/'" class="logon-frm-btn cancelbtn">Отмена</button> -->
                    <br><br><span class="psw">Регистрируясь, вы принимаете <a href="#">пользовательское соглашение</a></span>
                </div>
                <div class="container" style="border-top: 1px solid #888;">
                    <label><b>Зарегистрируйтесь с помощью социальных сетей</b></label><br>

                    <a href="<?= $vk_oauth ?>"><img src="/images/vk.svg" alt="Вконтакте" class="fa"></a>
                    <!-- <a href="#"> --><img src="/images/ya.svg" alt="Яндекс" class="fa" id="ya"><!-- </a> -->
                    <!-- <a href="#"> --><img src="/images/g.svg" alt="Google" class="fa" id="gg"><!-- </a> -->
                </div>
            </form>
        </div>
    </div>
<?php
}
?>

<script>
    function openPage(pageName, elmnt, color) {
        // Hide all elements with class="tabcontent" by default */
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            if (tabcontent[i].id !== 'errors') {
                tabcontent[i].style.display = "none";
            }
        }

        // Remove the background color of all tablinks/buttons
        tablinks = document.getElementsByClassName("tablink");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].style.backgroundColor = "";
        }

        // Show the specific tab content
        document.getElementById(pageName).style.display = "block";

        // Add the specific color to the button used to open the tab content
        elmnt.style.backgroundColor = color;
    }

    // Get the element with id="defaultOpen" and click on it
    document.getElementById("defaultOpen").click();
</script>