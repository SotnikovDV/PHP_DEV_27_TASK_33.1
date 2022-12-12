<?php
// Параметры подключения к БД
const DB_SERVER = 'localhost';
const DB_NAME   = 'qauth'; 
const DB_USER   = 'root';
const DB_PASS   = '';  //DVSt1969-DB
// Параметры приложения VK
const VK_CLIENT_ID      = '51449619'; // ID приложения
const VK_CLIENT_SECRET  = 'ICYoAqm3ENPwYarEU398'; // Защищённый ключ
const VK_REDIRECT_URI   = 'http://sotnikovdv.ru:86/oauthvc'; // Адрес, на который будет переадресован пользователь после прохождения авторизации
const VK_API_VERS = '5.131'; // Версия VK API   
const VK_SCOPE = 'email,offline';  // Запрашиваемы у VK права
const VK_QAUTH_URI = 'http://oauth.vk.com/authorize?';
const VK_QAUTH_AT = 'https://oauth.vk.com/access_token?';
const VK_API_USER = 'https://api.vk.com/method/users.get?';