CREATE TABLE IF NOT EXISTS `chats` (
  `chat_id` int(11) NOT NULL AUTO_INCREMENT,
  `receiver_id` int(11) NOT NULL DEFAULT 0,
  `sender_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`chat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
