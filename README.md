A fully functional, beginner-friendly e-commerce website built with HTML, CSS, JavaScript, PHP, and MySQL. Ready to run on XAMPP localhost.

 Features

 User Featueres
-  User registration and login with password hashing
-  User profile management (view and edit)
-  Browse products with categories
-  Search and filter products
-  Product details page with images and descriptions
-  Add/remove products from cart
-  Update cart quantities
-  Checkout process (simulated, no real payment)
-  Order history and tracking
-  Responsive, mobile-friendly design

--> Admin Features
-  Secure admin login
-  Dashboard with statistics
-  Product management (Add, Edit, Delete)
-  Category management
-  User management and deletion
-  Order management and status updates
-  Low stock alerts
-  Recent activity monitoring

--> Security
-  Password hashing with bcrypt
-  SQL injection prevention (prepared statements)
-  Input sanitization and validation
-  Session-based authentication
-  CSRF protection
-  User role-based access control


-  --> Project Structure
E-comm/
├── config/
│   └── db.php                 # Database configuratio
├── includes/
│   ├── functions.php          # Helper functions
│   ├── header.php             # Header component
│   ├── footer.php             # Footer component
│   └── navbar.php             # Navigation component
├── pages/
│   ├── register.php           # User registration
│   ├── login.php              # User login
│   ├── logout.php             # User logout
│   ├── shop.php               # Products listing
│   ├── product-detail.php     # Product details
│   ├── cart.php               # Shopping cart
│   ├── add-to-cart.php        # Add to cart (AJAX)
│   ├── checkout.php           # Checkout page
│   ├── orders.php             # Order history
│   ├── order-detail.php       # Order details
│   └── profile.php            # User profile
├── admin/
│   ├── index.php              # Admin login
│   ├── dashboard.php          # Admin dashboard
│   ├── products.php           # Manage products
│   ├── add-product.php        # Add/edit product
│   ├── edit-product.php       # Edit product (redirect)
│   ├── categories.php         # Manage categories
│   ├── users.php              # Manage users
│   ├── orders.php             # Manage orders
│   └── logout.php             # Admin logout
├── assets/
│   ├── css/
│   │   ├── style.css          # Main stylesheet
│   │   └── admin-style.css    # Admin stylesheet
│   ├── js/
│   │   └── script.js          # Main JavaScript
│   └── images/
│       └── (product images)
├── uploads/
│   └── products/              # Product image uploads
├── sql/
│   └── ecommerce_db.sql       # Database schema
└── index.php                  # Home page

