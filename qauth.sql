/*
SQLyog Ultimate v13.1.1 (64 bit)
MySQL - 8.0.24 : Database - qauth
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`qauth` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `qauth`;

/*Table structure for table `chats` */

DROP TABLE IF EXISTS `chats`;

CREATE TABLE `chats` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_from` int DEFAULT NULL,
  `user_to` int DEFAULT NULL,
  `time` datetime NOT NULL,
  `content` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `chats_from` (`user_from`),
  KEY `chats_to` (`user_to`),
  CONSTRAINT `chats_from` FOREIGN KEY (`user_from`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `chats_to` FOREIGN KEY (`user_to`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `chats` */

insert  into `chats`(`id`,`user_from`,`user_to`,`time`,`content`) values 
(1,2,3,'2022-12-11 17:24:23','Привет! Как дела?'),
(2,3,2,'2022-12-11 17:25:51','Привет! Дела отлично! А ты как?'),
(3,3,1,'2022-12-12 15:38:03','How are your?'),
(4,3,2,'2022-12-12 20:17:05','тестовое сообщение'),
(5,3,2,'2022-12-12 20:21:25','С новым годом!!!'),
(6,2,3,'2022-12-12 20:23:12','И тебя, мой дорогой!'),
(7,3,2,'2022-12-12 20:58:09','Спасибо!'),
(8,2,3,'2022-12-12 20:58:37','Пожалуйста!'),
(15,3,2,'2022-12-12 22:15:07','1111'),
(16,2,3,'2022-12-12 22:15:35','222'),
(17,3,2,'2022-12-12 22:15:49','3333');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `login` varchar(30) NOT NULL,
  `password` varchar(100) NOT NULL,
  `hash` varchar(100) NOT NULL DEFAULT '',
  `vcuser` varchar(300) DEFAULT '',
  `yuser` varchar(300) DEFAULT '',
  `guser` varchar(300) DEFAULT '',
  `name` varchar(300) DEFAULT '',
  `photo_file` varchar(500) DEFAULT '',
  `nikname` varchar(30) DEFAULT '',
  PRIMARY KEY (`id`,`login`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`login`,`password`,`hash`,`vcuser`,`yuser`,`guser`,`name`,`photo_file`,`nikname`) values 
(1,'jb@yandex.ru','$2y$10$.q5CT34pNN8Ihrz6R1GaTOLVAzAQ.R.ipkksWGj0Ubril3oxqig2y','aejrD1','','','','Джеймс Бонд','jb.png','JB'),
(2,'sotn@yandex.ru','$2y$10$h08DxvHKC8fzXUT5n3ZG4e5Kyze8bgBi.Ac3Dbhe151BbR4RMZSzu','soxmfn','','','','Дмитрий Сотников','','DSot'),
(3,'dv@gmail.com','$2y$10$WqJo5QFleEKhONo8c/M3W./R26otaigal8NSNJuWzF/nvYoI/gdqi','AUpZvO','','','','Алекс Пушкин','DVSt.jpg','Alex'),
(20,'sotnikovdv@sl.ru','$2y$10$rtyFXTswWdRAnxb6Rb.m9e9kMbJC8rtggEpDSVDUzBorCHdLIBKXC','4ZeUcP','','','','Вася Иванов','','Vasya');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
