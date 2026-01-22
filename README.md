# AdoPet

> Web application for pet adoption and management

## Team Members

- **Marija Musa**
- **Jelena Vučić**

## Project Overview

AdoPet is a web application that allows users to browse, manage, and adopt pets.  
Users can register, view pet profiles, add pets (if they are admins or shelters), and interact through comments and notifications.  
The project uses **Laravel** for the backend, **Vue 3 + Vite** for the frontend.


## Getting Started

### Backend Setup

```bash
# Navigate to backend directory
cd backend

# Install PHP dependencies
composer install

# Copy .env.example to .env
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Start backend server
php artisan serve

```
### Frontend Setup

```bash
# Navigate to frontend directory
cd frontend

# Install JS dependencies
npm install

# Start development server
npm run dev

```
### Example MySQL Configuration in .env

```ini

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
