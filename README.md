# TMTC

TMTC est un CMS permettant de gérer un système de réservation de logement.

## Description

Ce projet répond aux exigences d'un énoncé visant à fournir un CMS de gestion locative utilisant le modèle MVC, le moteur de template Twig, l'utilisation de Faker et tout le reste doit être fait from scratch. Le front-end n'était pas évalué.

Les prérequis étaient les suivants :

- Le projet devra être réalisé de façon individuelle.
- Utilisation du langage PHP de façon native (pas de framework ni d’API pour traiter les données directes de l’application)
- L’application doit être architecturée de sorte à respecter le design pattern MVC
- Les données de l’application doivent être pertinentes et cohérentes avec le contexte. Celles-ci doivent être stockées et exploitées depuis une base de données MySQL respectant les normes relationnelles.
- Les URL doivent être soignées. Ainsi /admin.php ne sera pas admis.

La mission consiste à créer une application pour une agence de location (Troc mon Toit) souhaitant numériser ses services et mettre à disposition différents logements afin d’en tirer des revenus. Cette application doit permettre un affichage de l’ensemble des logements disponibles, filtrables par ville, prix à la nuit, type de logement, équipements disponibles et services disponibles. Les utilisateurs connectés peuvent ajouter des logements en favoris et réserver des logements disponibles.

## Fonctionnalités principales

- Affichage des logements disponibles avec filtrage
- Ajout de logements en favoris pour les utilisateurs connectés
- Réservation de logements avec vérification de disponibilité et calcul du prix total
- Système de notation des logements après la fin de la réservation
- Panel administratif avec CRUD sur les types de logement, équipements, services, utilisateurs, logements et avis

## Présentation de CHEF

Le projet est basé sur un mini-framework MVC (Chef) créé from scratch, fortement inspiré du fonctionnement et de la logique de [Laravel](https://laravel.com) ❤️.

Chef comprend les éléments suivants :

- Routeur dynamique avec différents types de routes (GET, POST, PUT, PATCH, DELETE) et possibilité de nommer les routes et de les regrouper.
- Validation et nettoyage des données avec des règles de validation personnalisables.
- Système de migration similaire à Laravel permettant les modifications incrémentales de la base de données.
- Mini ORM pour interagir avec la base de données SQL, y compris la gestion des relations.
- Système de seedage de la base de données utilisant Faker.
- Système de Middlewares.
- Moteur de template Twig.
- Système de sessions et de messages flash.
- CLI avec les fonctionnalités suivantes :
  - help : affiche le menu d'aide
  - make:migration : crée une nouvelle migration
  - make:seeder : crée un nouveau seeder pour un modèle donné
  - make:admin : crée un utilisateur avec des permissions administrateur pour accéder au back-office
  - migrate : applique les migrations à la base de données
  - seed : applique les seeders à la base de données
  - storage:link : crée un lien symbolique entre le dossier de stockage et la partie publique du site pour rendre les assets accessibles

## Installation

1. **Téléchargement du code source :** Clônez le dépôt avec la commande suivante :
```bash
git clone https://github.com/MazBazDev/Project-TMTC.git
```

2. **Accès au répertoire :** Changez de répertoire pour accéder au dossier du projet.
```bash 
cd Project-TMTC 
```

3. **Lancement des conteneurs Docker :** Lancez les conteneurs avec Docker Compose.
```bash
docker-compose up -d
```

4. **Connexion au terminal du conteneur :** Accédez au terminal du conteneur.
```bash
docker exec -it tmtc-web bash
```

5. **Installation des dépendances PHP :** Exécutez la commande Composer pour installer les dépendances.
```bash
composer install
```

6. **Copie et configuration du fichier .env :** Remplissez les informations de la base de données et configurez l'URL de l'application.
```bash
cp .env.exemple .env
nano .env
```
Veillez à bien configurer la clé APP_URL.

7. **Migration de la base de données :** Exécutez les migrations pour créer les tables.
```bash
php chef migrate
```

8. **Création du lien symbolique pour le stockage :** Exécutez la commande pour créer le lien symbolique vers le dossier de stockage public.
```bash
php chef storage:link
```

9. **Optionnel - Ajout de données de test :** Ajoutez des données de test à la base de données si nécessaire.
```bash
php chef seed
```

10. **Création de l'utilisateur admin :** Utilisez la commande artisan pour créer un utilisateur admin en répondant aux questions.
```bash
php chef make:admin
```

## Utilisation

1. Accédez au site en utilisant votre compte admin.
2. À partir du panneau d'administration, créez les éléments nécessaires à l'utilisation du site.
3. Profitez !

## Auteur
- [Mazbaz](https://github.com/MazBazDev)
