<?php
$contents = file_get_contents(__FILE__, false, null, __COMPILER_HALT_OFFSET__);
header('Content-Type: text/sql');
header('Content-Disposition: inline; filename="gehgen.sql"');
echo $contents;
__halt_compiler();/*
Target Server Type    : MYSQL
Target Server Version : 50634
File Encoding         : 65001
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `categories`
-- ----------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`theme_id`  int(11) NOT NULL ,
`name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`updated`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`),
UNIQUE INDEX `categories_id` (`id`) USING BTREE ,
INDEX `categories_theme_id` (`theme_id`) USING BTREE ,
INDEX `categories_name` (`name`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=241

;

-- ----------------------------
-- Table structure for `gabarits`
-- ----------------------------
DROP TABLE IF EXISTS `gabarits`;
CREATE TABLE `gabarits` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`season_id`  int(11) NOT NULL ,
`name`  varchar(512) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`sections`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`locked`  tinyint(4) NOT NULL DEFAULT 0 ,
`updated`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`),
UNIQUE INDEX `gabarits_id` (`id`) USING BTREE ,
INDEX `gabarits_season_id` (`season_id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=85

;

-- ----------------------------
-- Table structure for `levels`
-- ----------------------------
DROP TABLE IF EXISTS `levels`;
CREATE TABLE `levels` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`name`  varchar(512) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
`updated`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`),
UNIQUE INDEX `levels_id` (`id`) USING BTREE ,
INDEX `levels_name` (`name`(255)) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=14

;

-- ----------------------------
-- Table structure for `messages`
-- ----------------------------
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`parent_id`  int(11) NOT NULL DEFAULT 0 ,
`user_id`  int(11) NOT NULL ,
`from_id`  int(11) NOT NULL DEFAULT 0 ,
`subject`  varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`body`  longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`unread`  tinyint(4) NOT NULL DEFAULT 1 ,
`hidden`  tinyint(4) NOT NULL DEFAULT 0 ,
`sent`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`),
INDEX `messages_id` (`id`) USING BTREE ,
INDEX `messages_parent_id` (`parent_id`) USING BTREE ,
INDEX `messages_user_id` (`user_id`) USING BTREE ,
INDEX `messages_from_id` (`from_id`) USING BTREE ,
INDEX `messages_unread` (`unread`) USING BTREE ,
INDEX `messages_hidden` (`hidden`) USING BTREE ,
INDEX `messages_sent` (`sent`) USING BTREE ,
INDEX `messages_user_id_unread_hidden` (`user_id`, `unread`, `hidden`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=175

;

-- ----------------------------
-- Table structure for `modes`
-- ----------------------------
DROP TABLE IF EXISTS `modes`;
CREATE TABLE `modes` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`name`  varchar(512) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`updated`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`),
UNIQUE INDEX `modes_id` (`id`) USING BTREE ,
INDEX `modes_name` (`name`(255)) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=12

;

-- ----------------------------
-- Table structure for `questions`
-- ----------------------------
DROP TABLE IF EXISTS `questions`;
CREATE TABLE `questions` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`type_id`  int(11) NOT NULL ,
`user_id`  int(11) NOT NULL DEFAULT 0 ,
`slug`  varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`question`  varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`answer`  varchar(512) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`timesense`  tinyint(4) NOT NULL DEFAULT 0 ,
`archive`  tinyint(4) NOT NULL DEFAULT 0 ,
`approve`  tinyint(4) NOT NULL DEFAULT 0 ,
`locked`  tinyint(4) NOT NULL DEFAULT 0 ,
`hidden`  tinyint(4) NOT NULL DEFAULT 0 ,
`misc`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`lastused`  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ,
`updated`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`),
UNIQUE INDEX `questions_id` (`id`) USING BTREE ,
INDEX `questions_type_id` (`type_id`) USING BTREE ,
INDEX `questions_lastused` (`lastused`) USING BTREE ,
INDEX `questions_updated` (`updated`) USING BTREE ,
INDEX `questions_updated_id` (`id`, `updated`) USING BTREE ,
INDEX `questions_locked` (`locked`) USING BTREE ,
INDEX `questions_timesense` (`timesense`) USING BTREE ,
INDEX `questions_approve` (`approve`) USING BTREE ,
INDEX `questions_user_id` (`user_id`) USING BTREE ,
INDEX `questions_archive` (`archive`) USING BTREE ,
INDEX `question_hidden` (`hidden`) USING BTREE ,
INDEX `questions_slug` (`slug`(255)) USING BTREE ,
INDEX `question_hidden_slug` (`slug`(255), `hidden`) USING BTREE ,
FULLTEXT INDEX `questions_question` (`question`) ,
FULLTEXT INDEX `questions_answer` (`answer`) ,
FULLTEXT INDEX `questions_misc` (`misc`) 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=100000

;

-- ----------------------------
-- Table structure for `questions_categories`
-- ----------------------------
DROP TABLE IF EXISTS `questions_categories`;
CREATE TABLE `questions_categories` (
`question_id`  int(11) NOT NULL ,
`category_id`  int(11) NOT NULL ,
PRIMARY KEY (`question_id`, `category_id`),
UNIQUE INDEX `questions_categories_question_id_category_id` (`question_id`, `category_id`) USING BTREE ,
INDEX `questions_categories_question_id` (`question_id`) USING BTREE ,
INDEX `questions_categories_category_id` (`category_id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci

;

-- ----------------------------
-- Table structure for `questions_levels`
-- ----------------------------
DROP TABLE IF EXISTS `questions_levels`;
CREATE TABLE `questions_levels` (
`question_id`  int(11) NOT NULL ,
`level_id`  int(11) NOT NULL ,
PRIMARY KEY (`question_id`, `level_id`),
UNIQUE INDEX `questions_levels_level_id_levels_question_id` (`question_id`, `level_id`) USING BTREE ,
INDEX `questions_levels_level_id` (`level_id`) USING BTREE ,
INDEX `questions_levels_question_id` (`question_id`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci

;

-- ----------------------------
-- Table structure for `quizzes`
-- ----------------------------
DROP TABLE IF EXISTS `quizzes`;
CREATE TABLE `quizzes` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`level_id`  int(11) NOT NULL ,
`gabarit_id`  int(11) NOT NULL ,
`user_id`  int(11) NOT NULL ,
`name`  varchar(512) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`sections`  longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`comments`  longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`generated`  tinyint(4) NOT NULL DEFAULT 0 ,
`edited`  tinyint(4) NOT NULL DEFAULT 0 ,
`approved`  tinyint(4) NOT NULL DEFAULT 0 ,
`updated`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`),
UNIQUE INDEX `quizzes_id` (`id`) USING BTREE ,
INDEX `quizzes_level_id` (`level_id`) USING BTREE ,
INDEX `quizzes_gabarit_id` (`gabarit_id`) USING BTREE ,
INDEX `quizzes_user_id` (`user_id`) USING BTREE ,
INDEX `quizzes_updated` (`updated`) USING BTREE ,
INDEX `quizzes_edited` (`edited`) USING BTREE ,
INDEX `quizzes_approved` (`approved`) USING BTREE ,
INDEX `quizzes_edited_approved` (`edited`, `approved`) USING BTREE ,
INDEX `quizzes_generated` (`generated`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=107

;

-- ----------------------------
-- Table structure for `roles`
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`name`  varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`slug`  varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`updated`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`),
UNIQUE INDEX `roles_id` (`id`) USING BTREE ,
INDEX `roles_name` (`name`(255)) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=5

;

-- ----------------------------
-- Table structure for `seasons`
-- ----------------------------
DROP TABLE IF EXISTS `seasons`;
CREATE TABLE `seasons` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`name`  varchar(512) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`active`  tinyint(4) NOT NULL DEFAULT 0 ,
`updated`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`),
UNIQUE INDEX `seasons_id` (`id`) USING BTREE ,
INDEX `seasons_active` (`active`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=3

;

-- ----------------------------
-- Table structure for `themes`
-- ----------------------------
DROP TABLE IF EXISTS `themes`;
CREATE TABLE `themes` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`temporary`  tinyint(4) NOT NULL DEFAULT 0 ,
`updated`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`),
UNIQUE INDEX `themes_id` (`id`) USING BTREE ,
INDEX `themes_temporary` (`temporary`) USING BTREE ,
INDEX `themes_name` (`name`) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=42

;

-- ----------------------------
-- Table structure for `types`
-- ----------------------------
DROP TABLE IF EXISTS `types`;
CREATE TABLE `types` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`name`  varchar(512) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
`slug`  varchar(514) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`updated`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`),
UNIQUE INDEX `types_id` (`id`) USING BTREE ,
INDEX `types_name` (`name`(255)) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=13

;

-- ----------------------------
-- Table structure for `users`
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`role_id`  int(11) NOT NULL ,
`name`  varchar(512) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`email`  varchar(512) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`pass`  varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3' ,
`active`  tinyint(4) NOT NULL DEFAULT 1 ,
`lastseen`  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ,
`updated`  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
PRIMARY KEY (`id`),
UNIQUE INDEX `users_id` (`id`) USING BTREE ,
INDEX `users_role_id` (`role_id`) USING BTREE ,
INDEX `users_login` (`email`(255), `pass`, `active`) USING BTREE ,
INDEX `users_id_active` (`id`, `active`) USING BTREE ,
INDEX `users_name` (`name`(255)) USING BTREE 
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=13

;

-- ----------------------------
-- Auto increment value for `categories`
-- ----------------------------
ALTER TABLE `categories` AUTO_INCREMENT=241;

-- ----------------------------
-- Auto increment value for `gabarits`
-- ----------------------------
ALTER TABLE `gabarits` AUTO_INCREMENT=85;

-- ----------------------------
-- Auto increment value for `levels`
-- ----------------------------
ALTER TABLE `levels` AUTO_INCREMENT=14;

-- ----------------------------
-- Auto increment value for `messages`
-- ----------------------------
ALTER TABLE `messages` AUTO_INCREMENT=175;

-- ----------------------------
-- Auto increment value for `modes`
-- ----------------------------
ALTER TABLE `modes` AUTO_INCREMENT=12;

-- ----------------------------
-- Auto increment value for `questions`
-- ----------------------------
ALTER TABLE `questions` AUTO_INCREMENT=100000;

-- ----------------------------
-- Auto increment value for `quizzes`
-- ----------------------------
ALTER TABLE `quizzes` AUTO_INCREMENT=107;

-- ----------------------------
-- Auto increment value for `roles`
-- ----------------------------
ALTER TABLE `roles` AUTO_INCREMENT=5;

-- ----------------------------
-- Auto increment value for `seasons`
-- ----------------------------
ALTER TABLE `seasons` AUTO_INCREMENT=3;

-- ----------------------------
-- Auto increment value for `themes`
-- ----------------------------
ALTER TABLE `themes` AUTO_INCREMENT=42;

-- ----------------------------
-- Auto increment value for `types`
-- ----------------------------
ALTER TABLE `types` AUTO_INCREMENT=13;

-- ----------------------------
-- Auto increment value for `users`
-- ----------------------------
ALTER TABLE `users` AUTO_INCREMENT=13;
