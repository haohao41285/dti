ALTER TABLE `pos_place` ADD `booking_v2` INT(1) NOT NULL DEFAULT '0' COMMENT 'old theme(1: on, 0:off)' AFTER `place_buy_sms`;