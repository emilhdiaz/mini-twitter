CREATE TABLE `user`(
`id` INT AUTO_INCREMENT,
`email` VARCHAR(255),
`password` VARCHAR(255),
PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `post` (
`id` INT AUTO_INCREMENT,
`user_id` INT,
`body` TEXT,
PRIMARY KEY (`id`),
FOREIGN KEY (`user_id`) REFERENCES `user`(`id`)
) ENGINE=InnoDB;

CREATE TABLE `follower` (
`user_id` INT,
`follower_user_id` INT,
PRIMARY KEY (user_id, follower_user_id),
CONSTRAINT `follower_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user`(`id`),
CONSTRAINT `follower_follower_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `user`(`id`)
) ENGINE=InnoDB;