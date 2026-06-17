CREATE TABLE IF NOT EXISTS `gallery` (
  `image_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) unsigned NOT NULL,
  `filename` varchar(255) NOT NULL,
  `is_shared` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`image_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
