ALTER TABLE `main_team_type` ADD `team_customer_status` TEXT NULL COMMENT 'json_encode of customer_id:status' AFTER `team_type_description`;
