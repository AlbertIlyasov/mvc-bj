<?
die;
?>
CREATE TABLE `task` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `text` TEXT NOT NULL,
    `status` TINYINT(4) UNSIGNED NOT NULL COMMENT '0-not completed;1-completed',
    `isChanged` TINYINT(3) UNSIGNED NOT NULL,
    PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;
