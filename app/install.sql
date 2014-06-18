CREATE TABLE dummy.meta_app (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `password` VARCHAR(255) NOT NULL,
    UNIQUE(`name`),
    PRIMARY KEY (`id`) 
);

CREATE TABLE dummy.meta_app_grant (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `app_id` INTEGER NOT NULL,
    `repository_id` INTEGER NOT NULL,
    `get` TINYINT NOT NULL DEFAULT 0,
    `post` TINYINT NOT NULL DEFAULT 0,
    `put` TINYINT NOT NULL DEFAULT 0,
    `delete` TINYINT NOT NULL DEFAULT 0,
PRIMARY KEY (`id`) 
);

CREATE TABLE dummy.meta_param (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `value` TEXT NOT NULL,
    UNIQUE(`name`),
    PRIMARY KEY (`id`)
);

INSERT INTO dummy.meta_param (`name`, `value`) VALUES ("env", "dev");
INSERT INTO dummy.meta_param (`name`, `value`) VALUES ("dir.uploads", "");

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

CREATE TABLE dummy.meta_repository_field_type (
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `class` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
);

INSERT INTO dummy.meta_repository_field_type (`name`, `class`) VALUES("string", "\Michcald\Dummy\Model\Field\String");
INSERT INTO dummy.meta_repository_field_type (`name`, `class`) VALUES("text", "\Michcald\Dummy\Model\Field\Text");
INSERT INTO dummy.meta_repository_field_type (`name`, `class`) VALUES("integer", "\Michcald\Dummy\Model\Field\Integer");
INSERT INTO dummy.meta_repository_field_type (`name`, `class`) VALUES("date", "\Michcald\Dummy\Model\Field\Date");
INSERT INTO dummy.meta_repository_field_type (`name`, `class`) VALUES("datetime", "\Michcald\Dummy\Model\Field\Datetime");
INSERT INTO dummy.meta_repository_field_type (`name`, `class`) VALUES("file", "\Michcald\Dummy\Model\Field\File");
INSERT INTO dummy.meta_repository_field_type (`name`, `class`) VALUES("boolean", "\Michcald\Dummy\Model\Field\Boolean");