CREATE TABLE dummy.meta_repository (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `label_singular` VARCHAR(255) NOT NULL,
    `label_plural` VARCHAR(255) NOT NULL,
    `parents` TEXT NULL,
    `children` TEXT NULL,
    UNIQUE(`name`),
    PRIMARY KEY (`id`) 
);

CREATE TABLE dummy.meta_repository_field (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `label` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `required` TINYINT NOT NULL DEFAULT 0,
    `searchable` TINYINT NOT NULL DEFAULT 0,
    `sortable` TINYINT NOT NULL DEFAULT 0,
    `main` TINYINT NOT NULL DEFAULT 0,
    `list` TINYINT NOT NULL DEFAULT 0,
    `type_id` INTEGER NOT NULL,
    PRIMARY KEY (`id`)
);