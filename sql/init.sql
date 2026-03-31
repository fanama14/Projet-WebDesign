SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE DATABASE IF NOT EXISTS iran_news
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

ALTER DATABASE iran_news
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

USE iran_news;

DROP TABLE IF EXISTS article_images;
DROP TABLE IF EXISTS articles;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE articles (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  content TEXT NOT NULL,
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE article_images (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  article_id INT UNSIGNED NOT NULL,
  image_kind ENUM('main', 'thumb') NOT NULL DEFAULT 'main',
  image_path VARCHAR(255) NOT NULL,
  image_alt VARCHAR(255) DEFAULT NULL,
  sort_order TINYINT UNSIGNED NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_article_images_article
    FOREIGN KEY (article_id) REFERENCES articles(id)
    ON DELETE CASCADE,
  UNIQUE KEY uq_article_kind (article_id, image_kind),
  KEY idx_article_images_article_id (article_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (username, password_hash) VALUES
('admin', '$2y$10$2BlhLlBnrCkmLkutol2MS.PRbGqXuQKFZ6uR5npMxlE7CJMSnVIJa');

INSERT INTO articles (title, slug, content, is_published) VALUES
-- 1) Accord entre USA/Israël et pays alliés, Houthis
('Les Houthis entrent en guerre aux côtés de l''Iran', 'houthis-iran-guerre-2026',
 'Depuis le début du conflit le 28 février 2026, les rebelles Houthis du Yémen ont annoncé des frappes balistiques dirigées vers Israël, affirmant soutenir l''Iran face aux attaques américaines et israéliennes. Les tensions régionales se sont intensifiées, et la crise s''est étendue au-delà des frontières originelles du conflit. Ce développement alimente les discussions diplomatiques pour désamorcer l''escalade.', 1),

-- 2) Donald Trump (président en 2026)
('Donald Trump et la stratégie américaine dans la guerre contre l''Iran', 'donald-trump-strategie-guerre-iran-2026',
 'En 2026, le président américain Donald Trump dirige les opérations militaires conjointes avec Israël contre l''Iran. Dans plusieurs déclarations publiques, il affirme que l''objectif est de neutraliser les capacités stratégiques iraniennes et de protéger les intérêts occidentaux au Moyen-Orient. Sur le plan politique intérieur, sa gestion du conflit suscite des débats et critiques au Congrès.', 1),

-- 3) Bombardements sur l''Iran
('Nouvelle vague de bombardements sur les infrastructures iraniennes', 'bombardements-infrastructures-iran-2026',
 'Les forces aériennes américaines et israéliennes ont mené une série de raids ciblés sur des installations militaires et de production de missiles en Iran. Ces bombardements visent à réduire la capacité iranienne à riposter, tout en minimisant les pertes civiles. Malgré cela, les frappes ont causé des dégâts majeurs sur plusieurs bases stratégiques.', 1),

-- 4) Bombardements et attaques iraniennes
('Riposte iranienne : missiles et drones contre Israël et bases américaines', 'iran-missiles-drones-reponse-2026',
 'En réponse aux attaques, Téhéran a lancé des salves de missiles balistiques et des essaims de drones contre des cibles en Israël et plusieurs bases américaines dans la région du Golfe. Des alertes aériennes ont été déclenchées et certains systèmes de défense ont intercepté de nombreux projectiles, tandis que d''autres ont causé des dégâts matériels dans des zones civiles.', 1),

-- 5) Problèmes récents engendrés par cette guerre
('Conséquences économiques et sociales de la guerre 2026', 'consequences-economiques-sociales-guerre-iran-2026',
 'La guerre entre les États-Unis, Israël et l''Iran provoque une perturbation majeure du commerce mondial, notamment à cause de l''instabilité dans le détroit d''Ormuz, passage clé pour le pétrole. Les prix de l''énergie s''envolent, entraînant une inflation accrue et des difficultés économiques significatives pour les ménages et les entreprises dans plusieurs pays.', 1),

-- 6) Netanyahu et le front politique le plus récent
('Netanyahu, Trump et les derniers développements du conflit', 'netanyahu-trump-developpements-conflit-2026',
 'Le conflit s''étend maintenant au 30e jour, avec des frappes continues sur les infrastructures iraniennes et des réponses iraniennes par missiles. Le Premier ministre israélien Benjamin Netanyahu travaille en coordination avec le président Donald Trump pour maintenir la pression tout en gérant les réactions internationales. Les pourparlers diplomatiques restent tendus avec peu de signes de désescalade.', 1);

UPDATE articles
SET created_at = '2026-03-24 08:10:00', updated_at = '2026-03-31 09:25:00'
WHERE id = 1;

UPDATE articles
SET created_at = '2026-03-25 10:15:00', updated_at = '2026-03-25 12:05:00'
WHERE id = 2;

UPDATE articles
SET created_at = '2026-03-26 07:50:00', updated_at = '2026-03-26 08:40:00'
WHERE id = 3;

UPDATE articles
SET created_at = '2026-03-27 13:20:00', updated_at = '2026-03-27 15:10:00'
WHERE id = 4;

UPDATE articles
SET created_at = '2026-03-28 09:35:00', updated_at = '2026-03-28 11:00:00'
WHERE id = 5;

UPDATE articles
SET created_at = '2026-03-29 16:05:00', updated_at = '2026-03-29 18:20:00'
WHERE id = 6;

INSERT INTO article_images (article_id, image_kind, image_path, image_alt, sort_order) VALUES
(1, 'main', '/assets/images/article-1.webp', 'Houthis et missiles vers Israël', 1),
(1, 'thumb', '/assets/images/article-1-thumb.webp', 'Houthis et missiles vers Israël', 1),
(2, 'main', '/assets/images/article-2.webp', 'Donald Trump et la stratégie américaine', 1),
(2, 'thumb', '/assets/images/article-2-thumb.webp', 'Donald Trump et la stratégie américaine', 1),
(3, 'main', '/assets/images/article-3.webp', 'Bombardements sur les infrastructures iraniennes', 1),
(3, 'thumb', '/assets/images/article-3-thumb.webp', 'Bombardements sur les infrastructures iraniennes', 1),
(4, 'main', '/assets/images/article-4.webp', 'Riposte iranienne : missiles et drones', 1),
(4, 'thumb', '/assets/images/article-4-thumb.webp', 'Riposte iranienne : missiles et drones', 1),
(5, 'main', '/assets/images/article-5.webp', 'Conséquences économiques et sociales de la guerre 2026', 1),
(5, 'thumb', '/assets/images/article-5-thumb.webp', 'Conséquences économiques et sociales de la guerre 2026', 1),
(6, 'main', '/assets/images/article-6.webp', 'Netanyahu, Trump et les derniers développements du conflit', 1),
(6, 'thumb', '/assets/images/article-6-thumb.webp', 'Netanyahu, Trump et les derniers développements du conflit', 1);