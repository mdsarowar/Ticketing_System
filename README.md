
# Customer Support Ticketing System
A full-stack customer support ticketing system with real-time chat functionality built with Laravel, Vue.js, and Inertia.js.

# Live link
https://ticketingsystem-production-acd5.up.railway.app/

## Features

- **Authentication & Authorization**
    - Token-based authentication using Laravel Sanctum
    - Two user roles: Admin and Customer
    - Registration, Login, and Logout functionality

- **Ticket Management**
    - Create, Read, Update, Delete (CRUD) operations
    - Fields: Subject, Description, Category, Priority, Status, Attachment
    - Role-based access (Admins see all tickets, Customers see own tickets)
    - Status tracking: Open, In Progress, Resolved, Closed

- **Comments System**
    - Both Admins and Customers can comment on tickets
    - Edit and delete own comments

- **Real-time Chat**
    - WebSocket-based real-time messaging using Laravel Pusher
    - Customer ↔ Admin communication
    - Chat linked to specific tickets
    - Message read receipts

- **Responsive UI**
    - Modern, clean interface built with Vue.js and Tailwind CSS
    - Inertia.js for seamless SPA experience

## Tech Stack

**Backend:**
- Laravel 12
- Laravel Sanctum (Authentication)
- Laravel Pusher (WebSocket)
- MySQL (Database)

**Frontend:**
- Vue 3
- Inertia.js
- Tailwind CSS
- Laravel Echo & Pusher JS



## Installation

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL


### Setup Instructions

1. **Clone the repository**
```bash
git clone https://github.com/mdsarowar/Ticketing_System.git
cd Ticketing_System
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Install Node dependencies**
```bash
npm install
```

4. **Environment Configuration**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Run Migrations**
```bash
php artisan migrate --seed
```

6. **Create Storage Link**
```bash
php artisan storage:link
```

## Running the Application

You need to run **2 separate terminals**:

**Terminal 1 - Laravel Server:**
```bash
composer run dev
```


[//]: # (**Terminal 3 - Reverb WebSocket Server:**)

[//]: # (```bash)

[//]: # (php artisan reverb:start)

[//]: # (```)

## Default Users (After Seeding)

**Admin Account:**
- Email: `admin@test.com`
- Password: `password`

**Customer Account:**
- Email: `customer@test.com`
- Password: `password`

Access the application at: `http://localhost:8000`

## API Documentation
URL for published documentation
https://documenter.getpostman.com/view/48165300/2sB3Wjz4Rs


## Author

Your Name - S.M. Sarowar (https://github.com/mdsarowar)

## Acknowledgments

- Laravel Framework
- Vue.js
- Inertia.js
- Tailwind CSS
