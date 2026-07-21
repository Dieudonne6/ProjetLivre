# 📚 Book Marketplace API

A RESTful API built with **Laravel** that allows users to buy and sell digital books.

This project simulates a **digital marketplace** where:
- Sellers can upload books and earn money.
- Buyers can browse books, add them to a cart, and purchase them using their account balance.

The API is fully documented with **Swagger/OpenAPI**, containerized with **Docker**, and deployed using **PostgreSQL** in production.


---

## 🌐 Live API

The API is deployed and publicly accessible:

Base URL:
https://projetlivre-3.onrender.com

Swagger Documentation:
https://projetlivre-3.onrender.com/api/documentation

---


# 🚀 Project Purpose

This project was built as part of a **backend developer portfolio** to demonstrate:

- REST API design
- Authentication using Laravel Sanctum
- Database design and transactions
- API documentation with Swagger
- Docker containerization
- Clean architecture using services and resources

---

# architecture diagram

Client
   |
Laravel API
   |
PostgreSQL


# 🛠 Tech Stack

| Technology | Purpose |
|------------|--------|
| Laravel | Backend framework |
| PostgreSQL | Production database |
| MySQL | Local development database |
| Docker | Containerization |
| Laravel Sanctum | API Authentication |
| Swagger (L5-Swagger) | API Documentation |
| Eloquent ORM | Database interactions |

---

# 📦 Main Features

### 👤 Authentication
- Register
- Login
- Logout
- Password reset

### 📚 Books
- Upload books (Seller)
- View all books
- Filter books by category

### 🛒 Cart
- Add book to cart
- View cart
- Remove book from cart

### 💳 Payment
- Recharge account balance
- Purchase books
- Automatic balance transfer between buyer and seller


---

# 🧪 Test Accounts

Two accounts are available for testing:

### Buyer Account
Email: buyer@gmail.com
Password: buyer12345

### Seller Account
Email: seller@gmail.com
Password: seller12345


---

# 📑 API Documentation

Swagger documentation is available at:

/api/documentation


It allows you to:

- explore all endpoints
- test requests directly
- see request/response examples

---

# 🔐 Authentication

This API uses **Bearer Token Authentication (Laravel Sanctum)**.

After login, include the token in requests:

It allows you to:

- explore all endpoints
- test requests directly
- see request/response examples

---

# 🔐 Authentication

This API uses **Bearer Token Authentication (Laravel Sanctum)**.

After login, include the token in requests:

Authorisation: Bearer YOUR_TOKEN



---

# 📚 Example Workflow

Typical buyer flow:

1️⃣ Register or login  
2️⃣ Browse available books  
3️⃣ Add books to cart  
4️⃣ Recharge account balance  
5️⃣ Validate payment  
6️⃣ Books are purchased and sellers receive payment

---

# 🧱 Database Entities

Main tables:

- users
- livres (books)
- categories
- panier (cart)
- commandes (orders)
- messages

---

# 🐳 Running the Project with Docker

Clone the repository:

https://github.com/Dieudonne6/ProjetLivre.git


Start containers:
docker compose up -d

Run migrations:
php artisan migrate


Generate Swagger docs:
php artisan l5-swagger:generate


---

# 📂 Project Structure

app
├── Http
│ ├── Controllers
│   │── Api
│ ├── Requests
│ └── Resources
├── Models
├── Services
database
routes
swagger


---

# 🔄 Payment Logic

The payment system ensures transactional integrity:

- Buyer balance is checked
- Book prices are transferred to sellers
- Orders are created
- Cart is cleared

All operations are wrapped inside a **database transaction**.

---

# 📈 What This Project Demonstrates

This project highlights backend skills such as:

✔ API architecture  
✔ Authentication systems  
✔ Secure transactions  
✔ RESTful design  
✔ Database relationships  
✔ Dockerized environments  
✔ API documentation

---

# 👨‍💻 Author

K. Franck Dieu-donné AYENAN D.

Backend Developer 

---

# 📬 Contact

LinkedIn: **  
Email: *kossoufranck6@gmail.com*

