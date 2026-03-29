# Document Technique - Personne A (Architecture & Logique)

## 1) Perimetre realise

- Modelisation et script SQL simple pour le projet.
- Connexion PHP <-> MySQL via PDO.
- Backoffice avec login + CRUD articles.
- Configuration Docker prete a l emploi (serveur + base).
- URL rewriting pour URLs normalisees.

## 2) Structure technique

- Front/Back: [src/index.php](src/index.php)
- Connexion DB: [src/db.php](src/db.php)
- Auth session: [src/auth.php](src/auth.php)
- Rewriting Apache: [src/.htaccess](src/.htaccess)
- Docker image app: [Dockerfile](Dockerfile)
- Orchestration: [docker-compose.yml](docker-compose.yml)
- Script SQL: [sql/init.sql](sql/init.sql)

## 3) Modelisation base de donnees (simple)

### Table users
- id (PK)
- username (UNIQUE)
- password_hash
- created_at

### Table articles
- id (PK)
- title
- slug (UNIQUE)
- content
- image_url
- image_alt
- is_published
- created_at
- updated_at

## 4) Login BO (identifiants par defaut)

- URL: /login (via rewriting) ou /index.php?page=login
- Username: admin
- Password: admin123

## 5) Docker et execution

- Demarrage: docker compose up --build -d
- Arret: docker compose down
- URL site: http://localhost:8080

Important: si la base existe deja, reimporter le SQL manuellement:
- Get-Content .\\sql\\init.sql | docker exec -i projet_db mysql -u root -proot_password

## 6) Exigences du sujet couvertes (partie A)

- URL normalisees: OK ([src/.htaccess](src/.htaccess))
- BO avec user/pass par defaut: OK
- Connexion data code <-> DB: OK (PDO)
- Conteneurisation: OK (app + mysql)
- Base simple et efficace: OK (2 tables)

## 7) Elements de livraison a completer

- Captures FO et BO: a inserer
- Num ETU: a renseigner
- Rapport Lighthouse mobile/desktop: a executer localement et joindre les scores