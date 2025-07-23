# Laravel E-Commerce Platform

A comprehensive e-commerce platform built with **Laravel** and **Blade**, offering powerful backend management and a modern, user-friendly shopping experience. This project is designed to handle core e-commerce functionalities including product listing, shopping cart, checkout, order tracking, and admin panel operations.

## 🚀 Features

* 🔐 **Authentication** (User & Admin)
* 🛍️ **Product & Inventory Management**
* 🛒 **Shopping Cart & Wishlist**
* 💳 **Stripe Payment Gateway Integration**
* 📦 **Order Management & Tracking**
* 🧾 **Discount Coupons**
* 🌍 **Country-based Shipping Charges**
* ✉️ **Email Notifications**
* 📄 **Dynamic Pages Management**
* ⭐ **Product Ratings**
* 🧑‍💼 **Role-based Access (Admin Panel)**
* 💬 **Real-time Chat System (Chatify Integration)**

## 🏗️ Tech Stack

* **Backend**: Laravel, PHP, Eloquent ORM
* **Frontend**: Blade, Bootstrap
* **Database**: MySQL
* **Authentication**: Laravel Auth, Middleware
* **Payments**: Stripe API
* **Real-time Features**: Laravel WebSockets, Chatify
* **Caching & Queues**: Laravel Queue System

## 📁 Project Structure

```
├── app/
│   ├── Console/
│   ├── Events/              # Custom Events (e.g. NotifyEvent)
│   ├── Exceptions/
│   ├── Helpers/             # Custom helper functions
│   ├── Http/
│   │   ├── Controllers/     # Frontend + Admin Controllers
│   │   ├── Middleware/      # Auth, CSRF, Admin Auth
│   ├── Mail/                # Order and Contact Mails
│   ├── Models/              # Eloquent Models (Product, Order, User, etc.)
│   └── Providers/
├── bootstrap/
├── config/                 # Laravel configurations
├── database/
│   ├── factories/           # Model factories
│   ├── migrations/          # DB schema
│   └── seeders/             # CountrySeeder, etc.
├── public/                 # Public assets
├── resources/
│   ├── views/              # Blade Templates (frontend + admin)
│   ├── js/                 # JS scripts
│   └── sass/               # Stylesheets
├── routes/
│   ├── web.php             # Web routes
│   └── admin.php           # Admin routes (if separated)
├── tests/                  # Feature & Unit tests
├── artisan
├── composer.json
├── package.json
├── vite.config.js
├── webpack.mix.js
└── .env.example
```

## ⚙️ Installation

```bash
git clone https://github.com/danishali22/laravel-ecommerence.git
cd laravel-ecommerence

# Install PHP dependencies
composer install

# Install JS dependencies
npm install && npm run dev

# Create environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Run migrations and seeders
php artisan migrate --seed

# (Optional) Link storage
php artisan storage:link
```

## 💳 Stripe Configuration

Set the following in your `.env` file:

```
STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret
```

## 🧪 Testing

```bash
php artisan test
```

## 🧠 Learnings

* Deep Laravel MVC architecture
* Advanced Eloquent relationships and queries
* Stripe payment integration in Laravel
* Custom Blade components and layout design
* Admin dashboard with role-based access
* Implemented websockets and real-time chat

## 📎 Related Links

* 🔗 [MERN E Commerence Frontend Repo](https://github.com/danishali22/mern-ecommerence-frontend)
* 🔗 [MERN E Commerence Backend Repo](https://github.com/danishali22/mern-ecommerence-backend)

## 🧑‍💻 Author

**Danish Ali**
[GitHub](https://github.com/danishali22)

---

> This project demonstrates the power and flexibility of Laravel as a full-stack e-commerce solution with an integrated admin dashboard and modern frontend using Blade.
