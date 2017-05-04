CREATE TABLE /*TABLE_PREFIX*/t_user_messages (
  `pk_message_id` int(10) NOT NULL AUTO_INCREMENT,
  `i_message_id` int(10) NOT NULL,
  `i_from_user_id` int(10) NOT NULL,
  `i_to_user_id` int(10) NOT NULL,
  `i_item_id` INT(10) NOT NULL,
  `i_message_read` int(1) NOT NULL,
  `dt_message_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dt_message_read` timestamp NULL DEFAULT NULL,
  `s_message_title` varchar(100) NOT NULL,
  `s_message_content` text NOT NULL,
  PRIMARY KEY (`pk_message_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI' AUTO_INCREMENT=1;