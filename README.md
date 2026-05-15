# PageTurner Online Bookstore Management System

## Student Information
- **Name:** Aaron Clyde C. Cervantes
- **Course:** Bachelor of Science in Information Technology
- **University:** Central Mindanao University
- **Activity:** Laboratory Activity 6 - Advanced Laravel Features & Enterprise-Grade Bookstore System

## Project Description
PageTurner is a comprehensive, enterprise-grade online bookstore management system built with Laravel 12. This full-featured web application demonstrates advanced Laravel concepts including routing, controllers, views, Blade templating, database operations, user authentication, and modern enterprise features. The system serves both administrators and customers with distinct functionalities for managing books, categories, orders, reviews, auditing, backups, and analytics.

## Learning Objectives Achieved
### Core Laravel Concepts
- ✅ Advanced routing with grouping, middleware, and resource controllers
- ✅ Complex controller architecture with admin and customer separation
- ✅ Blade templating with components, inheritance, and slots
- ✅ Database migrations with relationships and constraints
- ✅ Eloquent ORM with complex queries and relationships
- ✅ Database seeders and factories for test data

### Advanced Features Implemented
- ✅ Two-factor authentication (2FA) with recovery codes
- ✅ Email verification system
- ✅ Role-based access control (RBAC) with admin/customer/premium tiers
- ✅ API rate limiting with tiered access
- ✅ Comprehensive audit logging system
- ✅ Automated backup management with monitoring
- ✅ Data import/export functionality (Excel)
- ✅ Full-text search with Laravel Scout
- ✅ Queue system for background jobs
- ✅ Redis caching and session management
- ✅ Scheduled tasks and maintenance commands

## Features

### User Management & Authentication
- 👤 User registration and login via Laravel Breeze
- 🔐 Two-factor authentication (2FA) with recovery codes
- ✉️ Email verification system
- 🎯 Role-based access control (Admin/Customer/Premium)
- 📝 User profile management with password changes
- 🛡️ Protected routes with middleware
- 📊 User management dashboard (Admin)

### Book Management (Admin)
- 📚 Complete CRUD operations for books
- 🏷️ Category assignment and management
- 📊 Stock quantity tracking with low-stock alerts
- 🖼️ Cover image upload support
- 📖 Detailed book information (ISBN, author, description, pricing)
- 🔍 Full-text search with Laravel Scout
- 📈 Book performance analytics
- 📋 Batch operations and bulk imports

### Category Management (Admin)
- 📂 Create, read, update, delete categories
- 📋 Category descriptions and metadata
- 📊 Book count per category
- 🎨 Category organization and hierarchy

### Customer Features
- 🔍 Browse books with advanced search and filtering
- 📖 View detailed book information with ratings
- ⭐ Read and write book reviews with 5-star ratings
- 🛒 Shopping cart functionality with persistent storage
- 📦 Order management and history
- 💬 Review management (edit/delete own reviews)
- 🎁 Wishlist functionality (future enhancement)

### Review System
- ⭐ 5-star rating system with averages
- 💬 Written reviews with detailed comments
- 👤 User-specific review management
- 📊 Average rating calculations
- 🔒 Review ownership validation

### Admin Dashboard & Analytics
- 📊 Comprehensive dashboard with key metrics
- 📈 Sales analytics and trends
- 👥 User activity monitoring
- 📚 Book performance metrics
- 🎯 Order statistics and insights

### Audit & Compliance
- 📋 Complete audit logging of all user actions
- 🔍 Audit trail with timestamps and user information
- 📊 Audit log export functionality
- 🔐 Security event tracking
- 📝 Compliance reporting

### Backup & Data Management
- 💾 Automated backup system with monitoring
- 📅 Scheduled backup tasks
- 🔄 Backup restoration capabilities
- 📊 Backup monitoring dashboard
- 🗑️ Cleanup of old backups

### Import/Export Features
- 📤 Export books, users, orders, and audit logs to Excel
- 📥 Import books from Excel files
- 📊 Batch operations with progress tracking
- 🔍 Import validation and error reporting
- 📋 Export history and logging

### API Rate Limiting
- 🚦 Tiered rate limiting (Standard/Premium/Admin)
- 📊 Rate limit monitoring dashboard
- 🔧 Configurable rate limits per tier
- 📈 Usage analytics and tracking
- ⚠️ Rate limit alerts and notifications

### Queue & Background Jobs
- ⚙️ Redis-based queue system
- 📧 Email notifications
- 🔄 Asynchronous task processing
- 📊 Job monitoring and logging
- 🔧 Queue management commands

### Caching & Performance
- ⚡ Redis caching layer
- 🔄 Session management with Redis
- 📊 Cache statistics and monitoring
- 🎯 Query optimization with eager loading
- 📈 Performance monitoring

## Technologies Used
- **Laravel:** 12.x
- **PHP:** 8.2+
- **Database:** MySQL with read replicas support
- **Cache/Session:** Redis
- **Queue System:** Redis-based queues
- **Search:** Laravel Scout with database driver (Meilisearch optional)
- **Authentication:** Laravel Breeze with 2FA
- **File Storage:** Laravel Storage with S3 support
- **Export:** Maatwebsite Excel
- **Backup:** Spatie Laravel Backup
- **PDF Generation:** Laravel DomPDF
- **Templating:** Blade Template Engine
- **Frontend:** HTML5, CSS3, Tailwind CSS
- **Version Control:** Git & GitHub

## Database Schema

### Tables Overview
- **users** - User accounts with roles and 2FA support
- **categories** - Book categories/genres
- **books** - Book inventory with category relationships
- **orders** - Customer orders with status tracking
- **order_items** - Individual items in orders
- **reviews** - Customer book reviews with ratings
- **audit_logs** - Complete audit trail of system actions
- **api_rate_limits** - Rate limiting configuration and tracking
- **backup_monitoring** - Backup status and monitoring
- **export_logs** - Export operation history
- **import_logs** - Import operation history
- **scheduled_tasks** - Scheduled task tracking
- **sessions** - User session data (Redis)
- **cache** - Application cache (Redis)
- **jobs** - Queue job tracking (Redis)

### Key Relationships
- User hasMany Orders, Reviews, AuditLogs
- Category hasMany Books
- Book belongsTo Category, hasMany Reviews, OrderItems
- Order belongsTo User, hasMany OrderItems
- Review belongsTo User, Book
- AuditLog belongsTo User
- OrderItem belongsTo Order, Book

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
