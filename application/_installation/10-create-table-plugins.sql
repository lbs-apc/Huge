CREATE TABLE IF NOT EXISTS `plugins` (
  `plugin_id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin_name` text NOT NULL,
  `version` text NOT NULL,
  `installed` int(11) NOT NULL DEFAULT 0,
  `active` int(11) NOT NULL DEFAULT 0,
  `path` text NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`plugin_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
