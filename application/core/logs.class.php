<?php 

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\HtmlFormatter;

class Logs {
    
}

/*
// Создаем логгер 
$log = new Logger('mylogger');

// Хендлер, который будет писать логи в "mylog.log" и слушать все ошибки с уровнем "WARNING" и выше .
$log->pushHandler(new StreamHandler('mylog.log', Logger::WARNING));

// Хендлер, который будет писать логи в "troubles.log" и реагировать на ошибки с уровнем "ALERT" и выше.
$log->pushHandler(new StreamHandler('troubles.log', Logger::ALERT));


// Добавляем записи
$log->warning('Предупреждение');
$log->error('Большая ошибка');
$log->info('Просто тест');
*/