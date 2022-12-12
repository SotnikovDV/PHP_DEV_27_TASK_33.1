<?php

require_once 'mysql.class.php';

class Chats
{

    public $lastErrors;

    public $my_id;

    public $list; // список сообщений

    function __construct($my_user_id)
    {
        $this->my_id = $my_user_id;
    }

    // считываем список актуальных заказов из базы данных
    function readList($user_id)
    {

        unset($list); //$list = [];

        // подключаемся к БД
        $db = new MySQL(DB_SERVER, DB_NAME, DB_USER, DB_PASS);
        if (!$db) {
            $this->lastErrors[] = 'Ошибка подключения к базе данных: ';
            return null;
        }

        // считываем чаты с указанным полльзователем
        $sql = 'SELECT c.*, case when c.user_from = ' . $this->my_id .
            ' then 0 else 1 end direction' .
            ' from chats c' .
            ' where (c.user_from = ' . $this->my_id . ' and c.user_to = ' . $user_id.')'.
            ' or (c.user_to = ' . $this->my_id .' and c.user_from = ' . $user_id.')'.
            ' order by c.time';

        $this->list = $db->select($sql);

        $error = $db->getErrorMessage();

        // Закрываем БД
        $db->closeConnection();

        if (!$this->list) {
            $this->lastErrors[] = 'Ошибка считывания сообщений чата: '.$error;
            return null;
        } else {
            return $this->list;
        }
    }

    // получаем кол-во сообщений в чате
    function getMsgCount($user_id)
    {

        // подключаемся к БД
        $db = new MySQL(DB_SERVER, DB_NAME, DB_USER, DB_PASS);
        if (!$db) {
            $this->lastErrors[] = 'Ошибка подключения к базе данных: ';
            return null;
        }

        // считываем чаты с указанным полльзователем
        $sql = 'SELECT count(1) cnt' .
            ' from chats c' .
            ' where (c.user_from = ' . $this->my_id . ' and c.user_to = ' . $user_id.')'.
            ' or (c.user_to = ' . $this->my_id .' and c.user_from = ' . $user_id.')';

        $row = $db->select_row($sql);

        $error = $db->getErrorMessage();

        // Закрываем БД
        $db->closeConnection();

        if (!$row) {
            $this->lastErrors[] = 'Ошибка считывания количества сообщений чата: '.$error;
            return 0;
        } else {
            return $row['cnt'];
        }
    }

    // добавление сообщения чата
    public function addChatMsg($from, $to, $content)
    {

        $this->lastErrors = [];

        // подключаемся к БД
        $db = new MySQL(DB_SERVER, DB_NAME, DB_USER, DB_PASS);
        if (!$db) {
            $this->lastErrors[] = 'Ошибка подключения к базе данных: ';
            return null;
        }

        $sql = 'INSERT INTO chats (user_from, user_to, time, content ) VALUES ("'
            . mysqli_real_escape_string($db->db_connect, $from) . '", "'
            . mysqli_real_escape_string($db->db_connect, $to) . '", '
            . 'sysdate(), "'
            . mysqli_real_escape_string($db->db_connect, $content) . '");';


        $result = $db->update($sql);

        if (!$result) {
            $error = $db->getErrorMessage();
        }

        // Закрываем БД
        $db->closeConnection();

        if (!$result) {
            $this->lastErrors[] = 'Ошибка добавления пользователя: ' . $error;
            return false;
        } else {
            // Ищем пользователя
            return true;
        }
    }
}