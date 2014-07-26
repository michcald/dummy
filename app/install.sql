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
    `type` INTEGER NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`repository_id`) REFERENCES `meta_repository`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS meta_app (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `public_key` VARCHAR(64) NOT NULL,
    `private_key` VARCHAR(64) NOT NULL,
    UNIQUE(`public_key`),
    UNIQUE(`private_key`),
    PRIMARY KEY (`id`) 
) ENGINE=InnoDB;

/* insert default application */

INSERT INTO meta_app (`name`,`description`,`public_key`,`private_key`) VALUES ("dummy_app","Default application","829c3804401b0727f70f73d4415e162400cbe57b","f64133af6818761d95c8230953e5c9ddee1d0cf3");