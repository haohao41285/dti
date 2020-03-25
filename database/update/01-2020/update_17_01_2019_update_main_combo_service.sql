ALTER TABLE `main_combo_service` ADD `cs_type_time` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '1-month 2-day' AFTER `cs_expiry_period`;
ALTER TABLE `main_combo_service` CHANGE `cs_expiry_period` `cs_expiry_period` INT(5) NULL DEFAULT NULL COMMENT 'expiry period to use'; 