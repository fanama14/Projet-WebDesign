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
- is_published
- created_at
- updated_at

### Table article_images
- id (PK)
- article_id (FK -> articles.id)
- image_kind (main | thumb)
- image_path
- image_alt
- sort_order
- created_at

## 4) Login BO (identifiants par defaut)

- URL: /login (via rewriting) ou /index.php?page=login
- Username: admin
- Password: admin123

## 5) Docker et execution

- Demarrage: docker compose up --build -d
- Arret: docker compose down
- URL site: http://localhost:8080

Important: pour eviter tout probleme de caracteres (accents), ne pas utiliser le pipe PowerShell `Get-Content ... | mysql`.

Procedure recommandee pour reimporter le SQL en UTF-8 (Docker):

1. Demarrer les conteneurs:
- docker compose up -d

2. Reimporter le SQL depuis le fichier monte dans le conteneur (sans conversion PowerShell):
- docker compose exec -T db sh -c "mysql --default-character-set=utf8mb4 -uroot -proot_password < /docker-entrypoint-initdb.d/init.sql"

3. Verifier que les accents sont bien lus cote client MySQL:
- docker compose exec -T db mysql --default-character-set=utf8mb4 -uuser_projet -ppassword_projet -D iran_news -e "SELECT id, title FROM articles ORDER BY id;"

4. Si des caracteres sont encore corrompus, recreer les conteneurs + volume DB puis relancer l import:
- docker compose down -v
- docker compose up --build -d
- docker compose exec -T db sh -c "mysql --default-character-set=utf8mb4 -uroot -proot_password < /docker-entrypoint-initdb.d/init.sql"

## 6) Exigences du sujet couvertes (partie A)

- URL normalisees: OK ([src/.htaccess](src/.htaccess))
- BO avec user/pass par defaut: OK
- Connexion data code <-> DB: OK (PDO)
- Conteneurisation: OK (app + mysql)
- Base simple et efficace: OK (3 tables: users, articles, article_images)

## 7) Elements de livraison a completer

- Captures FO et BO: a inserer
- Num ETU: a renseigner
- Rapport Lighthouse mobile/desktop: a executer localement et joindre les scores