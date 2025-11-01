#  Sportradar Backend Assignment (Laravel)

This is the **backend service** for the Sportradar Coding Academy assignment, built using **Laravel**.  
It provides secure and scalable APIs for managing **sports events**, including creating, listing, and retrieving event details.

---

##  Features

- Built with **Laravel 12**
- RESTful API structure
- CRUD operations for events (Create, Read, Update, Delete)
- Uses **MySQL** 
- Follows clean architecture with Controllers, Models, and Resource classes
- Includes **Form Requests** for validation
- API responses follow **JSON:API** conventions
- Unit & Feature tests with **PHPUnit**

---

 

##  Installation Guide

### 📦 Please follow these steps to install and run the project
Ensure you have an active internet connection — some files are loaded from CDN.

```bash
# 1. Clone the repository
git clone https://github.com/asadkhalid566/sportradar_BE.git

# 2. Move into the project directory
cd sportradar_BE

# 3. Install PHP dependencies
composer install
 

# 5. Copy the example environment file
cp .env.example .env

# 6. Set your database name and credentials in the .env file
# Example:
# DB_DATABASE=sportradar_db
# DB_USERNAME=root
# DB_PASSWORD=

# 7. Generate application key
php artisan key:generate

# 8. Run database migrations
php artisan migrate

# 9. Seed the database with initial data
php artisan db:seed

# 10. Start the Laravel development server
php artisan serve

php artisan test