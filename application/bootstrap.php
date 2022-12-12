<?php
/*require_once 'core/model.php'; 
require_once 'core/view.php'; 
require_once 'core/controller.php'; 
require_once 'core/config.php';*/
/*require_once 'core/class/mysql.class.php';
require_once 'core/class/user.class.php'; 
require_once 'core/route.php'; */
require_once ($_SERVER['DOCUMENT_ROOT'].'/application/core/config.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/application/core/mail.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/application/core/autoload.php');
Route::start(); // запускаем маршрутизатор
?>