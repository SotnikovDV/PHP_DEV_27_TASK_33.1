<?php

$errors = [];

$name = '';
$nikname = '';
$photo_file = '';
$photo_src = '';
$login = $usr['login'];

// считываем пользователя из базы данных
    $usr = $user->userExists($login);

    // если не нашли - ошибка ???
    if (!$usr) {
        echo 'Ошибка чтения пользователя из базы данных';
    } else {
        
        // если это сохранение полей
        if (isset($_POST['name'])) {
            $name = $_POST['name'];
            $nikname = $_POST['nikname'];
            $photo_file = $usr['photo_file'];
            if ($photo_file) {
                $photo_src = '/images/face/'.$photo_file;
            }

            // сохраняем данные пользователя
            // Добавляем нового пользователя
            $usr = $user->udateUser($login, $name, $photo_file, $nikname);
            if (!$usr) {
                $errors = $user->lastErrors;
            } else {
                // переходим на главную
                header("Location: /");
            }

        } else {    
            $name = $usr['name'];
            $nikname = $usr['nikname'];
            $photo_file = $usr['photo_file'];
            if ($photo_file) {
                $photo_src = '/images/face/'.$photo_file;
            }

            // если пользователь не указа ИФ, никнейм и фото - попробуем взять из VK
            if (!$name or !$nikname or !$photo_file) {
            
                // Берем токен и id из сессии
                if (isset($_SESSION['oauth'])){

                    $serv = $_SESSION['oauth'];
                    $token = $_SESSION['token'];
                    $userId = $_SESSION['user_id'];
                    $fields = 'uid,first_name,last_name,screen_name,sex,bdate,photo_big';
                    
                    $oauth = OAuth::getInstance($serv);
                    // Запрашиваем данные пользователя
                    $userInfo = $oauth->getUserInfo($userId, $token, $fields);

                    if (!$$userInfo) {
                        $first_name = $userInfo->first_name;
                        $last_name = $userInfo->last_name;
                        $name = $first_name.' '.$last_name;
                        $nikname = $userInfo->screen_name;
                        $photo_big = $userInfo->photo_big;
                        $bdate  = $userInfo->bdate;
                        $photo_src = $userInfo->photo_big;
                    }
                    // добавить скачивание и сохранение фото пользователя
                }
            }
        }
    }       
    /* foreach ($userInfo as $key => $element) {
        echo $key . ' = ' . $element . '<br>';
    }*/

/*    
    header("Location: /logon?errors=Для просмотра профиля пользователя, авторизуйтесь через VK");
}
*/
?>
    <div class="tab-dialog">
        <!-- Профиль -->
        <div id="profile" class="tabcontent" style="display: block">
            <form class="logon-frm" action="/profile" method="post">

                <div class="container">
                    <table><tr>
            <td width="70%">
                    <h2 style="text-align: center;">Профиль<br>пользователя</h2>
                    </td>
                    <td width="30%">
                        
                        <?php 
                        if ($photo_src){
                            echo '<img src="'.$photo_src.'" alt="Фото пользователя">';
                        } else {
                            echo '<span>Это фото пользователя. Вы можете загрузить его здесь.</span>';
                        } ?>   
                    </td>
                    </tr></table>
                    <hr>
                    <label for="first_name"><b>Имя Фамилия</b></label>
                    <input type="text" placeholder="Иван Иванов" name="name" required class="logon-frm-input" value="<?=$name?>">

                    <label for="screen_name"><b>Ник-нейм</b></label>
                    <input type="text" placeholder="Ktulhu" name="nikname" required class="logon-frm-input" value="<?=$nikname?>">

                </div>

                <div class="container" style="background-color:#f1f1f1; text-align: center;">
                    <button type="submit" name="save" class="logon-frm-btn signupbtn" style="margin-top: 20px;">Сохранить</button>
                </div>

            </form>
        </div>
