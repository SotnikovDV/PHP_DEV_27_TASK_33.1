<?php
// Приложение VK: https://vk.com/editapp?id=51449619&section=options
// Инструкция: https://kotoff.net/article/39-avtorizacija-na-sajte-s-pomoschju-vk-prostoj-i-ponjatnyj-sposob-na-php.html
//   офицал.:  https://vk.com/dev/authcode_flow_user
// Подсказки: https://vk.com/topic-1_24428376?offset=2540

include_once($_SERVER['DOCUMENT_ROOT'] . '/application/core/config.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/application/core/user.class.php');
session_start();

class Controller_Oauthvc
extends Controller
{
    // Регистрация и вход на сайте через OAuth VK
    function action_index()
    {
        // обработка ошибки
        if (isset($_GET['error'])) {
            throw new Exception('Ошибка получении кода авторизации при запросе пользователя: ' . $_GET['error']);
            // получение кода        
        } elseif (isset($_GET['code'])) {
            $code = $_GET['code'];

            // запррашиваем токен пользователя      
            $oauth = OAuth::getInstance('vk');
            $response = $oauth->getUserToken($code);

            /*
            foreach ($response as $key => $element) {
                echo $key . ' = ' . $element . '<br>';
            }*/

            $token = $response->access_token; // Токен
            $expiresIn = $response->expires_in; // Время жизни токена
            $userId = $response->user_id; // ID авторизовавшегося пользователя
            $email = $response->email; // email пользователя
            $fields = 'uid,first_name,last_name,screen_name,sex,bdate,photo_big';

            // Сохраняем токен и id в сессии
            $_SESSION['oauth'] = 'vk';
            $_SESSION['token'] = $token;
            $_SESSION['user_id'] = $userId;

            
            $user = new User();

            // Проверяем - нет ли такого пользователя уже
            $usr = $user->userExists($email);

            // Если такого нет - регистрируем пользователя

            if (!$usr) {
                $usr = $user->addUserOAuth($email, $userId, $token, 'vk');
            }

            if (!$usr) {
                $errors = implode($user->lastErrors);
                header("Location: /logon?errors=" . $errors);
            } else {
                // Признак "Запоминать"
                if (isset($_COOKIE['remember'])) {
                    $remember = $_COOKIE['remember'];
                }
                $usr = $user->logon($email, $userId, $remember);
                //print_r($usr);
                header("Location: /");
            }

            /*
            // Запрашиваем данные пользователя
            $userInfo = $oauth->getUserInfo($userId, $token, $fields);

            foreach ($userInfo as $key => $element) {
                echo $key . ' = ' . $element . '<br>';
            }
            */
        }
    }

}
