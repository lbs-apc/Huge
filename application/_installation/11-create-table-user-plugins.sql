CREATE TABLE IF NOT EXISTS `user_plugins` (
  `user_plugins_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `plugin_id` int(11) NOT NULL,
  `installed` tinyint(4) NOT NULL DEFAULT 0,
  `active` tinyint(4) NOT NULL DEFAULT 0,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `installed_at` datetime NOT NULL,
  PRIMARY KEY (`user_plugins_id`),
  KEY `fk_user_id` (`user_id`),
  KEY `fk_plugin_id` (`plugin_id`),
  CONSTRAINT `fk_plugin_id` FOREIGN KEY (`plugin_id`) REFERENCES `plugins` (`plugin_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
s