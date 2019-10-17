ALTER TABLE `main_tracking_history` ADD `receiver_id` INT(11) NULL COMMENT 'receiver notification' AFTER `email_list`;
ALTER TABLE `main_tracking_history` ADD `read_not` BOOLEAN NOT NULL COMMENT '1: read, 0: not' AFTER `receiver_id`;
