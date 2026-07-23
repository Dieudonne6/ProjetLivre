# 📚 Book Marketplace API

A REST API built with **Laravel 10**, allowing users to buy and sell digital books. The project simulates a marketplace where sellers list books and buyers browse them, add them to a cart, and purchase them using a balance credited to their account.

The API is documented with **Swagger / OpenAPI**, containerized with **Docker**, and deployed to production with **PostgreSQL**.

---

## 🌐 Live Demo

| Resource | Link |
|---|---|
| API (Base URL) | https://projetlivre-3.onrender.com |
| Swagger Documentation | https://projetlivre-3.onrender.com/api/documentation |

---

## 🎯 Project Goal

This project was built as part of a backend portfolio to practice:

- designing a REST API;
- token-based authentication with **Laravel Sanctum**;
- modeling a relational database and handling transactions;
- API documentation with Swagger (L5-Swagger);
- containerization with Docker;
- a clean architecture based on controllers, requests, and Eloquent models.

---

## 🏗 Architecture

```
Client (front-end / Swagger UI)
        │
        ▼
   Laravel API (Sanctum)
        │
        ▼
  Database (MySQL locally / PostgreSQL in production)
```

---

## 🛠 Tech Stack

| Technology | Role |
|---|---|
| Laravel 10 (PHP 8.1+) | Backend framework |
| MySQL | Development database (via Docker) |
| PostgreSQL | Production database (Render) |
| Laravel Sanctum | Token-based (Bearer) authentication |
| L5-Swagger (OpenAPI) | Interactive API documentation |
| Eloquent ORM | Database access and relationships |
| Docker / Docker Compose | Containerization (app, nginx, mysql, phpMyAdmin) |

---

## 📦 Main Features

### 👤 Authentication
- Registration (seller or buyer, with profile picture)
- Login / logout
- Password change

### 📚 Books
- Publishing a book (seller)
- List of all available books
- Filtering books by category
- List of categories

### 🛒 Cart
- Adding a book to the cart
- Viewing the cart
- Removing a book from the cart

### 💳 Payment
- Topping up the account balance
- Payment validation and order creation
- Automatic amount transfer between the buyer and seller(s)

### 💬 Messages / Posts
- List of messages
- List and creation of posts

---

## 🔗 Main Endpoints

**Public**

| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/register` | Create an account |
| POST | `/api/login` | Log in and get a token |
| GET | `/api/categories` | List of categories |
| GET | `/api/listelivre` | List of books |
| GET | `/api/listemessage` | List of messages |
| GET | `/api/posts` | List of posts |
| POST | `/api/posts/create` | Create a post |

**Protected (Sanctum — require a Bearer token)**

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/user` | Currently authenticated user |
| PUT | `/api/modifpassword` | Change password |
| POST | `/api/createlivre` | Publish a book |
| POST | `/api/logout` | Log out |
| POST | `/api/addcart/{id}` | Add a book to the cart |
| GET | `/api/cart` | View the cart |
| DELETE | `/api/deletelivrecart/{id}` | Remove a book from the cart |
| GET | `/api/paiement` | Payment information |
| POST | `/api/validatepaiement` | Validate the payment / order |
| GET | `/api/livrecategorie/{categorie}` | Books in a category |
| PUT | `/api/rechargesolde` | Top up the account balance |

The complete list of routes, payloads, and responses is available in the Swagger documentation.

---

## 🔐 Authentication

The API uses **Bearer Token** authentication via Laravel Sanctum.

After logging in, pass the token in the header of every protected request:

```
Authorization: Bearer YOUR_TOKEN
```

---

## 🧱 Data Model (main tables)

| Table | Description |
|---|---|
| `users` | Users (sellers or buyers), with `statut` (0 = seller, 1 = buyer) and `solde` (balance) |
| `categories` | Book categories |
| `livres` | Books published by sellers (name, description, price, category, seller) |
| `paniers` | Shopping cart (book, seller, buyer) |
| `commandes` | Validated orders (buyer, book, total price) |
| `messages` | Notifications / messages linked to users |

---

## 🔄 Payment Logic

The purchase flow guarantees transaction integrity:

1. Checking the buyer's balance.
2. Transferring the amount of the book(s) to the seller(s).
3. Creating the order.
4. Emptying the cart.

All of these operations are executed within a **database transaction**, to avoid any inconsistent state in case of an error.

---

## 🚀 Installation and Setup

### Prerequisites
- PHP 8.1+
- Composer
- Docker and Docker Compose

### 1. Clone the project

```bash
git clone https://github.com/Dieudonne6/ProjetLivre.git
cd ProjetLivre
```

### 2. Set up the environment

```bash
cp .env.example .env
```

Adjust the `DB_*` variables if needed (the provided `docker-compose.yml` already uses `DB_DATABASE=livre`, `DB_USERNAME=laravel`, `DB_PASSWORD=secret`, `DB_PORT=3307`).

### 3. Start the containers

```bash
docker compose up -d
```

This starts:
- `app` — the Laravel application (PHP)
- `nginx` — web server, exposed on http://localhost:8000
- `mysql` — MySQL database, exposed on port 3307
- `phpmyadmin` — admin interface, on http://localhost:8080

### 4. Install dependencies and set up the application

```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
```

### 5. Generate the Swagger documentation

```bash
docker compose exec app php artisan l5-swagger:generate
```

The documentation is then available at: `http://localhost:8000/api/documentation`

---

## 🧪 Test Accounts

| Role | Email | Password |
|---|---|---|
| Buyer | buyer@gmail.com | buyer12345 |
| Seller | seller@gmail.com | seller12345 |

---

## 📚 Example Flow (buyer)

1. Register or log in.
2. Browse the available books.
3. Add books to the cart.
4. Top up the account balance.
5. Validate the payment.
6. The order is created, sellers' balances are credited, and the cart is emptied.

---

## 📈 What This Project Demonstrates

- REST API design
- Secure authentication (Sanctum)
- Reliable financial transactions
- Eloquent relationships (users, books, orders, cart)
- Containerized, deployable environment (Docker + Render)
- Interactive API documentation (Swagger)

---

## 👨‍💻 Author

**Franck Dieu-donné AYENAN**
Backend Developer

📧 kossoufranck6@gmail.com