use labrador;

ALTER TABLE `projects` ADD `accession_custom` VARCHAR(50) AFTER `notes`;

CREATE TABLE `config` (
	`id` INT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`option` VARCHAR(250) NOT NULL,
	`value` VARCHAR(250)
);

ALTER TABLE datasets MODIFY notes VARCHAR(255);
