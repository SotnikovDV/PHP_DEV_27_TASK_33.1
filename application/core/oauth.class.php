<?php
// Приложение VK: https://vk.com/editapp?id=51449619&section=options
// Инструкция: https://kotoff.net/article/39-avtorizacija-na-sajte-s-pomoschju-vk-prostoj-i-ponjatnyj-sposob-na-php.html
//   офицал.:  https://vk.com/dev/authcode_flow_user
// Подсказки: https://vk.com/topic-1_24428376?offset=2540

declare(strict_types=1);

include_once('config.php');

// Класс для авторизации по OAuth 2.0
// Может быть только по одному для каждого сервиса авторизации
class OAuth
{

    private static array $oauthServ = [];  // Созданные инстансы для сервисов авторизации: 'vk', 'yandex', 'gmail', ''
    private $thisServ;                     // Сервис текущего инстанса 

    public static function getInstance($services): OAuth
    {
        $srv = strtolower($services);
        if (!key_exists($srv, self::$oauthServ)) {
            $instance = new self();
            self::$oauthServ[] = [$srv => $instance];
            $instance->thisServ = $srv;
        } else {
            $instance = self::$oauthServ[$srv];
        }

        return $instance;
    }

    private function __construct()
    {
    }
    private function __clone()
    {
    }
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }

    // Формируем ссылку для авторизации 
    // $action - дополнительный параметр redirect_uri. Например: для $action='register', ссылка будет http://sotnikovdv.ru:86/oauthvc?act=register
    public function requestAuthorizationCodeURI($action = null): string
    {

        switch ($this->thisServ) {
            case 'vk':
                if (!empty($action)) {
                    $redirect_uri = VK_REDIRECT_URI . '?act=' . $action;
                } else {
                    $redirect_uri = VK_REDIRECT_URI;
                }
                $params = array(
                    'client_id'     => VK_CLIENT_ID,     // ID приложения, зарегистрированного на VK
                    'redirect_uri'  => $redirect_uri,  // адрес редиректа после подтверждения пользователя, например: http://sotnikovdv.ru:86/oauthvc
                    'response_type' => 'code',
                    'v'             => VK_API_VERS,     // (обязательный параметр) версия API https://vk.com/dev/versions
                    // Права доступа приложения https://vk.com/dev/permissions
                    // Если указать "offline", полученный access_token будет "вечным" (токен умрёт, если пользователь сменит свой пароль или удалит приложение).
                    // Если не указать "offline", то полученный токен будет жить 12 часов.
                    'scope'         => VK_SCOPE,
                );

                $qauth_uri = VK_QAUTH_URI . urldecode(http_build_query($params));
                break;
        }
        return  $qauth_uri;
    }
    // Запрос токена пользователя
    // $code - код для получения сведений с API. Получен в $GET['code'] на странице, на которую был редирект от сервиса после подтверждения пользователем предоставления прав (по адресу сформированному функцией requestAuthorizationCodeURI)
    public function getUserToken($code, $action = null)
    {
        switch ($this->thisServ) {
            case 'vk':
                if (!empty($action)) {
                    $redirect_uri = VK_REDIRECT_URI . '?act=' . $action;
                } else {
                    $redirect_uri = VK_REDIRECT_URI;
                }
                $params = array(
                    'client_id'     => VK_CLIENT_ID,
                    'client_secret' => VK_CLIENT_SECRET,
                    'code'          => $code,
                    'redirect_uri'  => $redirect_uri
                );
                $responce_uri = VK_QAUTH_AT . urldecode(http_build_query($params));
                break;
        }
        //echo $responce_uri.'<br>';

        if (!$content = @file_get_contents($responce_uri)) {
            $error = error_get_last();
            throw new Exception('HTTP request failed. Error: ' . $error['message']);
        }

        $response = json_decode($content);
        //print_r($response);

        // Если при получении токена произошла ошибка
        if (isset($response->error)) {
            throw new Exception('При получении токена произошла ошибка. Error: ' . $response->error . '. Error description: ' . $response->error_description);
        }
        return $response;
    }
    // Запрос информации о пользователе
    // $fields - набор полей для запроса. Например: 'uid,first_name,last_name,screen_name,sex,bdate,photo_big'
    public function getUserInfo($userId, $token, $fields)
    {

        switch ($this->thisServ) {
            case 'vk':
                $params = array(
                    'uids' => $userId,
                    'fields' => $fields,
                    'access_token' => $token,
                    'v' => VK_API_VERS
                );
                $responce_uri = VK_API_USER . urldecode(http_build_query($params));
                break;
        }

        if (!$userInfo = json_decode(file_get_contents($responce_uri))) {
            $error = error_get_last();
            throw new Exception('HTTP request failed. Error: ' . $error['message']);
        }

        if (isset($userInfo->response[0]->id)) {
            $userInfo = $userInfo->response[0];
            return $userInfo;
        } else {
            return null;
        }
        var_dump($userInfo);
        echo '<br>';
        return $userInfo;
    }
}
