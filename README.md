# Plateforme de Gestion de Blog

Une application web moderne construite avec **Symfony 7.4** pour la gestion d'un blog complet avec système d'authentification, gestion des articles, catégories et commentaires.

## 📋 Table des matières

- [Aperçu](#aperçu)
- [Fonctionnalités](#fonctionnalités)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Configuration](#configuration)
- [Utilisation](#utilisation)
- [Architecture](#architecture)
- [Base de données](#base-de-données)
- [Tests](#tests)
- [Contribution](#contribution)

## 🎯 Aperçu

Cette application permet de créer, gérer et publier des articles de blog avec un système de catégorisation, de commentaires et d'administration utilisateur. Elle fournit une interface complète pour les utilisateurs et un panneau d'administration pour les modérateurs.

## ✨ Fonctionnalités

### Authentification & Utilisateurs
- Inscription et connexion sécurisées
- Gestion des rôles (utilisateur, administrateur)
- Profils utilisateur
- Gestion administrative des utilisateurs

### Articles (Posts)
- Création, édition et suppression d'articles
- Publication avec date
- Images d'illustration
- Affichage avec pagination
- Association aux catégories

### Catégories
- Création et gestion des catégories
- Organisation des articles par catégorie
- Affichage des articles par catégorie

### Commentaires
- Système de commentaires sur les articles
- Modération des commentaires (admin)
- Affichage chronologique des commentaires

### Interface Admin
- Tableau de bord administrateur
- Gestion des utilisateurs
- Gestion des articles
- Gestion des catégories
- Modération des commentaires

## 📦 Prérequis

- **PHP** 8.2 ou supérieur
- **Composer** 2.0+
- **PostgreSQL** 16+ (ou Docker)
- **Node.js** (pour les assets)
- **Git**

## 🚀 Installation

### 1. Cloner le repository

```bash
git clone <repository-url>
cd tp_cours
```

### 2. Installer les dépendances

```bash
composer install
```

### 3. Configurer l'environnement

Créer un fichier `.env.local` à partir du fichier `.env` :

```bash
cp .env .env.local
```

Configurer les variables d'environnement (notamment la connexion à la base de données) :

```ini
DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=16&charset=utf8"
```

### 4. Créer la base de données

```bash
symfony console doctrine:database:create
```

### 5. Exécuter les migrations

```bash
symfony console doctrine:migrations:migrate
```

### 6. Charger les fixtures (données de test)

```bash
symfony console app:load-fixtures
```

### 7. Lancer le serveur de développement

```bash
symfony serve
```

L'application est accessible sur `http://localhost:8000`

## 🐳 Installation avec Docker

### Utiliser Docker Compose

```bash
docker-compose up -d
```

### Initialiser la base de données

```bash
docker-compose exec php php bin/console doctrine:database:create
docker-compose exec php php bin/console doctrine:migrations:migrate
docker-compose exec php php bin/console app:load-fixtures
```

## ⚙️ Configuration

### Variables d'environnement importantes

| Variable | Description | Par défaut |
|----------|-------------|-----------|
| `APP_ENV` | Environnement (dev/prod) | dev |
| `APP_SECRET` | Clé secrète de l'application | - |
| `DATABASE_URL` | URL de connexion PostgreSQL | - |
| `POSTGRES_DB` | Nom de la base de données | app |
| `POSTGRES_USER` | Utilisateur PostgreSQL | app |
| `POSTGRES_PASSWORD` | Mot de passe PostgreSQL | !ChangeMe! |

### Configuration Symfony

- Base de données: [config/packages/doctrine.yaml](config/packages/doctrine.yaml)
- Sécurité: [config/packages/security.yaml](config/packages/security.yaml)
- Routage: [config/routes.yaml](config/routes.yaml)
- Services: [config/services.yaml](config/services.yaml)

## 💡 Utilisation

### Routes principales

**Publiques:**
- `/` - Page d'accueil
- `/posts` - Liste des articles
- `/posts/{id}` - Détail d'un article
- `/category/{id}` - Articles d'une catégorie
- `/register` - Inscription
- `/login` - Connexion

**Authentifiées:**
- `/admin/users` - Gestion des utilisateurs
- `/admin/posts` - Gestion des articles
- `/admin/categories` - Gestion des catégories
- `/admin/comments` - Modération des commentaires

## 🏗️ Architecture

### Structure du projet

```
src/
├── Controller/          # Contrôleurs
│   ├── AdminUserController.php
│   ├── AuthController.php
│   ├── CategoryController.php
│   ├── CommentController.php
│   ├── HomeController.php
│   └── PostController.php
├── Entity/             # Entités Doctrine
│   ├── Category.php
│   ├── Comment.php
│   ├── Post.php
│   └── User.php
├── Form/               # Classes de formulaires
│   ├── CategoryType.php
│   ├── CommentType.php
│   ├── PostType.php
│   ├── RegistrationFormType.php
│   └── UserType.php
├── Repository/         # Repositories (requêtes BD)
│   ├── CategoryRepository.php
│   ├── CommentRepository.php
│   ├── PostRepository.php
│   └── UserRepository.php
├── DataFixtures/       # Données de test
│   └── AppFixtures.php
├── Command/            # Commandes console
│   └── LoadFixturesCommand.php
└── Kernel.php         # Configuration du noyau
```

### Entités principales

#### User
- Gestion de l'authentification
- Rôles (ROLE_USER, ROLE_ADMIN)
- Relations avec les articles et commentaires

#### Post
- Titre, contenu, image
- Date de publication
- Relation avec Category et User
- Commentaires associés

#### Category
- Nom et description
- Organisation des articles

#### Comment
- Contenu, date de création
- Relation avec Post et User
- Statut e modération

## 📊 Base de données

### Schéma de données

```
┌─────────┐         ┌──────────┐         ┌──────────────┐
│  User   │         │ Category │         │    Post      │
├─────────┤         ├──────────┤         ├──────────────┤
│ id (PK) │         │ id (PK)  │         │ id (PK)      │
│ email   │         │ name     │         │ title        │
│ password│         │ slug     │◄─────┐  │ content      │
│ roles   │         └──────────┘      │  │ picture      │
│ name    │         (0..n)             │  │ publishedAt  │
└─────────┘                            │  │ categoryId   │
    ▲                                  │  │ userId       │
    │                               (1..1)└──────────────┘
    │ (1..n)                           │      ▲
    │                                  │      │ (1..n)
    └──────┐                           └──────┤
           │                                  │
        ┌──────────┐                    ┌─────────────┐
        │ Comment  │                    │   Like      │
        ├──────────┤                    ├─────────────┤
        │ id (PK)  │                    │ id (PK)     │
        │ content  │                    │ userId      │
        │ createdAt│                    │ postId      │
        │ userId   │                    └─────────────┘
        │ postId   │
        └──────────┘
```

### Migrations

Les migrations sont gérées dans le dossier [migrations/](migrations/). Pour générer une nouvelle migration après modification des entités :

```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

## 🧪 Tests

### Exécuter les tests unitaires

```bash
php bin/phpunit
```

### Configuration des tests

La configuration est disponible dans [phpunit.dist.xml](phpunit.dist.xml)

Environment de test défini dans [.env.test](.env.test)

## 🛠️ Commandes utiles

```bash
# Créer une nouvelle entité
php bin/console make:entity

# Créer un formulaire
php bin/console make:form

# Créer un contrôleur
php bin/console make:controller

# Afficher les routes
php bin/console debug:router

# Vider le cache
php bin/console cache:clear

# Afficher les services disponibles
php bin/console debug:container

# Charger les fixtures
php bin/console app:load-fixtures
```

## 📝 Fixtures

L'application inclut des fixtures pour initialiser la base de données avec des données de test. Les fixtures créent :

- 5 utilisateurs (1 admin, 4 utilisateurs classiques)
- 10 articles avec images
- 5 catégories
- 20 commentaires

Pour charger les fixtures :

```bash
php bin/console app:load-fixtures
```

## 🔒 Sécurité

- Authentification par formulaire avec hash bcrypt
- Protection CSRF activée
- Validation des formulaires côté serveur
- Gestion des rôles et permissions
- Base de données sécurisée par mot de passe

## 🌐 Dépendances principales

- **Symfony 7.4** - Framework web PHP
- **Doctrine ORM** - ORM pour la gestion de données
- **PostgreSQL** - Base de données
- **Twig** - Moteur de templates
- **Stimulus** - Framework JavaScript léger
- **Turbo** - Navigation rapide côté client

## 📄 Licence

Propriétaire - Tous droits réservés

## 👤 Auteur

Projet de cours Symfony

---

**Dernière mise à jour :** Février 2026
