/*
 * meta_repository
 */
CREATE TABLE IF NOT EXISTS meta_repository (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `label_singular` VARCHAR(255) NOT NULL,
    `label_plural` VARCHAR(255) NOT NULL,
    `parents` TEXT NULL,
    `children` TEXT NULL,
    UNIQUE(`name`),
    PRIMARY KEY (`id`) 
) ENGINE=InnoDB;

/*
 * meta_repository_field
 */
CREATE TABLE IF NOT EXISTS meta_repository_field (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `repository_id` INTEGER NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `label` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `required` TINYINT NOT NULL DEFAULT 0,
    `searchable` TINYINT NOT NULL DEFAULT 0,
    `sortable` TINYINT NOT NULL DEFAULT 0,
    `main` TINYINT NOT NULL DEFAULT 0,
    `list` TINYINT NOT NULL DEFAULT 0,
    `type` VARCHAR(255) NOT NULL,
    `options` TEXT NULL,
    `display_order` INTEGER NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`repository_id`) REFERENCES `meta_repository`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

/*
 * meta_app
 */
CREATE TABLE IF NOT EXISTS meta_app (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `is_admin` TINYINT NOT NULL DEFAULT 0,
    `base_url` VARCHAR(255) NOT NULL,
    `public_key` VARCHAR(64) NOT NULL,
    `private_key` VARCHAR(64) NOT NULL,
    UNIQUE(`name`),
    UNIQUE(`public_key`),
    UNIQUE(`private_key`),
    PRIMARY KEY (`id`) 
) ENGINE=InnoDB;

/* insert default application */

INSERT INTO `meta_app` (`name`,`title`,`description`,`is_admin`,`base_url`,`public_key`,`private_key`) VALUES ("dummy_app","Dummy App","Default application",1,"http://localhost/~michael/dummy-client/","dummy_app","dummy_app");

/*
 * meta_app_grants
 */
CREATE TABLE IF NOT EXISTS meta_app_grant (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `app_id` INTEGER NOT NULL,
    `repository_id` INTEGER NOT NULL,
    `create` INTEGER NOT NULL DEFAULT 0,
    `read` INTEGER NOT NULL DEFAULT 0,
    `update` INTEGER NOT NULL DEFAULT 0,
    `delete` INTEGER NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`app_id`) REFERENCES `meta_app`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`repository_id`) REFERENCES `meta_repository`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;