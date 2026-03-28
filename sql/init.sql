-- Création de la table des utilisateurs (pour le login BO)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertion de l'utilisateur par défaut demandé 
-- Note : Le mot de passe 'admin123' est ici en clair pour l'exemple, 
-- mais utilise password_hash() en PHP pour la version finale.
INSERT INTO users (username, password) VALUES ('admin', 'admin123');

-- Création de la table des articles (Contenu du site) [cite: 6]
CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE, -- Pour l'URL rewriting [cite: 12]
    content TEXT NOT NULL,
    image_url VARCHAR(255),
    image_alt VARCHAR(255), -- Pour le point SEO [cite: 16]
    published_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);