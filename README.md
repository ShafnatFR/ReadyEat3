# ReadyEat3 🍔

ReadyEat3 is a modern food ordering application built with **Laravel 12** and **Tailwind CSS 4**. It features a robust backend for managing menus, orders, and payments, and a responsive frontend for customers to browse and order food.

## 📚 Documentation

- [Database Schema & Architecture](docs/DATABASE_SCHEMA.md)

## 🚀 Tech Stack

- **Framework**: Laravel 12
- **Language**: PHP 8.2+
- **Database**: MySQL
- **Frontend**: Blade, React, Tailwind CSS 4
- **Build Tool**: Vite

## 🛠️ Prerequisites

Ensure you have the following installed on your machine:
- [PHP 8.2+](https://www.php.net/downloads)
- [Composer](https://getcomposer.org/)
- [Node.js & npm](https://nodejs.org/)
- [MySQL](https://www.mysql.com/)

## 💿 Installation

1.  **Clone the repository**
    ```bash
    git clone https://github.com/ShafnatFR/ReadyEat3.git
    cd ReadyEat3
    ```

2.  **Install PHP dependencies**
    ```bash
    composer install
    ```

3.  **Install Node.js dependencies**
    ```bash
    npm install
    ```

4.  **Environment Setup**
    Copy the example environment file and configure your database credentials.
    ```bash
    cp .env.example .env
    ```
    Open `.env` and update the database settings:
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=readyeat3
    DB_USERNAME=root
    DB_PASSWORD=
    ```

5.  **Generate Application Key**
    ```bash
    php artisan key:generate
    ```

6.  **Run Migrations & Seeders**
    Set up the database tables and populate them with initial data.
    ```bash
    php artisan migrate --seed
    ```

7.  **Build Assets**
    Compile the frontend assets.
    ```bash
    npm run build
    ```

## 🏁 Running the Application

1.  **Start the Laravel Development Server**
    ```bash
    php artisan serve
    ```

2.  **Start the Vite Development Server** (for hot module replacement)
    ```bash
    npm run dev
    ```

3.  Access the application at `http://localhost:8000`.

## 🧪 Testing

Run the test suite to ensure everything is working correctly.

```bash
php artisan test
```

## 📂 Project Structure

- `app/Models`: Eloquent models (User, Menu, Order, Payment).
- `app/Http/Controllers`: Logic for handling requests (Admin & Customer).
- `database/migrations`: Database schema definitions.
- `resources/views`: Blade templates.
- `routes/web.php`: Application routes.

## 📄 License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
