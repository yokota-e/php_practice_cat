CREATE DATABASE IF NOT EXISTS `cat_cafe` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `cat_cafe`;

-- 新規ユーザーの作成と権限付与
CREATE USER IF NOT EXISTS 'catcafeuser'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON `cat_cafe`.* TO 'catcafeuser'@'localhost';
FLUSH PRIVILEGES;


CREATE TABLE `breeds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `cats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `breed_id` int(11) NOT NULL,
  `gender` tinyint(4) NOT NULL COMMENT '1:おとこのこ 2:おんなのこ',
  `age` int(11) NOT NULL,
  `profile` text,
  `image_name` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_name` (`name`),
  KEY `fk_breed_id` (`breed_id`),
  CONSTRAINT `cats_ibfk_1` FOREIGN KEY (`breed_id`) REFERENCES `breeds` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `breeds` (`name`) VALUES
('ミックス'),
('マンチカン'),
('スコティッシュフォールド'),
('アメリカンショートヘア'),
('ラグドール'),
('ベンガル'),
('ロシアンブルー');

INSERT INTO `cats` (`name`, `breed_id`, `gender`, `age`, `profile`, `image_name`) VALUES
('タマ', 1, 1, 3, 'のんびり屋でいつも日向ぼっこをしています。', NULL),
('ミケ', 1, 2, 2, '好奇心旺盛！おもちゃが大好きです。', NULL),
('レオ', 2, 1, 4, '短い足で一生懸命走る姿がキュートです。', NULL),
('モモ', 3, 2, 1, '甘えん坊で人懐っこい性格です。', NULL);
