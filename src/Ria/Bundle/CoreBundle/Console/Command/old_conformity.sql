CREATE TABLE `old_conformity` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`old_id` VARCHAR(32) NOT NULL,
	`current_id` INT(10) NOT NULL,
	`type` VARCHAR(10) NOT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;