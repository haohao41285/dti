ALTER TABLE `main_team_type` ADD `slug` VARCHAR(255) NOT NULL COMMENT 'slug and a column of main_customer_template are the same for get customer_status' AFTER `team_type_name`; 
ALTER TABLE `main_team_type` CHANGE `team_type_description` `team_type_description` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL; 