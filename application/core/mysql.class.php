<?php

class MySQL
{
    private $db_server, $db_name, $db_user, $db_pass, $db_error;
    public $db_connect;

    function __construct($db_server, $db_name, $db_user, $db_pass)
    {
        $this->db_server  = $db_server;
        $this->db_name    = $db_name;
        $this->db_user    = $db_user;
        $this->db_pass    = $db_pass;
        $this->db_connect = $this->openConnection();
    }

    // Возвращает последнюю ошибку (результат - ассоциативный массив)
    public function getError()
    {
        if (!$this->db_error) {
            return false;
        } else {
            return $this->db_error;
        }
    }

    public function getErrorMessage()
    {
        if (!$this->db_error) {
            return null;
        } else {
            $error = null;
            //$errors = $this->getError();
            if ($this->db_error) {
                foreach ($this->db_error as $key => $err){
                    if (isset($err['message'])){
                        $error .= $err['message'].PHP_EOL;
                    }    
                }
            }
            return $error;
        }
    }

    // Сохранение последней ошибки
    private function setError($code, $message, $sql = '')
    {
        $this->db_error[]['code'] = $code;
        $this->db_error[]['message'] = $message;
        $this->db_error[]['sql'] = $sql;
        return $this->db_error;
    }
    // Очистка ошибки
    private function clearError()
    {
        $this->db_error = [];
        /*$this->db_error['code'] = null;
        $this->db_error['message'] = null;
        $this->db_error['sql'] = null;*/
    }

    // Подключение к базе данных
    public function openConnection()
    {

        $this->clearError();

        if (!$this->db_connect) {
            $connect = mysqli_connect($this->db_server, $this->db_user, $this->db_pass);
            if ($connect) {
                mysqli_select_db($connect, $this->db_name);
                return $connect;
            } else {
                $this->setError(mysqli_connect_errno(), mysqli_connect_error());
                return false;
                //???$e = oci_error();
                //???Log::Log('[ ОШИБКА ]: '.$e['message'].($e['sqltext'] ? PHP_EOL.PHP_EOL.' Позиция: '.$e['offset'].PHP_EOL.$e['sqltext'].PHP_EOL.PHP_EOL : ''));
            }
        } else {
            return $this->db_connect;
        }
    }

    // Закрытие подключения
    public function closeConnection()
    {

        if ($this->db_connect) {
            mysqli_close($this->db_connect);
        }
    }

    // Запрос массива данных
    // !!! потом добавить парамтр int $result_mode = MYSQLI_STORE_RESULT для mysqli_query 
    public function select($sql)
    {

        $this->clearError();

        //$row_count = 0;
        $rows = [];
        try {
            $query = mysqli_query($this->db_connect, $sql);
            if ($query) {
                /*
                while ($rows[$row_count] = mysqli_fetch_assoc($query)) {
                    $row_count++;
                }
                */
                // может правильнее так:
                while ($r = mysqli_fetch_assoc($query)) {
                    $rows[] = $r;
                }
                mysqli_free_result($query);
                return $rows;
            } else {
                $this->setError(mysqli_errno($this->db_connect), mysqli_error($this->db_connect), $sql);
                return false;
            }
        } catch (Exception $e) {
            $this->setError(mysqli_errno($this->db_connect), mysqli_error($this->db_connect), $sql);
            return false;
        }
    }

    // Запрос одной строки данных
    // !!! потом добавить парамтр int $result_mode = MYSQLI_STORE_RESULT для mysqli_query 
    public function select_row($sql)
    {

        $this->clearError();

        try {
            $query = mysqli_query($this->db_connect, $sql);
            if ($query) {
                $row = mysqli_fetch_assoc($query);
                mysqli_free_result($query);
                return $row;
            } else {
                $this->setError(mysqli_errno($this->db_connect), mysqli_error($this->db_connect), $sql);
                return false;
            }
        } catch (Exception $e) {
            $this->setError(mysqli_errno($this->db_connect), mysqli_error($this->db_connect), $sql);
            return false;
        }
    }
    // Обновление данных
    public function update($sql)
    {

        $this->clearError();

        try {
            if (mysqli_query($this->db_connect, $sql)) {
                return true;
            } else {
                $this->setError(mysqli_errno($this->db_connect), mysqli_error($this->db_connect), $sql);
                return false;
            }
        } catch (Exception $e) {
            $this->setError(mysqli_errno($this->db_connect), mysqli_error($this->db_connect), $sql);
            return false;
        }
    }
}
