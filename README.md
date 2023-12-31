# TMTC

TMTC est un cms, permettant de gérer un system de réservation de logement.

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
   Veillez à bien configurer le clé APP_URL

7. **Migration de la base de données :** Exécutez les migrations pour créer les tables.
   ```bash
   php chef migrate
   ```

8. **Création du lien symbolique pour le stockage :** Exécutez la commande artisan pour créer le lien symbolique vers le dossier de stockage public.
   ```bash
   php chef storage:link
   ```

9. **Optionnel - Ajout de données de test :** Si nécessaire, vous pouvez ajouter des données de test à la base de données.
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

## Author
- ### [Mazbaz](https://github.com/MazBazDev)
