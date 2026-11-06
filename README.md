# MyBand

Site vitrine dynamique pour un groupe de rock fictif, réalisé dans le cadre des TP WEB1. Le projet rassemble les versions TP1 à TP5 dans une application PHP/MySQL unique : identité du groupe, répertoire musical, comptes à privilèges et paroles au format PDF.

## Objectifs pédagogiques

- Construire des pages web dynamiques avec PHP, HTML, CSS et JavaScript.
- Structurer une application avec des fichiers partagés et une base MySQL.
- Utiliser les sessions pour conserver l'identité du groupe et gérer les connexions.
- Afficher, rechercher, trier et administrer une setlist.
- Différencier les droits d'un visiteur, d'un membre et d'un administrateur.
- Protéger les formulaires, les données affichées et les fichiers téléversés.

## Fonctionnalités

- Nom du groupe et logo choisi au hasard dans `logos/`, générés une fois puis conservés pendant la session.
- Illustration de la page d'accueil : `rock.png`.
- Accueil et navigation communs à toutes les pages.
- Setlist issue de MySQL, avec tri serveur par titre, artiste ou style et filtre de recherche côté navigateur.
- Gestion de la setlist par un administrateur : ajout, modification et suppression de titres.
- Téléchargement de paroles PDF par les membres connectés ; ajout et suppression réservés aux administrateurs.
- Formulaire de contact validé côté serveur et envoyé à l'adresse d'un membre marqué comme contact dans la base.
- Interface responsive dans un thème rock rouge, noir et gris.

## Technologies et outils

| Domaine | Technologies |
| --- | --- |
| Front-end | HTML5, CSS3, JavaScript |
| Back-end | PHP 7.4+ |
| Base de données | MySQL 5.7+ / MariaDB, PDO |
| Environnement local | WAMP (Apache, PHP, MySQL), phpMyAdmin |
| Développement et vérification | Visual Studio Code, navigateur et outils de développement |
| Gestion de versions | Git |

## Organisation des fichiers

- `index.php` : accueil et présentation du groupe.
- `header.php`, `footer.php` : structure partagée, navigation et connexion.
- `setlist.php` : lecture, recherche, tri et gestion du répertoire.
- `contact.php` : coordonnées sélectionnées depuis la base.
- `dbconnect.php` : connexion PDO à MySQL.
- `band_generators.php`, `logo.php` : génération de l'identité du groupe et affichage contrôlé du logo.
- `download_lyrics.php`, `lyrics/` : téléchargement protégé des paroles.
- `rock.png`, `logos/` : illustration de l'accueil et logos sélectionnés aléatoirement.
- `myband.sql` : création de la base, des tables et insertion des morceaux d'exemple.

## Installation locale

1. Placer le dossier `myband` dans le répertoire web de WAMP, par exemple `C:\wamp64\www\myband`.
2. Démarrer Apache et MySQL, puis importer `myband.sql` avec phpMyAdmin dans une base neuve. Le script crée la base `myband`, ses deux tables, les comptes de démonstration et les morceaux d'exemple. Si tu as déjà une ancienne base `myband`, ne réimporte pas ce script par-dessus ; sauvegarde tes données et migre d'abord son schéma.
3. Vérifier la connexion dans `dbconnect.php`. Par défaut, le projet utilise `localhost`, la base `myband`, l'utilisateur local `root` et un mot de passe vide, configuration courante en développement WAMP. Pour un autre environnement, définir `MYBAND_DB_HOST`, `MYBAND_DB_NAME`, `MYBAND_DB_USER` et `MYBAND_DB_PASSWORD`.
4. Créer au moins un compte membre et un compte administrateur comme décrit ci-dessous.
5. Ouvrir `http://localhost/myband/`.

Apache doit autoriser les fichiers `.htaccess` (`AllowOverride`) pour empêcher l'accès direct aux répertoires `logos/` et `lyrics/`. PHP doit aussi pouvoir écrire les PDF dans `lyrics/`.
L'envoi du formulaire de contact utilise `mail()` : configurez un serveur SMTP ou un relais d'e-mail dans PHP/WAMP pour transmettre les messages. Si cet envoi n'est pas configuré, la page signale l'échec et affiche l'adresse de contact configurée.

### Créer un compte

Le script SQL insère les trois comptes de démonstration demandés pour le TP :

| Identifiant | Mot de passe du TP | Rôle |
| --- | --- | --- |
| `admin` | `SuperPassword` | Administrateur et contact |
| `Van.Halen` | `jump` | Membre |
| `Freddy.Mercury` | `queen` | Membre |

Ces comptes sont destinés à la démonstration locale. Pour un vrai déploiement, remplacez ces identifiants par des mots de passe personnels. Le champ `contact = 1` sélectionne l'adresse affichée sur la page Contact.

## Schéma MySQL final

La base est nommée `myband` et contient les tables suivantes.

```sql
CREATE TABLE members (
    login VARCHAR(100) NOT NULL PRIMARY KEY,
    password CHAR(64) NOT NULL,
    salt CHAR(64) NOT NULL,
    email VARCHAR(255) DEFAULT NULL,
    contact TINYINT(1) NOT NULL DEFAULT 0,
    admin TINYINT(1) NOT NULL DEFAULT 0
);

CREATE TABLE setlist (
    id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    artist VARCHAR(255) NOT NULL,
    style VARCHAR(255) NOT NULL,
    lyrics VARCHAR(255) DEFAULT NULL
);
```

Pour correspondre à l'exercice, chaque compte possède un sel aléatoire unique de 32 octets représenté en hexadécimal. Le hash stocké est `SHA256(sel_hexadécimal + mot_de_passe)` en hexadécimal. Les deux valeurs font 64 caractères. Le sel n'est pas un mot de passe : il est stocké à côté du hash et concaténé avant le mot de passe lors du calcul. Le site vérifie les identifiants en recalculant ce SHA-256 et en comparant les résultats. SHA-256 simple est utilisé pour répondre au TP ; pour une application réelle, préférer une fonction spécialisée pour les mots de passe telle que `password_hash()`.

### `members`

| Colonne | Type | Rôle |
| --- | --- | --- |
| `login` | `VARCHAR(100)` — clé primaire | Identifiant unique |
| `password` | `CHAR(64)` | Empreinte SHA-256 salée du mot de passe |
| `salt` | `CHAR(64)` | Sel aléatoire unique, encodé en hexadécimal |
| `email` | `VARCHAR(255)`, nullable | Adresse de contact |
| `contact` | `TINYINT(1)`, défaut `0` | Indique l'adresse affichée sur la page Contact |
| `admin` | `TINYINT(1)`, défaut `0` | `0` pour membre, `1` pour administrateur |

### `setlist`

| Colonne | Type | Rôle |
| --- | --- | --- |
| `id` | `BIGINT`, clé primaire auto-incrémentée | Identifiant du morceau |
| `title` | `VARCHAR(255)` | Titre |
| `artist` | `VARCHAR(255)` | Artiste ou groupe |
| `style` | `VARCHAR(255)` | Style ou catégorie |
| `lyrics` | `VARCHAR(255)`, nullable | Nom du PDF des paroles stocké dans `lyrics/` |

## Méthodes mises en œuvre

- Les opérations SQL utilisent PDO et des requêtes préparées ; les colonnes de tri sont limitées à une liste autorisée.
- Les nouveaux comptes suivent le sujet : empreinte SHA-256 avec un sel aléatoire propre à chaque membre, stocké dans une colonne séparée. Le code conserve aussi la compatibilité avec les anciens comptes sans sel des projets TP précédents.
- Les contenus issus de la base sont échappés à l'affichage HTML ; les textes transmis au JavaScript sont encodés avant insertion dans les attributs.
- Les fichiers PDF sont contrôlés, renommés avec un nom généré par le serveur et servis uniquement aux membres connectés.
- Les accès directs aux dossiers de ressources privées sont bloqués par `.htaccess`.
- Le filtre de recherche des morceaux fonctionne côté client, sans rechargement de la page.
- La feuille de style utilise une palette cohérente, une mise en page flexible et des adaptations pour les petits écrans.

## Pistes d'évolution

- Ajouter des tests automatisés et une configuration de déploiement.
- Externaliser la configuration de la base et désactiver les erreurs détaillées en production.
- Mettre en place HTTPS sur un serveur de déploiement.
