# SAGLOTIS CRM - Application Web

Cette application est un système de gestion de la relation client (CRM) pour SAGLOTIS SARL, développée en HTML, CSS, JavaScript et PHP.

## Fonctionnalités

- Authentification utilisateur
- Gestion des clients
- Suivi des interactions (appels, messages)
- Planification
- Gestion des utilisateurs (pour administrateurs)

## Structure des fichiers

- `index.html` : Page principale avec interface utilisateur
- `api.php` : Backend PHP pour les API REST
- `assets/` : Dossier pour les ressources statiques (images, etc.)

## Configuration locale

### Prérequis

- PHP 7.4 ou supérieur
- Serveur web (Apache/Nginx) ou utiliser le serveur intégré PHP

### Installation

1. Clonez ou téléchargez les fichiers dans un dossier.
2. Assurez-vous que PHP est installé.
3. Lancez le serveur :
   ```
   php -S localhost:8000
   ```
4. Ouvrez http://localhost:8000 dans votre navigateur.

### Comptes de test

- Admin : admin@saglotis.com / password
- Utilisateur : user@saglotis.com / password
- Lecteur : lecteur@saglotis.com / password

## Hébergement en ligne

Pour héberger cette application web en ligne, suivez ces étapes :

### Option 1 : Hébergement gratuit avec 000webhost

1. **Inscrivez-vous** sur [000webhost](https://www.000webhost.com/).
2. **Créez un nouveau site** :
   - Allez dans "Website Builder" > "Create Website".
   - Choisissez un nom de domaine gratuit (ex: monsite.000webhostapp.com).
3. **Téléchargez les fichiers** :
   - Utilisez le File Manager de 000webhost.
   - Téléchargez `index.html`, `api.php` et le dossier `assets/`.
4. **Configuration PHP** :
   - 000webhost supporte PHP par défaut.
   - Si vous voulez utiliser une base de données MySQL, créez une base via le panneau de contrôle.
5. **Modifier api.php** :
   - Remplacez les informations de connexion DB par celles fournies par 000webhost.
   - Ou gardez les données mockées pour un test rapide.

### Option 2 : Hébergement avec Heroku (gratuit avec limites)

1. **Inscrivez-vous** sur [Heroku](https://www.heroku.com/).
2. **Installez Heroku CLI** et Git.
3. **Créez une app** :
   ```
   heroku create mon-crm-saglotis
   ```
4. **Ajoutez un Procfile** (fichier sans extension) :
   ```
   web: vendor/bin/heroku-php-apache2
   ```
5. **Déployez** :
   ```
   git init
   git add .
   git commit -m "Initial commit"
   git push heroku master
   ```
6. **Base de données** : Ajoutez ClearDB MySQL addon pour une DB gratuite.

### Option 3 : Autres hébergements

- **InfinityFree** : Similaire à 000webhost.
- **Vercel** : Pour statique, mais PHP nécessite un adaptateur.
- **Netlify** : Idem.

## Base de données (optionnel)

Si vous voulez utiliser une vraie base de données :

1. Créez une base MySQL.
2. Importez le schéma SQL suivant :

```sql
CREATE DATABASE saglotis_crm;

USE saglotis_crm;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    motDePasse VARCHAR(255) NOT NULL,
    role ENUM('administrateur', 'utilisateur', 'lecteur') NOT NULL
);

CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    telephone VARCHAR(20),
    email VARCHAR(255),
    type ENUM('Particulier', 'Entreprise', 'Association'),
    statut ENUM('Prospect', 'Suivi', 'Actif', 'Fidèle', 'Inactif'),
    dateCreation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE interactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('call', 'message', 'visite', 'changement') NOT NULL,
    clientId INT,
    telephone VARCHAR(20),
    duree VARCHAR(50),
    commentaire TEXT,
    destinataire VARCHAR(255),
    sujet VARCHAR(255),
    message TEXT,
    date DATE NOT NULL,
    FOREIGN KEY (clientId) REFERENCES clients(id)
);

-- Si la base existe déjà, exécutez ceci pour ajouter les types manquants :
-- ALTER TABLE interactions MODIFY type ENUM('call', 'message', 'visite', 'changement') NOT NULL;

CREATE TABLE planifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    date DATE NOT NULL,
    heure TIME
);

-- Insérer un utilisateur admin
INSERT INTO users (nom, email, motDePasse, role) VALUES ('Admin', 'admin@saglotis.com', 'password', 'administrateur');
```

3. Modifiez les informations de connexion dans `api.php`.

## Sécurité

- En production, utilisez HTTPS.
- Hash les mots de passe avec `password_hash()`.
- Validez toutes les entrées utilisateur.
- Utilisez des tokens JWT pour l'authentification persistante si nécessaire.

## Support

Pour toute question, contactez le développeur.