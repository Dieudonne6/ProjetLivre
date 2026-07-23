# 📚 Book Marketplace API

API REST développée avec **Laravel 10**, permettant à des utilisateurs d'acheter et de vendre des livres numériques. Le projet simule une place de marché où les vendeurs mettent en ligne des livres et où les acheteurs les parcourent, les ajoutent à un panier, puis les achètent grâce à un solde crédité sur leur compte.

L'API est documentée avec **Swagger / OpenAPI**, conteneurisée avec **Docker**, et déployée en production avec **PostgreSQL**.

---

## 🌐 Démo en ligne

| Ressource | Lien |
|---|---|
| API (Base URL) | https://projetlivre-3.onrender.com |
| Documentation Swagger | https://projetlivre-3.onrender.com/api/documentation |

---

## 🎯 Objectif du projet

Ce projet a été réalisé dans le cadre d'un portfolio backend afin de mettre en pratique :

- la conception d'une API REST ;
- l'authentification par token avec **Laravel Sanctum** ;
- la modélisation d'une base de données relationnelle et la gestion de transactions ;
- la documentation d'API avec Swagger (L5-Swagger) ;
- la conteneurisation avec Docker ;
- une architecture propre basée sur les contrôleurs, requests et models Eloquent.

---

## 🏗 Architecture

```
Client (front / Swagger UI)
        │
        ▼
   Laravel API (Sanctum)
        │
        ▼
  Base de données (MySQL en local / PostgreSQL en production)
```

---

## 🛠 Stack technique

| Technologie | Rôle |
|---|---|
| Laravel 10 (PHP 8.1+) | Framework backend |
| MySQL | Base de données en développement (via Docker) |
| PostgreSQL | Base de données en production (Render) |
| Laravel Sanctum | Authentification par token (Bearer) |
| L5-Swagger (OpenAPI) | Documentation interactive de l'API |
| Eloquent ORM | Accès et relations en base de données |
| Docker / Docker Compose | Conteneurisation (app, nginx, mysql, phpMyAdmin) |

---

## 📦 Fonctionnalités principales

### 👤 Authentification
- Inscription (vendeur ou acheteur, avec photo de profil)
- Connexion / déconnexion
- Modification du mot de passe

### 📚 Livres
- Publication d'un livre (vendeur)
- Liste de tous les livres disponibles
- Filtrage des livres par catégorie
- Liste des catégories

### 🛒 Panier
- Ajout d'un livre au panier
- Consultation du panier
- Suppression d'un livre du panier

### 💳 Paiement
- Rechargement du solde du compte
- Validation du paiement et création de la commande
- Transfert automatique du montant entre l'acheteur et le(s) vendeur(s)

### 💬 Messages / Posts
- Liste des messages
- Liste et création de posts

---

## 🔗 Endpoints principaux

**Publics**

| Méthode | Endpoint | Description |
|---|---|---|
| POST | `/api/register` | Créer un compte |
| POST | `/api/login` | Se connecter et récupérer un token |
| GET | `/api/categories` | Liste des catégories |
| GET | `/api/listelivre` | Liste des livres |
| GET | `/api/listemessage` | Liste des messages |
| GET | `/api/posts` | Liste des posts |
| POST | `/api/posts/create` | Créer un post |

**Protégés (Sanctum — nécessitent un Bearer token)**

| Méthode | Endpoint | Description |
|---|---|---|
| GET | `/api/user` | Utilisateur connecté |
| PUT | `/api/modifpassword` | Modifier le mot de passe |
| POST | `/api/createlivre` | Publier un livre |
| POST | `/api/logout` | Se déconnecter |
| POST | `/api/addcart/{id}` | Ajouter un livre au panier |
| GET | `/api/cart` | Voir le panier |
| DELETE | `/api/deletelivrecart/{id}` | Retirer un livre du panier |
| GET | `/api/paiement` | Informations de paiement |
| POST | `/api/validatepaiement` | Valider le paiement / la commande |
| GET | `/api/livrecategorie/{categorie}` | Livres d'une catégorie |
| PUT | `/api/rechargesolde` | Recharger le solde du compte |

La liste complète des routes, payloads et réponses est disponible dans la documentation Swagger.

---

## 🔐 Authentification

L'API utilise l'authentification par **Bearer Token** via Laravel Sanctum.

Après connexion, transmettre le token dans l'en-tête de chaque requête protégée :

```
Authorization: Bearer VOTRE_TOKEN
```

---

## 🧱 Modèle de données (principales tables)

| Table | Description |
|---|---|
| `users` | Utilisateurs (vendeurs ou acheteurs), avec `statut` (0 = vendeur, 1 = acheteur) et `solde` |
| `categories` | Catégories de livres |
| `livres` | Livres publiés par les vendeurs (nom, description, prix, catégorie, vendeur) |
| `paniers` | Panier d'achat (livre, vendeur, acheteur) |
| `commandes` | Commandes validées (acheteur, livre, prix total) |
| `messages` | Notifications / messages liés aux utilisateurs |

---

## 🔄 Logique de paiement

Le flux d'achat garantit l'intégrité des transactions :

1. Vérification du solde de l'acheteur.
2. Transfert du montant du/des livre(s) vers le(s) vendeur(s).
3. Création de la commande.
4. Vidage du panier.

L'ensemble de ces opérations est exécuté dans une **transaction de base de données**, afin d'éviter tout état incohérent en cas d'erreur.

---

## 🚀 Installation et démarrage

### Prérequis
- PHP 8.1+
- Composer
- Docker et Docker Compose

### 1. Cloner le projet

```bash
git clone https://github.com/Dieudonne6/ProjetLivre.git
cd ProjetLivre
```

### 2. Configurer l'environnement

```bash
cp .env.example .env
```

Adapter au besoin les variables `DB_*` (le `docker-compose.yml` fourni utilise déjà `DB_DATABASE=livre`, `DB_USERNAME=laravel`, `DB_PASSWORD=secret`, `DB_PORT=3307`).

### 3. Lancer les conteneurs

```bash
docker compose up -d
```

Cela démarre :
- `app` — l'application Laravel (PHP)
- `nginx` — serveur web, exposé sur http://localhost:8000
- `mysql` — base de données MySQL, exposée sur le port 3307
- `phpmyadmin` — interface d'administration, sur http://localhost:8080

### 4. Installer les dépendances et préparer l'application

```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
```

### 5. Générer la documentation Swagger

```bash
docker compose exec app php artisan l5-swagger:generate
```

La documentation est alors accessible sur : `http://localhost:8000/api/documentation`

---

## 🧪 Comptes de test

| Rôle | Email | Mot de passe |
|---|---|---|
| Acheteur | buyer@gmail.com | buyer12345 |
| Vendeur | seller@gmail.com | seller12345 |

---

## 📚 Exemple de parcours (acheteur)

1. Inscription ou connexion.
2. Parcours des livres disponibles.
3. Ajout de livres au panier.
4. Recharge du solde du compte.
5. Validation du paiement.
6. La commande est créée, le solde des vendeurs est crédité et le panier est vidé.


---

## 📈 Ce que ce projet met en avant

- Conception d'API REST
- Authentification sécurisée (Sanctum)
- Transactions financières fiables
- Relations Eloquent (utilisateurs, livres, commandes, panier)
- Environnement conteneurisé et déployable (Docker + Render)
- Documentation d'API interactive (Swagger)

---

## 👨‍💻 Auteur

**K. Franck Dieu-donné AYENAN D.**
Backend Developer

📧 kossoufranck6@gmail.com