# PageTurner Online Bookstore Management System

## Student Information
- **Name:** Aaron Clyde C. Cervantes
- **Course:** Bachelor of Science in Information Technology
- **University:** Central Mindanao University
- **Activity:** Laboratory Activity 3 - Routing, Controllers, Views, Blade Templating, and Database Operations

## Project Description
PageTurner is a comprehensive online bookstore management system built with Laravel. This full-featured web application demonstrates advanced Laravel concepts including routing, controllers, views, Blade templating, database operations, and user authentication. The system serves both administrators and customers with distinct functionalities for managing books, categories, orders, and reviews.

## Learning Objectives Achieved
### Routing & HTTP Methods
- ✅ Defined routes using GET, POST, PUT, PATCH, and DELETE methods
- ✅ Implemented route parameters with constraints
- ✅ Created named routes, route prefixes, and route grouping
- ✅ Applied middleware for authentication and authorization

### Controllers
- ✅ Generated controllers using Artisan commands
- ✅ Bound routes to controller methods
- ✅ Implemented resource controllers for CRUD operations
- ✅ Applied proper validation and error handling

### Views & Blade Templating
- ✅ Used Blade syntax and directives (@if, @foreach, @extends, @yield, @section, @include)
- ✅ Passed data from controllers to views using compact() and with()
- ✅ Created layout templates and reusable partial views
- ✅ Implemented Blade components with slots

### Database Operations
- ✅ Created database migrations for multiple tables
- ✅ Defined Eloquent models with relationships
- ✅ Implemented database seeders and factories
- ✅ Performed CRUD operations using Eloquent ORM

### Authentication
- ✅ Set up Laravel Breeze for user authentication
- ✅ Implemented authorization in views using @auth and @guest directives
- ✅ Created role-based access control (admin/customer)

## Features

### User Management
- 👤 User registration and login via Laravel Breeze
- 🔐 Role-based authentication (Admin/Customer)
- 📝 User profile management
- 🛡️ Protected routes with middleware

### Book Management (Admin)
- 📚 Complete CRUD operations for books
- 🏷️ Category assignment and management
- 📊 Stock quantity tracking
- 🖼️ Cover image upload support
- 📖 Detailed book information (ISBN, author, description)

### Category Management (Admin)
- 📂 Create, read, update, delete categories
- 📋 Category descriptions
- 📊 Book count per category

### Customer Features
- 🔍 Browse books with search and filtering
- 📖 View detailed book information
- ⭐ Read and write book reviews
- 🛒 Shopping cart functionality (bonus feature)
- 📦 Order management and history

### Review System
- ⭐ 5-star rating system
- 💬 Written reviews with comments
- 👤 User-specific review management
- 📊 Average rating calculations

## Technologies Used
- **Laravel:** 10.x/11.x
- **PHP:** 8.1+
- **Database:** MySQL/SQLite
- **Authentication:** Laravel Breeze
- **Templating:** Blade Template Engine
- **Frontend:** HTML5, CSS3, Tailwind CSS
- **Version Control:** Git & GitHub

## Database Schema

### Tables Overview
- **users** - User accounts (customers & admins)
- **categories** - Book categories/genres
- **books** - Book inventory with category relationships
- **orders** - Customer orders
- **order_items** - Individual items in orders
- **reviews** - Customer book reviews

### Key Relationships
- User hasMany Orders, Reviews
- Category hasMany Books
- Book belongsTo Category, hasMany Reviews, OrderItems
- Order belongsTo User, hasMany OrderItems
- Review belongsTo User, Book

## Installation Instructions

### 1. Clone the Repository
```bash
git clone [your-repository-url]
cd pageturner-bookstore
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Configure Environment
```bash
cp .env.example .env
```

Edit `.env` file with your database configuration:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pageturner_bookstore
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Install Laravel Breeze
```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm run build
```

### 6. Run Migrations and Seeders
```bash
php artisan migrate:fresh --seed
```

### 7. Create Storage Link (for file uploads)
```bash
php artisan storage:link
```

### 8. Start Development Server
```bash
php artisan serve
```

### 9. Access the Application
Visit: `http://localhost:8000`

## Default Login Credentials

### Admin Account
- **Email:** admin@pageturner.com
- **Password:** password

### Test Customer Accounts
- Multiple customer accounts created via seeders
- **Password:** password (for all seeded accounts)

## Usage Guide

### For Administrators
1. **Login** with admin credentials
2. **Manage Categories:**
   - Navigate to Categories section
   - Create, edit, or delete book categories
3. **Manage Books:**
   - Add new books with category assignment
   - Upload cover images
   - Update stock quantities and pricing
   - Edit book details and descriptions
4. **View Orders:** Monitor customer orders and update status

### For Customers
1. **Register/Login** as a customer
2. **Browse Books:**
   - View all books or filter by category
   - Search by title or author
   - View detailed book information
3. **Write Reviews:**
   - Rate books (1-5 stars)
   - Leave written comments
   - Edit or delete your reviews
4. **Place Orders:** Add books to cart and checkout

## MVC Architecture Implementation

### Models
- **User:** Authentication and user data
- **Category:** Book categorization
- **Book:** Book inventory with relationships
- **Order:** Customer order management
- **OrderItem:** Individual order line items
- **Review:** Customer feedback system

### Views (Blade Templates)
- **Layout System:** `layouts/app.blade.php` with partials
- **Authentication:** Login, register, profile pages
- **Book Management:** Index, show, create, edit views
- **Category Management:** CRUD operation views
- **Components:** Reusable book cards, alerts, navigation

### Controllers
- **HomeController:** Homepage with featured content
- **CategoryController:** Category CRUD operations
- **BookController:** Book management with search/filter
- **OrderController:** Order processing and history
- **ReviewController:** Review submission and management

## Route Structure

### Public Routes
- `GET /` - Homepage
- `GET /books` - Book listing
- `GET /books/{book}` - Book details
- `GET /categories` - Category listing

### Authenticated Routes
- `POST /books/{book}/reviews` - Submit review
- `GET /orders` - Order history
- `POST /orders` - Create order

### Admin Routes (Prefix: /admin)
- Category management routes
- Book management routes
- Protected by authentication middleware

## Blade Features Demonstrated

### Directives Used
- `@extends` - Template inheritance
- `@section/@yield` - Content sections
- `@include` - Partial views
- `@foreach/@forelse` - Data iteration
- `@if/@auth/@guest` - Conditional rendering
- `@csrf/@method` - Form security
- `@error` - Validation error display

### Components
- Book card component with slots
- Alert component with type variants
- Reusable navigation and footer partials

## Security Features
✅ **CSRF Protection:** All forms protected with @csrf tokens  
✅ **Authentication:** Laravel Breeze integration  
✅ **Authorization:** Role-based access control  
✅ **Validation:** Comprehensive form validation  
✅ **SQL Injection Prevention:** Eloquent ORM usage  
✅ **File Upload Security:** Proper image validation  

## What I Learned

### Technical Skills
- Advanced Laravel routing with grouping and middleware
- Resource controllers and RESTful conventions
- Complex Eloquent relationships and queries
- Blade templating with components and inheritance
- Database migrations with foreign key constraints
- Factory and seeder implementation for test data
- File upload handling with Laravel Storage
- Form validation and error handling

### Conceptual Understanding
- MVC architecture in complex applications
- Authentication and authorization patterns
- Database design with normalized relationships
- RESTful API design principles
- Component-based UI development
- Security best practices in web applications

## Project Structure
```
pageturner-bookstore/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/ (Breeze controllers)
│   │   │   ├── BookController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── HomeController.php
│   │   │   ├── OrderController.php
│   │   │   └── ReviewController.php
│   │   └── Requests/
│   └── Models/
│       ├── User.php
│       ├── Category.php
│       ├── Book.php
│       ├── Order.php
│       ├── OrderItem.php
│       └── Review.php
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
│       ├── auth/ (Breeze views)
│       ├── books/
│       ├── categories/
│       ├── cart/
│       ├── components/
│       ├── layouts/
│       └── partials/
└── routes/
    └── web.php
```

## Challenges Faced and Solutions

1. **Challenge:** Complex Eloquent relationships
   - **Solution:** Carefully planned database schema and used proper foreign key constraints

2. **Challenge:** Role-based authorization
   - **Solution:** Implemented custom middleware and helper methods in User model

3. **Challenge:** File upload handling
   - **Solution:** Used Laravel Storage facade with proper validation

4. **Challenge:** Complex Blade component structure
   - **Solution:** Created reusable components with slots for flexibility

## Future Enhancements
- [ ] Advanced inventory management
- [ ] Email notifications for orders
- [ ] Wishlist functionality
- [ ] Book recommendations system
- [ ] Multi-language support
- [ ] Payment gateway integration
- [ ] Advanced reporting dashboard

## Academic Integrity Statement
This project was completed individually as part of Laboratory Activity 3. All code was written following Laravel best practices and the provided specifications. The implementation demonstrates original understanding of the concepts taught in class.


## License
This project was created for educational purposes as part of coursework requirements.

## Acknowledgments
- **Laravel Documentation** - For comprehensive framework guidance
- **Laravel Breeze** - For authentication scaffolding
- **Tailwind CSS** - For responsive design components
- **My Instructor** - For providing detailed specifications and support

---

**Created with ❤️ for Laboratory Activity 3**  
*Routing, Controllers, Views, Blade Templating, and Database Operations*  
*PageTurner Online Bookstore Management System*
