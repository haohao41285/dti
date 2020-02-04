ALTER TABLE `main_combo_service_bought` ADD `csb_last_call` DATETIME NULL COMMENT 'for remider cskh call' AFTER `csb_note`;
ALTER TABLE `main_combo_service_bought` ADD `csb_status_call` TINYINT(1) NULL COMMENT 'for check calling or not' AFTER `csb_last_call`;
ALTER TABLE `main_combo_service_bought` ADD `csb_user_call` INT(11) NULL COMMENT 'for main_user' AFTER `csb_status_call`; 