# Projet WebDesign - Guide de demarrage et verification

Ce guide explique les commandes utiles pour:
- demarrer le projet
- verifier que tout fonctionne
- reimporter la base proprement (UTF-8)
- se connecter a MySQL dans Docker
- verifier les tables dans Docker Desktop

## 1) Prerequis

- Docker Desktop installe et lance
- Docker Compose disponible
- Port 8080 libre (ou changer le port, voir section 9)

## 2) Demarrage rapide

Depuis la racine du projet:

1. Construire et demarrer les conteneurs

   docker compose up --build -d

2. Verifier que les conteneurs tournent

   docker compose ps

3. Ouvrir le site

   http://localhost:8080/

4. Ouvrir le backoffice

   http://localhost:8080/admin

5. Connexion backoffice

   Username: admin
   Password: admin123

## 3) Commandes de verification

1. Voir les logs web

   docker compose logs -f app

2. Voir les logs base

   docker compose logs -f db

3. Tester HTTP depuis PowerShell

   Invoke-WebRequest -Uri "http://localhost:8080/guerre-iran-accueil.html" -UseBasicParsing

## 4) Import SQL propre (important pour les caracteres)

Remarque: docker compose up execute init.sql automatiquement seulement si la base est vide.

Si la base existe deja, ou si tu veux reinitialiser les donnees, execute l import manuel UTF-8:

docker compose exec -T db sh -c "mysql --default-character-set=utf8mb4 -uroot -proot_password < /docker-entrypoint-initdb.d/init.sql"

Verification des accents:

docker compose exec -T db mysql --default-character-set=utf8mb4 -uuser_projet -ppassword_projet -D iran_news -e "SELECT id, title FROM articles ORDER BY id;"

## 5) Connexion MySQL dans Docker (ligne de commande)

Entrer dans MySQL:

docker compose exec db mysql -uuser_projet -ppassword_projet -D iran_news

Commandes SQL utiles:

SHOW TABLES;
DESCRIBE articles;
DESCRIBE article_images;
SELECT id, title FROM articles ORDER BY id;
SELECT article_id, image_kind, image_path FROM article_images ORDER BY article_id, image_kind;

Sortie MySQL:

exit

## 6) Voir les tables dans Docker Desktop

1. Ouvrir Docker Desktop
2. Aller dans Containers
3. Ouvrir le conteneur projet_db
4. Ouvrir l onglet Exec (ou Terminal)
5. Lancer:

   mysql -uuser_projet -ppassword_projet -D iran_news

6. Lancer ensuite:

   SHOW TABLES;
   SELECT id, title FROM articles;

## 7) Arreter et nettoyer

Arreter les conteneurs:

docker compose down

Arreter + supprimer le volume de base (reset complet):

docker compose down -v

Puis relancer:

docker compose up --build -d

## 8) Reimport apres reset complet

Apres un reset complet, tu peux forcer un reimport explicite:

docker compose exec -T db sh -c "mysql --default-character-set=utf8mb4 -uroot -proot_password < /docker-entrypoint-initdb.d/init.sql"

## 9) Changer le port HTTP si 8080 est occupe

PowerShell:

$env:APP_PORT=8081
docker compose up -d

Acces site:

http://localhost:8081/guerre-iran-accueil.html

## 10) Probleme courant et solution

Probleme: caracteres casses (ex: c??t??s)

Cause probable:
- import SQL via pipe PowerShell qui convertit le fichier

Bonne pratique:
- ne pas utiliser Get-Content ... | mysql
- utiliser l import fichier dans le conteneur avec --default-character-set=utf8mb4

Commande correcte:

docker compose exec -T db sh -c "mysql --default-character-set=utf8mb4 -uroot -proot_password < /docker-entrypoint-initdb.d/init.sql"
