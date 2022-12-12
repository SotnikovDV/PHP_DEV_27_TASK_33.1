<?php
// Славнефть - осуществляет проверку на существование адреса получателя в корпоративной почте
$config['smtp_username'] = ''; //'oracle@slavneft.ru';  //Смените на адрес своего почтового ящика.
$config['smtp_port'] = '25'; //'465'; // Порт работы.
$config['smtp_host'] =  '10.11.0.3'; //ssl://smtp.yandex.ru';  //сервер для отправки почты
$config['smtp_password'] = ''; // '';  //Измените пароль
$config['smtp_from_name'] = 'Почтальон Парус'; //Ваше имя - или имя Вашего сайта. Будет показывать при прочтении в поле "От кого"
$config['smtp_from_mail'] = 'oracle@slavneft.ru'; //Ваше имя - или имя Вашего сайта. Будет показывать при прочтении в поле "От кого"
$config['mail_from'] = 'Почтальон "Парус" <oracle@slavneft.ru>'; //

// Яндекс, Google - не работает
/*
$config['smtp_username'] = 'dvst.com@gmail.com'; //'oracle@slavneft.ru';  //Смените на адрес своего почтового ящика.
$config['smtp_port'] = '465'; // Порт работы.
$config['smtp_host'] =  'ssl://smtp.gmail.com';  //сервер для отправки почты
$config['smtp_password'] = ''; // '';  //Измените пароль
$config['smtp_from_name'] = 'Дмитрий Сотников'; //Ваше имя - или имя Вашего сайта. Будет показывать при прочтении в поле "От кого"
$config['smtp_from_mail'] = 'dvst.com@gmail.com'; //Ваше имя - или имя Вашего сайта. Будет показывать при прочтении в поле "От кого"
$config['mail_from'] = 'Дмитрий Сотников <dvst.com@gmail.com>'; //
*/

$config['smtp_debug'] = true;  //Если Вы хотите видеть сообщения ошибок, укажите true вместо false
$config['smtp_charset'] = 'utf-8';	//кодировка сообщений. (windows-1251 или utf-8, итд)

function smtpmail($to, $mail_to, $subject, $message, $headers='') {
    global $config;
    $SEND =	"Date: ".date("D, d M Y H:i:s") . " UT\r\n";
    $SEND .= 'Subject: =?'.$config['smtp_charset'].'?B?'.base64_encode($subject)."=?=\r\n";
    if ($headers) $SEND .= $headers."\r\n\r\n";
    else
    {
        $SEND .= "Reply-To: ".$config['smtp_username']."\r\n";
        $SEND .= "To: \"=?".$config['smtp_charset']."?B?".base64_encode($to)."=?=\" <$mail_to>\r\n";
        $SEND .= "MIME-Version: 1.0\r\n";
        $SEND .= "Content-Type: text/html; charset=\"".$config['smtp_charset']."\"\r\n";
        $SEND .= "Content-Transfer-Encoding: 8bit\r\n";
        $SEND .= "From: \"=?".$config['smtp_charset']."?B?".base64_encode($config['smtp_from_name'])."=?=\" <".$config['smtp_from_mail'].">\r\n";
        $SEND .= "X-Priority: 3\r\n\r\n";
    }
    $SEND .=  $message."\r\n";

    if( !$socket = fsockopen($config['smtp_host'], $config['smtp_port'], $errno, $errstr, 30) ) {
        if ($config['smtp_debug']) echo '<p>Ошибка подключения к серверу эл.почты: '.$errno."<br>".$errstr.'</p>';
        return false;
    }

    if (!server_parse($socket, "220", __LINE__)) return false;

    //fputs($socket, "HELO " . $config['smtp_host'] . "\r\n");
    fputs($socket, "HELO mydomen.ru\r\n");
    if (!server_parse($socket, "250", __LINE__)) {
        if ($config['smtp_debug']) echo '<p>Не могу отправить HELO!</p>';
        fclose($socket);
        return false;
    }

    /* Если указано имя пользователя - то нужна smtp-авторизация */
    if (!empty($config['smtp_username'])) {
        fputs($socket, "AUTH LOGIN\r\n");
        if (!server_parse($socket, "334", __LINE__)) {
            if ($config['smtp_debug']) echo '<p>Не могу найти ответ на запрос авторизаци.</p>';
            fclose($socket);
            return false;
        }
        fputs($socket, base64_encode($config['smtp_username']) . "\r\n");
        if (!server_parse($socket, "334", __LINE__)) {
            if ($config['smtp_debug']) echo '<p>Логин авторизации не был принят сервером!</p>';
            fclose($socket);
            return false;
        }
        if (!empty($config['smtp_password'])) {
            fputs($socket, base64_encode($config['smtp_password']) . "\r\n");
            if (!server_parse($socket, "235", __LINE__)) {
                if ($config['smtp_debug']) echo '<p>Пароль не был принят сервером как верный! Ошибка авторизации!</p>';
                fclose($socket);
                return false;
            }
        }
    }

    fputs($socket, "MAIL FROM: <".$config['mail_from'].">\r\n");
    if (!server_parse($socket, "250", __LINE__)) {
        if ($config['smtp_debug']) echo '<p>Не могу отправить команду MAIL FROM: </p>';
        fclose($socket);
        return false;
    }

    
    fputs($socket, "RCPT TO: <" . $mail_to . ">\r\n");
    if (!server_parse($socket, "250", __LINE__)) {
        if ($config['smtp_debug']) echo '<p>Не могу отправить команду RCPT TO: </p>';
        fclose($socket);
        return false;
    }

    fputs($socket, "DATA\r\n");
    if (!server_parse($socket, "354", __LINE__)) {
        if ($config['smtp_debug']) echo '<p>Не могу отправить команду DATA</p>';
        fclose($socket);
        return false;
    }

    fputs($socket, $SEND."\r\n.\r\n");
    if (!server_parse($socket, "250", __LINE__)) {
        if ($config['smtp_debug']) echo '<p>Не смог отправить тело письма. Письмо не было отправленно!</p>';
        fclose($socket);
        return false;
    }

    fputs($socket, "QUIT\r\n");
    fclose($socket);
    return TRUE;
}

function server_parse($socket, $response, $line = __LINE__) {
    global $config;
    while (@substr($server_response, 3, 1) != ' ') {
        if (!($server_response = fgets($socket, 256))) {
            if ($config['smtp_debug']) echo "<p>Проблемы с отправкой почты!</p>$response<br>$line<br>";
            return false;
        }
    }
    if (!(substr($server_response, 0, 3) == $response)) {
        if ($config['smtp_debug']) echo "<p>Проблемы с отправкой почты!</p>$response<br>$line<br>Ответ сервера: ".substr($server_response, 0, 3);
        return false;
    }
    return true;
}
?>