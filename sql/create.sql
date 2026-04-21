
-- Script de création de la base de données


SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Utilisateurs 

CREATE USER IF NOT EXISTS 'app_user'@'%' IDENTIFIED BY 'app_user_password';
CREATE USER IF NOT EXISTS 'app_readonly'@'%' IDENTIFIED BY 'app_readonly_password';


-- BDD


CREATE DATABASE IF NOT EXISTS quai_antique
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE quai_antique;


-- Attribution des droits

GRANT SELECT, INSERT, UPDATE, DELETE ON quai_antique.* TO 'app_user'@'%';
GRANT SELECT ON quai_antique.* TO 'app_readonly'@'%';
FLUSH PRIVILEGES;


-- Table : user

CREATE TABLE IF NOT EXISTS user (
    user_id                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email                   VARCHAR(255)     NOT NULL UNIQUE,
    password                VARCHAR(255)     NOT NULL,
    prenom                  VARCHAR(100)     NOT NULL,
    nom                     VARCHAR(100)     NOT NULL,
    nombre_convives_defaut  TINYINT UNSIGNED NOT NULL DEFAULT 1,
    allergies               VARCHAR(255)     NULL DEFAULT NULL,
    role                    ENUM('admin', 'client') NOT NULL DEFAULT 'client',
    created_at              DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Table : restaurant_settings

CREATE TABLE IF NOT EXISTS restaurant_settings (
    settings_id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    heure_ouverture         TIME             NOT NULL DEFAULT '19:00:00',
    heure_fermeture         TIME             NOT NULL DEFAULT '21:00:00',
    max_convives            SMALLINT UNSIGNED NOT NULL DEFAULT 50
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Table : reservation

CREATE TABLE IF NOT EXISTS reservation (
    reservation_id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id                 INT UNSIGNED     NOT NULL,
    date                    DATE             NOT NULL,
    heure                   TIME             NOT NULL,
    nombre_convives         TINYINT UNSIGNED NOT NULL,
    allergies               VARCHAR(255)     NULL DEFAULT NULL,
    created_at              DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_reservation_user
        FOREIGN KEY (user_id) REFERENCES user(user_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Table : dish_category

CREATE TABLE IF NOT EXISTS dish_category (
    category_id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titre                   VARCHAR(100)     NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Table : dish

CREATE TABLE IF NOT EXISTS dish (
    dish_id                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id             INT UNSIGNED     NOT NULL,
    titre                   VARCHAR(150)     NOT NULL,
    description             TEXT             NOT NULL,
    prix                    INT UNSIGNED     NOT NULL,

    CONSTRAINT fk_dish_category
        FOREIGN KEY (category_id) REFERENCES dish_category(category_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Table : menu

CREATE TABLE IF NOT EXISTS menu (
    menu_id                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titre                   VARCHAR(150)     NOT NULL,
    nombre_sequences        TINYINT UNSIGNED NOT NULL DEFAULT 3,
    prix                    INT UNSIGNED     NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Table : menu_dish (liaison menu et plats)

CREATE TABLE IF NOT EXISTS menu_dish (
    menu_id                 INT UNSIGNED     NOT NULL,
    dish_id                 INT UNSIGNED     NOT NULL,
    PRIMARY KEY (menu_id, dish_id),

    CONSTRAINT fk_menu_dish_menu
        FOREIGN KEY (menu_id) REFERENCES menu(menu_id)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT fk_menu_dish_dish
        FOREIGN KEY (dish_id) REFERENCES dish(dish_id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Table : gallery

CREATE TABLE IF NOT EXISTS gallery (
    gallery_id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titre                   VARCHAR(150)     NOT NULL,
    photo                   VARCHAR(255)     NOT NULL,
    created_at              DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


SET FOREIGN_KEY_CHECKS = 1;
