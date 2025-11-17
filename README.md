# Task Management API

A RESTful API for managing tasks with authentication and role-based access control. Built with Laravel and using Sanctum for API authentication.

## Requirements

Before you start, make sure you have these installed:
- PHP >= 8.2
- Composer
- MySQL (XAMPP/WAMP/MAMP includes MySQL, or install it separately)
- XAMPP/WAMP/MAMP (or just use PHP's built-in server)

I'm using MySQL, so make sure MySQL is running on your machine. If you're using XAMPP, just start MySQL from the XAMPP control panel.

## Installation

### 1. Clone or download the project

If you're using git:
```bash
git clone https://github.com/AbdelrahmanAsismS/softexpert2025
cd softexpert2025
```

Or just download and extract the zip file somewhere.

### 2. Install dependencies

```bash
composer install
```

This might take a minute or two depending on your internet speed.

### 3. Set up environment file

Copy the `.env.example` to `.env` (if it doesn't exist already):

```bash
cp .env.example .env
```

Or on Windows:
```bash
copy .env.example .env
```

### 4. Generate application key

```bash
php artisan key:generate
```

This creates an encryption key that Laravel needs.

### 5. Database setup

First, make sure MySQL is running. If you're using XAMPP, start MySQL from the XAMPP control panel.

Create a new database in MySQL. You can do this using phpMyAdmin (comes with XAMPP):
1. Open phpMyAdmin (usually at `http://localhost/phpmyadmin`)
2. Click "New" in the left sidebar
3. Enter database name: `task_management` (or any name you prefer)
4. Click "Create"

Or create it using MySQL command line:
```bash
mysql -u root -p
CREATE DATABASE task_management;
exit
```

Now update your `.env` file with your MySQL credentials. Make sure these lines look like this:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=root
DB_PASSWORD=
```

Update the database name, username, and password to match your MySQL setup. If your MySQL has a password, add it to `DB_PASSWORD`.

### 6. Run migrations

```bash
php artisan migrate
```

This creates all the necessary tables (users, tasks, etc).

### 7. Seed the database

```bash
php artisan db:seed
```

This creates some test users so you can login and test the API:
- Manager: `manager@example.com` / `password123`
- User 1: `user1@example.com` / `password123`
- User 2: `user2@example.com` / `password123`

## Running the application

Start the development server:

```bash
php artisan serve
```

By default it runs on `http://localhost:8000`. If port 8000 is taken, you can specify a different port:

```bash
php artisan serve --port=8001
```

The API endpoints will be available at `http://localhost:8000/api/`

## API Endpoints

All endpoints (except login) require authentication. Include the Bearer token in the Authorization header.

### Authentication
- `POST /api/login` - Login and get access token
- `GET /api/me` - Get current user info
- `POST /api/logout` - Logout

### Tasks
- `GET /api/tasks` - List all tasks (with filters: status, assignee_id, from, to)
- `GET /api/tasks/{id}` - Get task details
- `POST /api/tasks` - Create task (Manager only)
- `PUT /api/tasks/{id}` - Update task (Manager only)
- `PATCH /api/tasks/{id}` - Update task (Manager only)
- `PATCH /api/tasks/{id}/status` - Update task status (Users can update their own tasks)
- `DELETE /api/tasks/{id}` - Delete task (Manager only)

### Roles and Permissions

**Managers can:**
- Create, update, and delete tasks
- Assign tasks to any user
- View all tasks
- Filter tasks by assignee

**Regular Users can:**
- View only tasks assigned to them
- Update status of their own tasks only

## Testing with Postman

I've included a Postman collection file (`Task_Management_API.postman_collection.json`) that you can import. Here's how:

1. Open Postman
2. Click "Import" button
3. Select the `Task_Management_API.postman_collection.json` file
4. The collection should appear with all endpoints

Make sure to:
- Set the `base_url` variable to `http://localhost:8000/api` (or whatever port you're using)
- Run the "Login" request first to get an access token
- The token will be automatically saved and used for other requests

## Notes

- The API uses Laravel Sanctum for authentication (token-based)
- Task dependencies work with parent_id - a task can't be completed if its children aren't completed
- Users can't filter by other users' tasks, only managers can
- Tasks are automatically set to "pending" status when created
- The creator is automatically set to the authenticated user

If you run into any issues, check the Laravel logs in `storage/logs/laravel.log` - they usually tell you what went wrong.

Good luck!
