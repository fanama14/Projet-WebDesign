CREATE DATABASE IF NOT EXISTS iran_news
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE iran_news;

DROP TABLE IF EXISTS articles;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE articles (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  content TEXT NOT NULL,
  image_url VARCHAR(255) DEFAULT NULL,
  image_alt VARCHAR(255) DEFAULT NULL,
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO users (username, password_hash) VALUES
('admin', '$2y$10$2BlhLlBnrCkmLkutol2MS.PRbGqXuQKFZ6uR5npMxlE7CJMSnVIJa');

INSERT INTO articles (title, slug, content, image_url, image_alt, is_published) VALUES
('Conflit en Iran: point de situation', 'guerre', 'Article de demonstration pour valider le front-office et le back-office.', NULL, NULL, 1),
('Analyse regionale', 'analyse-regionale', 'Deuxieme article de test pour les operations CRUD.', NULL, NULL, 1);