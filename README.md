# Decoration Rental Project - Complete System Documentation

## Table of Contents
1. [Project Overview](#project-overview)
2. [Business Context](#business-context)
3. [System Architecture](#system-architecture)
4. [Technology Stack](#technology-stack)
5. [Database Schema](#database-schema)
6. [User Roles & Workflows](#user-roles--workflows)
7. [Features & Functionality](#features--functionality)
8. [Page-by-Page Documentation](#page-by-page-documentation)
9. [Backend API Endpoints](#backend-api-endpoints)
10. [Form Validation Rules](#form-validation-rules)
11. [Security Implementation](#security-implementation)
12. [Installation & Setup Guide](#installation--setup-guide)
13. [Directory Structure](#directory-structure)
14. [File-by-File Reference](#file-by-file-reference)
15. [Future Enhancement Recommendations](#future-enhancement-recommendations)

---

## 1. Project Overview

### 1.1 Project Name
**NGTRUST LTD / DecoRwanda** - Decoration Rental Management System

### 1.2 Purpose
A web-based application for managing event decoration product rentals, designed for both clients and staff. The system handles:
- Client registration and authentication
- Product bookings
- Product returns
- Missing product reports
- Stock management by employees
- Stock comparison and reporting

### 1.3 Target Users
1. **Clients**: Customers who rent decoration items for events
2. **Staff/Employees**: Company personnel who manage inventory and stock

### 1.4 Core Business Problem Solved
The system digitizes and streamlines the decoration rental business by:
- Eliminating manual booking processes
- Tracking inventory in real-time
- Managing product returns and accountability
- Identifying missing items
- Comparing stock reports across different employees/shifts

---

## 2. Business Context

### 2.1 Company Information
- **Business Name**: DecoRwanda / NGTRUST LTD
- **Industry**: Event decoration and rental services
- **Location**: Rwanda
- **Services**: Wedding decorations, corporate events, parties, and general event rental equipment

### 2.2 Product Catalog
The system manages five core product categories:
1. **Chairs** (Product ID: 1)
2. **Tents** (Product ID: 2)
3. **Tables** (Product ID: 3)
4. **Lamps** (Product ID: 4)
5. **Flowers** (Product ID: 5)

### 2.3 Business Workflows
1. **Client Journey**: Register → Login → Browse Products → Book → Return → Report Issues
2. **Staff Journey**: Login → Submit Stock Reports → View Stock Comparisons
3. **Management Journey**: Review bookings, returns, missing items, and stock discrepancies

---

## 3. System Architecture

### 3.1 Architecture Type
**Traditional MVC (Model-View-Controller)** with server-side rendering

### 3.2 Application Pattern
- **Frontend**: HTML5 pages with embedded JavaScript
- **Backend**: PHP scripts for business logic
- **Database**: MySQL/MariaDB relational database
- **Session Management**: PHP sessions for authentication state

### 3.3 Data Flow
```
User → HTML Form → POST Request → PHP Script → Database Operation → Response (Redirect/Alert)
```

### 3.4 Authentication Flow
```
1. User submits login form (email + password)
2. PHP verifies credentials against database
3. Password hash verification using password_verify()
4. Session created with client_id
5. Subsequent pages check session via auth.php
6. Unauthorized users redirected to login.php
```

---

## 4. Technology Stack

### 4.1 Frontend Technologies
- **HTML5**: Page structure
- **CSS3**: Styling (multiple stylesheets)
- **JavaScript (Vanilla)**: Client-side validation and dynamic UI

### 4.2 Backend Technologies
- **PHP**: Server-side scripting (no framework)
- **MySQLi**: Database connectivity

### 4.3 Database
- **Database System**: MySQL/MariaDB
- **Database Name**: `decoration_rental`
- **Connection Method**: MySQLi extension

### 4.4 Server Requirements
- PHP 7.0+ (with password_hash support)
- MySQL 5.6+ or MariaDB 10.0+
- Apache/Nginx web server
- Session support enabled in PHP

---

## 5. Database Schema

### 5.1 Database Name
`decoration_rental`

### 5.2 Tables & Structure

#### Table: `clients`
Stores customer information and credentials.

```sql
CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    personal_id VARCHAR(16) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(13) NOT NULL,
    workplace VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**Fields:**
- `id`: Unique client identifier (auto-increment)
- `personal_id`: National ID or personal identification (16 characters)
- `full_name`: Client's full name
- `phone`: Phone number (Rwandan format: +25078xxxxxxx or +25079xxxxxxx)
- `workplace`: Client's place of work
- `email`: Unique email address (used for login)
- `password`: Hashed password using PHP's PASSWORD_DEFAULT algorithm
- `created_at`: Registration timestamp

**Indexes:**
- PRIMARY KEY on `id`
- UNIQUE KEY on `email`

---

#### Table: `products`
Stores available rental products.

```sql
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**Initial Data:**
```sql
INSERT INTO products (id, name) VALUES
(1, 'Chairs'),
(2, 'Tents'),
(3, 'Tables'),
(4, 'Lamps'),
(5, 'Flowers');
```

**Fields:**
- `id`: Unique product identifier
- `name`: Product name
- `description`: Product description (optional)
- `price`: Rental price (optional, not currently used)
- `created_at`: Record creation timestamp

---

#### Table: `bookings`
Stores client product booking requests.

```sql
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    booking_date DATE NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
```

**Fields:**
- `id`: Unique booking identifier
- `client_id`: Reference to client who made the booking
- `product_id`: Reference to booked product
- `quantity`: Number of units booked
- `booking_date`: Date when client needs the items
- `notes`: Additional booking instructions (optional)
- `created_at`: When booking was created

**Relationships:**
- Foreign key to `clients(id)`
- Foreign key to `products(id)`

---

#### Table: `returns`
Stores product return records.

```sql
CREATE TABLE returns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    return_date DATE NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
```

**Fields:**
- `id`: Unique return identifier
- `client_id`: Reference to client returning items
- `product_id`: Reference to returned product
- `quantity`: Number of units returned
- `return_date`: Date of return
- `notes`: Additional return notes (optional)
- `created_at`: Record creation timestamp

**Relationships:**
- Foreign key to `clients(id)`
- Foreign key to `products(id)`

---

#### Table: `missing_items`
Stores reports of missing or lost products.

```sql
CREATE TABLE missing_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    notes TEXT,
    client_id INT,
    reported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (client_id) REFERENCES clients(id)
);
```

**Fields:**
- `id`: Unique report identifier
- `product_id`: Reference to missing product
- `quantity`: Number of units missing
- `notes`: Description of circumstances (optional)
- `client_id`: Reference to client (nullable - may be staff reporting)
- `reported_at`: When issue was reported

**Relationships:**
- Foreign key to `products(id)`
- Foreign key to `clients(id)` (nullable)

---

#### Table: `employees`
Stores staff/employee information.

```sql
CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**Fields:**
- `id`: Unique employee identifier
- `name`: Employee's full name
- `created_at`: Record creation timestamp

**Note:** Employees are auto-created when submitting stock reports if they don't exist.

---

#### Table: `stock_reports`
Stores periodic inventory counts by employees.

```sql
CREATE TABLE stock_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    employee_id INT NOT NULL,
    quantity INT NOT NULL,
    notes TEXT,
    reported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (employee_id) REFERENCES employees(id)
);
```

**Fields:**
- `id`: Unique stock report identifier
- `product_id`: Reference to product being counted
- `employee_id`: Reference to employee who did the count
- `quantity`: Number of units counted
- `notes`: Additional observations (optional)
- `reported_at`: Timestamp of stock count

**Relationships:**
- Foreign key to `products(id)`
- Foreign key to `employees(id)`

---

### 5.3 Entity Relationship Diagram (ERD)

```
┌─────────────┐         ┌──────────────┐
│   clients   │────┐    │   products   │
└─────────────┘    │    └──────────────┘
      │            │           │
      │            │           │
      │            │           │
      ├────────────┼───────────┤
      │            │           │
      ▼            ▼           ▼
┌─────────────┐  ┌─────────────┐  ┌──────────────┐
│  bookings   │  │   returns   │  │missing_items │
└─────────────┘  └─────────────┘  └──────────────┘


┌─────────────┐         ┌──────────────┐
│  employees  │◄────────│stock_reports │
└─────────────┘         └──────────────┘
                              │
                              │
                              ▼
                        ┌──────────────┐
                        │   products   │
                        └──────────────┘
```

---

## 6. User Roles & Workflows

### 6.1 User Roles

#### Role 1: Client/Customer
**Access Level**: Authenticated users who rent products

**Capabilities:**
- Register for an account
- Login with email/password
- Browse products and gallery
- Book products with specific dates
- Return products
- Report missing items
- View company information (About, Mission, Vision)
- Logout

**Authentication**: Required (session-based)

#### Role 2: Staff/Employee
**Access Level**: Company personnel managing inventory

**Capabilities:**
- Submit stock reports
- View stock comparisons
- Track inventory discrepancies

**Authentication**: Partially implemented (uses client authentication for some features, employee name input for stock reports)

---

### 6.2 Client Workflow

#### 6.2.1 Registration Flow
```
1. Navigate to register.html
2. Fill out registration form:
   - Full Name
   - Personal ID (16 characters minimum)
   - Phone (Rwandan format: +25078/79 + 7 digits)
   - Workplace
   - Email (unique identifier)
   - Password (minimum 6 characters)
3. JavaScript validates input before submission
4. Submit → register.php
5. Password hashed using password_hash()
6. Insert into clients table
7. Redirect to login.html
```

#### 6.2.2 Login Flow
```
1. Navigate to login.html
2. Enter email and password
3. Submit → login.php
4. Query database for matching email
5. Verify password using password_verify()
6. If valid:
   - Create session with client_id
   - Redirect to homepage.html
7. If invalid:
   - Display "Invalid login" message
```

#### 6.2.3 Booking Flow
```
1. Login required (enforced by auth.php)
2. Navigate to booking.html
3. Select products via checkboxes (dynamic quantity fields appear)
4. Enter quantity for each selected product
5. Choose booking date (must be present or future)
6. Add optional notes
7. JavaScript validates:
   - At least one product selected
   - Valid quantities (positive integers)
   - Valid date (not in past)
   - Notes under 300 characters
8. Submit → booking.php
9. Loop through products and insert into bookings table
10. Show success alert
11. Redirect to dashboard.html
```

#### 6.2.4 Return Flow
```
1. Login required
2. Navigate to returning.html
3. Select products to return via checkboxes
4. Enter quantity returned for each
5. Choose return date
6. Add optional notes
7. Validate form (similar to booking)
8. Submit → returning.php
9. Insert into returns table for each product
10. Show success alert
11. Redirect to dashboard.html
```

#### 6.2.5 Missing Item Report Flow
```
1. Login required (optional - client_id can be NULL)
2. Navigate to missing.html
3. Select missing products via checkboxes
4. Enter quantity missing
5. Add optional notes describing circumstances
6. Validate form
7. Submit → missing.php
8. Insert into missing_items table
9. Show success alert
10. Redirect to dashboard.html
```

---

### 6.3 Staff Workflow

#### 6.3.1 Stock Report Submission Flow
```
1. Navigate to stock.html
2. Select products counted
3. Enter quantities for each
4. Enter employee name (auto-creates employee if new)
5. Add optional notes
6. Validate form
7. Submit → stock.php
8. Check if employee exists in database
   - If exists: retrieve employee_id
   - If new: insert into employees table, get new ID
9. Insert stock report entries for each product
10. Show success alert
11. Redirect to stock_comparison.php
```

#### 6.3.2 Stock Comparison View Flow
```
1. Navigate to stock_comparison.php (auto-loads after stock submission)
2. System runs complex SQL query to compare:
   - Current stock count vs. previous count
   - Current employee vs. previous employee
   - Calculated difference
3. Display results in HTML table showing:
   - Product name
   - Current employee and quantity
   - Previous employee and quantity
   - Difference (discrepancy indicator)
   - Timestamp of current report
4. Helps identify inventory discrepancies between shifts/employees
```

---

## 7. Features & Functionality

### 7.1 Core Features

#### Feature 1: User Authentication
- **Registration**: New client account creation with validation
- **Login**: Email/password authentication with session management
- **Logout**: Session destruction and redirect
- **Session Protection**: Unauthorized access prevention via auth.php

#### Feature 2: Product Booking System
- **Multi-product selection**: Checkbox-based product selection
- **Dynamic quantity input**: Quantity fields appear when product is selected
- **Date selection**: Future-date validation for bookings
- **Notes field**: Custom instructions support
- **Database persistence**: All bookings saved to database with client association

#### Feature 3: Product Return Management
- **Return tracking**: Record product returns with quantities
- **Date tracking**: Return date recording
- **Notes support**: Return condition documentation
- **Client association**: Returns linked to specific clients

#### Feature 4: Missing Item Reporting
- **Loss tracking**: Report lost or missing products
- **Quantity tracking**: Specify how many units are missing
- **Note documentation**: Explain circumstances
- **Optional client association**: Can be reported by staff or clients

#### Feature 5: Stock Management
- **Employee-based counting**: Each count associated with specific employee
- **Multi-product reporting**: Count multiple products in one submission
- **Auto-employee creation**: New employees auto-registered during first report
- **Timestamp tracking**: Precise recording of when counts occurred

#### Feature 6: Stock Comparison & Analysis
- **Current vs. Previous**: Compare latest stock count with previous count
- **Employee accountability**: Track which employee performed each count
- **Discrepancy detection**: Calculate differences between counts
- **Time-based tracking**: Show when each count was performed
- **Product-wise analysis**: Separate analysis for each product type

#### Feature 7: Information Pages
- **Homepage**: Welcome page with company branding
- **Gallery**: Visual showcase of past events (6 images)
- **Products**: Description of available rental products
- **About**: Company background and services
- **Mission**: Company mission statement
- **Vision**: Company vision statement

---

### 7.2 User Interface Features

#### Navigation System
- Consistent navigation bar across all pages
- Links to: Home, Gallery, Products, About, Mission, Vision, Dashboard
- Responsive navigation (topnav class)

#### Form Validation
- **Client-side validation**: JavaScript validation before submission
- **Server-side validation**: PHP validation in backend scripts
- **User feedback**: Alert messages for validation errors
- **Visual feedback**: Success alerts with checkmarks (✅)

#### Dynamic UI Elements
- **Checkbox-triggered inputs**: Quantity fields appear/disappear based on selection
- **Product list rendering**: JavaScript-generated product checkboxes
- **Date constraints**: Minimum date set to today (prevents past dates)
- **Form state management**: Dynamic addition/removal of form fields

---

### 7.3 Data Management Features

#### Database Operations
- **CRUD Operations**: Create, Read operations implemented
- **Prepared Statements**: SQL injection protection
- **Foreign Key Relationships**: Data integrity through relationships
- **Auto-increment IDs**: Automatic primary key generation

#### Session Management
- **Stateful sessions**: PHP sessions maintain login state
- **Client ID tracking**: Session stores authenticated client ID
- **Session validation**: auth.php middleware checks authentication
- **Session destruction**: Proper logout implementation

---

## 8. Page-by-Page Documentation

### 8.1 Public Pages (No Authentication Required)

#### Page: `register.html`
**Purpose**: New client registration

**URL**: `/register.html`

**Form Fields:**
1. `name` (text, required): Full name
2. `personal_id` (text, required): Personal ID (min 16 characters)
3. `phone` (text, required): Phone number (Rwandan format)
4. `workplace` (text, required): Place of employment
5. `email` (email, required): Email address (unique)
6. `password` (password, required): Password (min 6 characters)

**Validation (JavaScript):**
- Name: Cannot be empty
- Personal ID: Minimum 16 characters
- Phone: Must match regex `/^\+2507[89]\d{7}$/` (Rwandan format)
- Email: Must match email pattern
- Password: Minimum 6 characters

**Form Action**: `POST /register.php`

**Styles**: `css/style.css`

**Scripts**: `js/validate.js`

**Success Flow**: Redirect to `login.html`

---

#### Page: `login.html`
**Purpose**: Client authentication

**URL**: `/login.html`

**Form Fields:**
1. `email` (email, required): Registered email
2. `password` (password, required): Account password

**Validation**: HTML5 required attributes

**Form Action**: `POST /login.php`

**Styles**: `css/style.css`

**Success Flow**: 
- Create session with `client_id`
- Redirect to `homepage.html`

**Error Flow**: Display "Invalid login" message

---

### 8.2 Authenticated Pages (Login Required)

#### Page: `homepage.html`
**Purpose**: Landing page after login

**URL**: `/homepage.html`

**Authentication**: Not enforced (should be protected)

**Content**:
- Company name: NGTRUST LTD
- Welcome message
- Slideshow background
- Navigation to all sections

**Styles**: `css/home.css`

**Navigation Links**:
- Home, Gallery, Products, About, Mission, Vision, Dashboard

---

#### Page: `dashboard.html`
**Purpose**: Client dashboard with action links

**URL**: `/dashboard.html` (rendered via `dashboard.php`)

**Authentication**: Required (via `includes/auth.php`)

**Available Actions**:
1. View Products → `products.html`
2. Book Products → `booking.html`
3. Return Products → `returning.html`
4. Report Missing Products → `missing.html`
5. Stock Management (Staff) → `stock.html`
6. Logout → `logout.php`

**Styles**: `css/style.css`

---

#### Page: `booking.html`
**Purpose**: Book rental products

**URL**: `/booking.html`

**Authentication**: Should be required (not explicitly enforced in HTML)

**Product Selection**:
- Chairs (ID: 1)
- Tents (ID: 2)
- Tables (ID: 3)
- Lamps (ID: 4)
- Flowers (ID: 5)

**Form Fields:**
- `products[]` (hidden array): Selected product IDs
- `quantities[]` (number array): Quantities for each product
- `booking_date` (date, required): Desired booking date
- `notes` (textarea, optional): Special instructions (max 300 chars)

**Dynamic Behavior**:
- Checking a product checkbox reveals quantity input
- Unchecking removes the quantity input
- `toggleQuantity(productId)` function manages this

**Validation (JavaScript - inline)**:
- At least one product must be selected
- All quantities must be positive integers
- Booking date cannot be in the past
- Notes must be ≤ 300 characters

**Form Action**: `POST /booking.php`

**Success Flow**: 
- Alert: "✅ Booking successfully submitted!"
- Redirect to `dashboard.html`

---

#### Page: `returning.html`
**Purpose**: Return rented products

**URL**: `/returning.html`

**Authentication**: Should be required

**Form Structure**: Similar to `booking.html`

**Form Fields:**
- `products[]` (hidden array): Product IDs being returned
- `quantities[]` (number array): Quantities returned
- `return_date` (date, required): Date of return
- `notes` (textarea, optional): Return notes

**Dynamic Behavior**: Same checkbox/quantity toggle as booking

**Validation**:
- At least one product selected
- Valid quantities (positive integers)

**Form Action**: `POST /returning.php`

**Success Flow**: 
- Alert: "✅ Return submitted successfully!"
- Redirect to `dashboard.html`

---

#### Page: `missing.html`
**Purpose**: Report missing or lost products

**URL**: `/missing.html`

**Authentication**: Partially required (client_id can be NULL)

**Form Fields:**
- `products[]` (hidden array): Missing product IDs
- `quantities[]` (number array): Quantities missing
- `notes` (textarea, optional): Circumstances description

**Form Action**: `POST /missing.php`

**Success Flow**: 
- Alert: "✅ Missing product report submitted successfully!"
- Redirect to `dashboard.html`

---

#### Page: `stock.html`
**Purpose**: Staff stock count submission

**URL**: `/stock.html`

**Authentication**: Not enforced (should be protected for staff only)

**Form Fields:**
- `products[]` (hidden array): Product IDs counted
- `quantities[]` (number array): Counted quantities
- `employee_name` (text, required): Employee's name
- `notes` (textarea, optional): Observations

**Validation**:
- At least one product selected
- Valid quantities
- Employee name required

**Form Action**: `POST /stock.php`

**Success Flow**: 
- Alert: "✅ Stock report submitted successfully!"
- Redirect to `stock_comparison.php`

---

### 8.3 Information Pages

#### Page: `gallery.html`
**Purpose**: Display event photos

**URL**: `/gallery.html`

**Content**:
- 6 images from `Gallery/` folder (image1.jpeg through image6.png)
- Gallery grid layout

**Styles**: `css/gallery.css`

---

#### Page: `product.html`
**Purpose**: Product information

**URL**: `/product.html`

**Content**: 
- Description of rental products
- Link to gallery
- General product categories

**Styles**: `css/home.css`

---

#### Page: `about.html`
**Purpose**: Company information

**Content**:
- Company background
- Service description
- Specializations (weddings, corporate, parties)

---

#### Page: `mission.html`
**Purpose**: Mission statement

**Content**: 
"To elevate every event experience by delivering top-notch decoration and rental services with professionalism and care."

---

#### Page: `vision.html`
**Purpose**: Vision statement

**Content**: 
"To become the leading event decoration company in East Africa by continually exceeding client expectations."

---

### 8.4 Backend/Report Pages

#### Page: `stock_comparison.php`
**Purpose**: Display stock count comparisons

**URL**: `/stock_comparison.php`

**Authentication**: Not enforced

**Functionality**:
- Queries latest stock report for each product
- Compares with previous stock report
- Calculates differences
- Shows employee accountability

**Display Table Columns**:
1. Product name
2. Current employee
3. Current quantity
4. Previous employee
5. Previous quantity
6. Difference (discrepancy)
7. Report timestamp

**SQL Logic**: 
- Joins `stock_reports`, `products`, `employees`
- Uses MAX(reported_at) to find latest/previous reports
- LEFT JOINs to handle products with only one report

**Initial Alert**: "✅ Stock report comparison loaded successfully!"

---

## 9. Backend API Endpoints

### 9.1 Authentication Endpoints

#### Endpoint: `register.php`
**Method**: POST

**Purpose**: Create new client account

**Request Parameters**:
- `name` (string, required): Client's full name
- `personal_id` (string, required): Personal ID
- `phone` (string, required): Phone number
- `workplace` (string, required): Workplace
- `email` (string, required): Email address
- `password` (string, required): Plain text password

**Process**:
1. Receive POST data
2. Hash password using `password_hash($password, PASSWORD_DEFAULT)`
3. Prepare INSERT statement with bound parameters
4. Execute: `INSERT INTO clients (personal_id, full_name, phone, workplace, email, password) VALUES (?, ?, ?, ?, ?, ?)`
5. Close statement and connection

**Response**: 
- Success: `header("Location: login.html")`
- Error: None explicitly handled (would show database error)

**Security**:
- Password hashing (bcrypt via PASSWORD_DEFAULT)
- SQL injection protection (prepared statements)
- No duplicate email enforcement (database UNIQUE constraint)

---

#### Endpoint: `login.php`
**Method**: POST

**Purpose**: Authenticate client

**Request Parameters**:
- `email` (string, required)
- `password` (string, required)

**Process**:
1. Start session
2. Prepare SELECT statement: `SELECT id, password FROM clients WHERE email = ?`
3. Bind email parameter
4. Execute query
5. If one row found:
   - Fetch id and hashed_password
   - Verify password using `password_verify($password, $hashed_password)`
   - If valid:
     - Set `$_SESSION['client_id'] = $id`
     - Redirect to `homepage.html`
6. If invalid: Display "Invalid login"

**Response**:
- Success: Redirect to `homepage.html` with session
- Failure: Echo "Invalid login"

**Security**:
- Password verification (never compares plain text)
- Prepared statements
- Session-based authentication

---

#### Endpoint: `logout.php`
**Method**: GET

**Purpose**: End user session

**Process**:
1. Start session
2. Destroy session: `session_destroy()`
3. Redirect to login page

**Response**: `header("Location: login.php")`

---

### 9.2 Booking Endpoints

#### Endpoint: `booking.php`
**Method**: POST

**Purpose**: Create product bookings

**Authentication**: Required (includes `auth.php`)

**Request Parameters**:
- `products[]` (array of integers): Product IDs
- `quantities[]` (array of integers): Corresponding quantities
- `booking_date` (date string): Booking date
- `notes` (string, optional): Additional instructions

**Process**:
1. Include `auth.php` (validates session, gets `client_id`)
2. Retrieve `client_id` from session
3. Get POST arrays: `products[]`, `quantities[]`
4. Loop through arrays:
   - Validate quantity is numeric and > 0
   - Prepare INSERT: `INSERT INTO bookings (client_id, product_id, quantity, booking_date, notes) VALUES (?, ?, ?, ?, ?)`
   - Bind parameters (iiiss)
   - Execute
5. Close statement

**Response**: 
- HTML with JavaScript alert and redirect
- Alert: "✅ Booking successfully submitted!"
- Redirect: `window.location.href = "dashboard.html"`

**Validation**:
- Server-side: Quantity must be numeric and positive
- Invalid entries skipped via `continue`

---

#### Endpoint: `returning.php`
**Method**: POST

**Purpose**: Record product returns

**Authentication**: Required (session check)

**Request Parameters**:
- `products[]` (array): Product IDs
- `quantities[]` (array): Return quantities
- `return_date` (date): Date of return
- `notes` (string, optional): Return notes

**Process**:
1. Check session for `client_id` (redirect to login if missing)
2. Get client_id from session
3. Prepare INSERT: `INSERT INTO returns (client_id, product_id, quantity, return_date, notes, created_at) VALUES (?, ?, ?, ?, ?, NOW())`
4. Loop through products array
5. Bind and execute for each product

**Response**:
- Alert: "✅ Return submitted successfully!"
- Redirect: `dashboard.html`

---

#### Endpoint: `missing.php`
**Method**: POST

**Purpose**: Report missing products

**Authentication**: Partial (client_id can be NULL)

**Request Parameters**:
- `products[]` (array, required): Missing product IDs
- `quantities[]` (array, required): Missing quantities
- `notes` (string, optional): Description

**Process**:
1. Validate request method is POST
2. Validate products and quantities arrays exist and match in length
3. Get client_id from session (optional)
4. Prepare INSERT: `INSERT INTO missing_items (product_id, quantity, notes, client_id, reported_at) VALUES (?, ?, ?, ?, NOW())`
5. Loop and execute for each product

**Response**:
- Alert: "✅ Missing product report submitted successfully!"
- Redirect: `dashboard.html`

**Error Handling**:
- Comprehensive validation with specific error messages
- Database error catching
- Dies with error message if validation fails

---

### 9.3 Stock Management Endpoints

#### Endpoint: `stock.php`
**Method**: POST

**Purpose**: Submit employee stock count

**Authentication**: Not enforced

**Request Parameters**:
- `products[]` (array): Product IDs counted
- `quantities[]` (array): Counted quantities
- `employee_name` (string, required): Employee name
- `notes` (string, optional): Observations

**Process**:
1. Validate employee_name exists (die if empty)
2. Check if employee exists in database:
   - Query: `SELECT id FROM employees WHERE name = ?`
   - If exists: Use existing employee_id
   - If not: Insert new employee, get insert_id
3. Prepare stock report INSERT: `INSERT INTO stock_reports (product_id, employee_id, quantity, notes, reported_at) VALUES (?, ?, ?, ?, NOW())`
4. Loop through products and execute inserts

**Response**:
- JavaScript alert: "✅ Stock report submitted successfully!"
- Redirect: `stock_comparison.php`

**Special Features**:
- Auto-creates employees if they don't exist
- Timestamps all reports with NOW()

---

#### Endpoint: `stock_comparison.php`
**Method**: GET

**Purpose**: Display stock comparison report

**Authentication**: Not enforced

**Process**:
1. Execute complex SQL query joining:
   - Current stock reports (latest)
   - Previous stock reports (second-most recent)
   - Products table
   - Employees table
2. Calculate differences between current and previous counts
3. Display results in HTML table

**SQL Query Logic**:
```sql
SELECT 
    p.name AS product_name,
    e1.name AS current_employee,
    sr1.quantity AS current_quantity,
    e2.name AS previous_employee, 
    sr2.quantity AS previous_quantity,
    (sr1.quantity - sr2.quantity) AS difference,
    sr1.reported_at AS current_report_time
FROM stock_reports sr1
JOIN (subquery for latest reports) latest
LEFT JOIN (subquery for previous reports) prev_report
LEFT JOIN stock_reports sr2 (previous report data)
JOIN products p
JOIN employees e1 (current employee)
LEFT JOIN employees e2 (previous employee)
ORDER BY product name
```

**Display**:
- HTML table with styled header
- Shows discrepancies (difference column)
- Previous data shows as '-' if no previous report exists

**Initial Alert**: "✅ Stock report comparison loaded successfully!"

---

## 10. Form Validation Rules

### 10.1 Registration Form Validation

#### Client-Side (JavaScript - `validate.js`)

**Full Name**:
- Rule: Cannot be empty
- Error: "Please enter your full name."

**Personal ID**:
- Rule: Minimum 16 characters
- Error: "Personal ID must be at least 16 characters."

**Phone**:
- Rule: Must match regex `/^\+2507[89]\d{7}$/`
- Format: +25078XXXXXXX or +25079XXXXXXX
- Error: "Please enter a valid Rwandan phone number starting with +25078 or +25079 followed by 7 digits (total 13 characters)."

**Workplace**:
- Rule: Cannot be empty
- Error: "Please enter your workplace."

**Email**:
- Rule: Must match regex `/^[^ ]+@[^ ]+\.[a-z]{2,}$/i`
- Error: "Please enter a valid email address."

**Password**:
- Rule: Minimum 6 characters
- Error: "Password should be at least 6 characters long."

---

### 10.2 Booking Form Validation

#### Client-Side (JavaScript - inline in `booking.html`)

**Product Selection**:
- Rule: At least one checkbox must be checked
- Error: "Please select at least one product."

**Quantities**:
- Rule: All quantity inputs must have values > 0
- Error: "Please enter a valid quantity for each selected product."

**Booking Date**:
- Rule: Cannot be empty
- Rule: Cannot be in the past
- Minimum date: Today (set via `input.min = today`)
- Error (empty): "Please select a booking date."
- Error (past): "Booking date cannot be in the past."

**Notes**:
- Rule: Maximum 300 characters
- Error: "Notes should not exceed 300 characters."

---

### 10.3 Return Form Validation

**Product Selection**: At least one required
**Quantities**: Positive integers required
**Return Date**: Required (no past validation on returns)
**Notes**: Optional

---

### 10.4 Missing Items Form Validation

**Product Selection**: At least one required
**Quantities**: Positive integers required
**Notes**: Optional

---

### 10.5 Stock Report Form Validation

**Product Selection**: At least one required
**Quantities**: Positive integers required
**Employee Name**: Required and cannot be empty
**Notes**: Optional

---

## 11. Security Implementation

### 11.1 Authentication Security

#### Password Security
- **Hashing Algorithm**: PHP's `password_hash()` with `PASSWORD_DEFAULT`
- **Current Implementation**: Bcrypt (as of PHP 7.x)
- **Salt**: Automatically generated and stored in hash
- **Verification**: `password_verify()` for constant-time comparison
- **Storage**: 255-character field to accommodate future algorithm changes

#### Session Security
- **Session Start**: `session_start()` called in required pages
- **Session Storage**: `$_SESSION['client_id']` stores authenticated user ID
- **Session Validation**: `auth.php` middleware checks session existence
- **Session Destruction**: Proper `session_destroy()` on logout

#### Authentication Middleware
**File**: `includes/auth.php`
```php
session_start();
if (!isset($_SESSION['client_id'])) {
    header("Location: login.php");
    exit();
}
```
- Included in protected pages
- Redirects unauthenticated users to login
- Prevents direct URL access

---

### 11.2 SQL Injection Protection

**Method**: Prepared Statements with Bound Parameters

**Example**:
```php
$stmt = $conn->prepare("SELECT id, password FROM clients WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
```

**Implementation**:
- All database queries use prepared statements
- Parameters bound with appropriate data types (s=string, i=integer)
- No direct concatenation of user input into SQL

**Binding Types Used**:
- `s`: String
- `i`: Integer
- `d`: Double (not currently used)

---

### 11.3 XSS Protection

**Current Implementation**: Limited

**Issues**:
- No HTML escaping on output
- Database values displayed directly in HTML
- JavaScript alerts use unescaped values

**Recommendations** (not implemented):
- Use `htmlspecialchars()` for all user-generated content
- Implement Content Security Policy headers
- Sanitize all output in `stock_comparison.php` table

---

### 11.4 CSRF Protection

**Current Implementation**: None

**Vulnerability**: 
- Forms have no CSRF tokens
- POST requests can be forged from external sites

**Recommendation** (not implemented):
- Generate unique token per session
- Include hidden token field in all forms
- Validate token on server before processing

---

### 11.5 Access Control

**Current Implementation**: Basic session-based

**Protected Pages**:
- `dashboard.php` (via `auth.php` include)
- `booking.php` (via `auth.php` include)

**Unprotected Pages** (should be protected):
- `returning.html` (client-side only, no server validation)
- `missing.html` (partially protected)
- `stock.html` (should be staff-only)
- `stock_comparison.php` (no authentication)

**Missing Features**:
- Role-based access control (Staff vs. Client)
- Permission levels
- Admin interface

---

### 11.6 Data Validation

**Client-Side Validation**:
- JavaScript validation in forms
- Regex patterns for phone and email
- Date constraints

**Server-Side Validation**:
- Limited implementation
- Some numeric validation in `booking.php`
- Required field validation in `missing.php`

**Gaps**:
- Insufficient server-side validation
- Reliance on client-side validation can be bypassed
- No input sanitization

---

### 11.7 Error Handling

**Current Approach**:
- Database errors displayed to user (potential information disclosure)
- `die()` statements used for validation failures
- Generic error messages in some places

**Security Concerns**:
- Error messages may reveal database structure
- No logging mechanism
- No graceful error handling

---

## 12. Installation & Setup Guide

### 12.1 System Requirements

**Server Requirements**:
- PHP 7.0 or higher
- MySQL 5.6+ or MariaDB 10.0+
- Apache 2.4+ or Nginx 1.10+
- Minimum 50MB disk space

**PHP Extensions Required**:
- mysqli
- session
- password (built-in)

**Recommended**:
- PHP 7.4+ for better performance
- MySQL 8.0+ or MariaDB 10.4+

---

### 12.2 Database Setup

#### Step 1: Create Database
```sql
CREATE DATABASE decoration_rental CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### Step 2: Create Tables

**Clients Table**:
```sql
USE decoration_rental;

CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    personal_id VARCHAR(16) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(13) NOT NULL,
    workplace VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**Products Table**:
```sql
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO products (id, name) VALUES
(1, 'Chairs'),
(2, 'Tents'),
(3, 'Tables'),
(4, 'Lamps'),
(5, 'Flowers');
```

**Bookings Table**:
```sql
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    booking_date DATE NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);
```

**Returns Table**:
```sql
CREATE TABLE returns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    return_date DATE NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);
```

**Missing Items Table**:
```sql
CREATE TABLE missing_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    notes TEXT,
    client_id INT,
    reported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL
);
```

**Employees Table**:
```sql
CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**Stock Reports Table**:
```sql
CREATE TABLE stock_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    employee_id INT NOT NULL,
    quantity INT NOT NULL,
    notes TEXT,
    reported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);
```

---

### 12.3 File Installation

#### Step 1: Extract Files
1. Extract project files to web server document root
2. Typical paths:
   - Apache: `/var/www/html/decoration-rental-project/`
   - XAMPP: `C:\xampp\htdocs\decoration-rental-project\`
   - WAMP: `C:\wamp64\www\decoration-rental-project\`

#### Step 2: Configure Database Connection
Edit `includes/db.php`:
```php
<?php
$host = 'localhost';        // Database host
$user = 'root';             // Database username
$pass = '';                 // Database password
$db   = 'decoration_rental'; // Database name

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}
?>
```

**Modify for Production**:
- Change `$user` and `$pass` to actual credentials
- Consider using environment variables for sensitive data
- Enable error logging instead of displaying errors

#### Step 3: Set Permissions
```bash
# For Linux/Unix systems
chmod 755 -R decoration-rental-project/
chmod 644 decoration-rental-project/*.php
chmod 644 decoration-rental-project/*.html
```

#### Step 4: Create Required Directories
Ensure these directories exist:
```
decoration-rental-project/
├── Background/  (for slideshow images)
├── Gallery/     (for gallery images)
├── css/
├── js/
└── includes/
```

Add images:
- Place 6 images in `Gallery/` folder (image1.jpeg, image2.jpeg, image3.png, image4.png, image5.png, image6.png)
- Add slideshow backgrounds in `Background/` folder (referenced in CSS)

---

### 12.4 Web Server Configuration

#### Apache Configuration
Create `.htaccess` file in project root:
```apache
# Enable mod_rewrite for clean URLs (optional)
RewriteEngine On

# Redirect to homepage if accessing directory
DirectoryIndex homepage.html

# Protect includes directory
<Directory "includes">
    Require all denied
</Directory>

# PHP settings
php_value session.cookie_httponly 1
php_value session.use_only_cookies 1
```

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name example.com;
    root /var/www/decoration-rental-project;
    index homepage.html;

    location / {
        try_files $uri $uri/ =404;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    # Protect includes directory
    location /includes/ {
        deny all;
    }
}
```

---

### 12.5 Testing Installation

#### Test 1: Database Connection
1. Navigate to any PHP file (e.g., `register.html`)
2. Try to register a test account
3. Check for database connection errors

#### Test 2: Registration Flow
1. Go to `register.html`
2. Fill out form with valid data
3. Submit and verify redirect to login page
4. Check database: `SELECT * FROM clients;`

#### Test 3: Login Flow
1. Go to `login.html`
2. Enter registered email and password
3. Verify redirect to `homepage.html`
4. Check session: Add `<?php session_start(); print_r($_SESSION); ?>` temporarily

#### Test 4: Booking Flow
1. Login as test client
2. Navigate to `booking.html`
3. Select products and submit
4. Verify database: `SELECT * FROM bookings;`

#### Test 5: Stock Report
1. Go to `stock.html`
2. Enter employee name and stock counts
3. Submit and check redirect to `stock_comparison.php`
4. Verify data: `SELECT * FROM stock_reports;`

---

## 13. Directory Structure

```
decoration-rental-project/
│
├── index.html                 [Entry point, could redirect to homepage]
├── homepage.html              [Landing page after login]
├── login.html                 [Login form]
├── login.php                  [Login processor]
├── register.html              [Registration form]
├── register.php               [Registration processor]
├── logout.php                 [Logout handler]
│
├── dashboard.html             [Client dashboard (static)]
├── dashboard.php              [Client dashboard (protected)]
│
├── booking.html               [Product booking form]
├── booking.php                [Booking processor]
├── returning.html             [Product return form]
├── returning.php              [Return processor]
├── missing.html               [Missing items report form]
├── missing.php                [Missing items processor]
│
├── stock.html                 [Stock report form (staff)]
├── stock.php                  [Stock report processor]
├── stock_comparison.php       [Stock comparison report]
│
├── product.html               [Products information page]
├── gallery.html               [Photo gallery]
├── about.html                 [About company]
├── mission.html               [Mission statement]
├── vision.html                [Vision statement]
│
├── includes/
│   ├── db.php                 [Database connection]
│   └── auth.php               [Authentication middleware]
│
├── css/
│   ├── style.css              [General form styles]
│   ├── home.css               [Homepage and info pages styles]
│   ├── gallery.css            [Gallery page styles]
│   └── dashboardstyles.css    [Dashboard styles]
│
├── js/
│   └── validate.js            [Registration form validation]
│
├── Background/
│   └── [slideshow images]     [Background images for slideshow]
│
└── Gallery/
    ├── image1.jpeg            [Gallery image 1]
    ├── image2.jpeg            [Gallery image 2]
    ├── image3.png             [Gallery image 3]
    ├── image4.png             [Gallery image 4]
    ├── image5.png             [Gallery image 5]
    └── image6.png             [Gallery image 6]
```

---

## 14. File-by-File Reference

### 14.1 HTML Files

| File | Purpose | Authentication | Forms | Links To |
|------|---------|----------------|-------|----------|
| homepage.html | Landing page | No | No | All sections via nav |
| login.html | Login form | No | Yes | login.php |
| register.html | Registration form | No | Yes | register.php |
| dashboard.html | Static dashboard | No | No | Action pages |
| booking.html | Booking form | Should be | Yes | booking.php |
| returning.html | Return form | Should be | Yes | returning.php |
| missing.html | Missing report form | Partial | Yes | missing.php |
| stock.html | Stock form | Should be | Yes | stock.php |
| gallery.html | Photo gallery | No | No | - |
| product.html | Products info | No | No | - |
| about.html | Company info | No | No | - |
| mission.html | Mission statement | No | No | - |
| vision.html | Vision statement | No | No | - |

---

### 14.2 PHP Files

| File | Method | Purpose | Includes | Output |
|------|--------|---------|----------|--------|
| register.php | POST | Create client account | db.php | Redirect to login.html |
| login.php | POST | Authenticate client | db.php | Redirect to homepage.html or error |
| logout.php | GET | End session | - | Redirect to login.php |
| dashboard.php | GET | Protected dashboard | auth.php | HTML dashboard |
| booking.php | POST | Process bookings | auth.php, db.php | Alert + redirect |
| returning.php | POST | Process returns | db.php | Alert + redirect |
| missing.php | POST | Process missing reports | db.php | Alert + redirect |
| stock.php | POST | Process stock reports | db.php | Alert + redirect to stock_comparison.php |
| stock_comparison.php | GET | Display stock comparison | db.php | HTML table |

---

### 14.3 Include Files

#### `includes/db.php`
**Purpose**: Database connection
**Type**: Configuration
**Contents**:
- Database credentials
- MySQLi connection object creation
- Connection error handling

**Usage**: Included in all PHP files that need database access

---

#### `includes/auth.php`
**Purpose**: Authentication middleware
**Type**: Security
**Contents**:
- Session start
- Session validation
- Redirect if not authenticated

**Usage**: Included in protected pages

---

### 14.4 CSS Files

| File | Purpose | Used By |
|------|---------|---------|
| style.css | General form styles | Forms (login, register, booking, etc.) |
| home.css | Homepage and info pages | homepage, about, mission, vision, product |
| gallery.css | Gallery page styling | gallery.html |
| dashboardstyles.css | Dashboard styling | dashboard pages |

**Note**: Actual CSS content not reviewed in detail

---

### 14.5 JavaScript Files

#### `js/validate.js`
**Purpose**: Registration form validation
**Functions**:
- `validateForm()`: Master validation function

**Validations**:
- Full name presence
- Personal ID length (min 16)
- Phone format (Rwandan)
- Email format
- Password length (min 6)

**Usage**: Loaded in `register.html`

---

## 15. Future Enhancement Recommendations

### 15.1 Security Enhancements

1. **Implement CSRF Protection**
   - Add token generation in session
   - Include tokens in all forms
   - Validate on server side

2. **Add XSS Protection**
   - Use `htmlspecialchars()` on all output
   - Implement Content Security Policy headers
   - Sanitize user input

3. **Improve Access Control**
   - Implement role-based permissions (Client vs. Staff vs. Admin)
   - Separate staff authentication from client authentication
   - Add admin interface with proper authorization

4. **Enhance Password Security**
   - Add password strength requirements
   - Implement password reset functionality
   - Add "Remember Me" functionality securely
   - Consider multi-factor authentication

5. **Add Rate Limiting**
   - Prevent brute force login attempts
   - Limit registration attempts
   - Add CAPTCHA for public forms

---

### 15.2 Functionality Enhancements

1. **Booking Management**
   - View active bookings
   - Cancel or modify bookings
   - Booking approval workflow
   - Booking status tracking (Pending, Approved, Completed, Cancelled)
   - Email notifications for booking confirmation

2. **Product Inventory**
   - Real-time availability checking
   - Prevent overbooking
   - Product images
   - Product categories and filtering
   - Pricing display and calculation
   - Product search functionality

3. **Return Management**
   - Link returns to specific bookings
   - Track return conditions
   - Damage assessment
   - Late return penalties
   - Return approval process

4. **Reporting & Analytics**
   - Client booking history
   - Revenue reports
   - Popular products analysis
   - Missing items trends
   - Employee performance metrics
   - Export reports to PDF/Excel

5. **Payment Integration**
   - Online payment gateway integration
   - Invoice generation
   - Payment history
   - Deposit and refund management
   - Receipt generation

---

### 15.3 User Experience Improvements

1. **Dashboard Enhancements**
   - Show upcoming bookings
   - Display booking statistics
   - Quick action buttons
   - Notification center
   - Profile management

2. **Form Improvements**
   - Product images in selection forms
   - Real-time availability display
   - Autocomplete for employee names
   - Date picker enhancements
   - Multi-step booking wizard

3. **Navigation**
   - Breadcrumb navigation
   - Highlight active page
   - Search functionality
   - Mobile-responsive navigation

4. **Feedback**
   - Toast notifications instead of alerts
   - Loading indicators
   - Form submission progress
   - Success/error message styling

---

### 15.4 Technical Improvements

1. **Code Organization**
   - Implement MVC framework (Laravel, CodeIgniter)
   - Use namespaces and autoloading
   - Centralize configuration
   - Create reusable components
   - Implement dependency injection

2. **Database Optimization**
   - Add database indexes
   - Optimize complex queries
   - Implement database caching
   - Add audit tables for tracking changes
   - Regular optimization and maintenance scripts

3. **API Development**
   - Create RESTful API
   - JSON responses
   - API authentication (OAuth, JWT)
   - Mobile app support
   - Third-party integrations

4. **Testing**
   - Unit tests
   - Integration tests
   - Automated testing
   - Load testing
   - Security testing

5. **Error Handling**
   - Centralized error logging
   - Custom error pages
   - Email alerts for critical errors
   - Debug mode toggle
   - Error tracking service integration

---

### 15.5 Scalability Improvements

1. **Performance**
   - Implement caching (Redis, Memcached)
   - Use CDN for static assets
   - Image optimization
   - Database query optimization
   - Lazy loading

2. **Infrastructure**
   - Use environment-based configuration
   - Implement proper backup system
   - Add monitoring tools
   - Load balancing for high traffic
   - Database replication

3. **Code Management**
   - Version control (Git)
   - Code review process
   - Continuous integration/deployment
   - Documentation generation
   - Coding standards enforcement

---

### 15.6 Feature Additions

1. **Client Portal**
   - Profile editing
   - Order history
   - Wishlist/favorites
   - Reviews and ratings
   - Referral program

2. **Staff Portal**
   - Shift management
   - Task assignments
   - Internal messaging
   - Inventory alerts
   - Performance dashboards

3. **Admin Portal**
   - User management
   - Product management
   - Dynamic pricing
   - Promotions/discounts
   - System settings
   - Backup and restore

4. **Communication**
   - Email notifications
   - SMS notifications
   - In-app messaging
   - Push notifications
   - Newsletter system

5. **Multi-language Support**
   - Kinyarwanda locale
   - French locale
   - English locale
   - Dynamic language switching

---

### 15.7 Business Logic Enhancements

1. **Booking Rules**
   - Minimum booking duration
   - Advance booking requirements
   - Blackout dates
   - Peak season pricing
   - Bulk booking discounts

2. **Inventory Management**
   - Automated stock alerts
   - Reorder points
   - Supplier management
   - Maintenance tracking
   - Asset depreciation

3. **Customer Relationship**
   - Loyalty programs
   - Customer tiers (Bronze, Silver, Gold)
   - Personalized offers
   - Birthday/anniversary reminders
   - Feedback collection

---

### 15.8 Compliance & Legal

1. **Data Protection**
   - GDPR compliance (if applicable)
   - Data encryption at rest
   - Data export functionality
   - Right to be forgotten implementation
   - Privacy policy display

2. **Terms & Conditions**
   - Rental agreements
   - Digital signature collection
   - Terms acceptance tracking
   - Liability waivers

3. **Auditing**
   - Action logging
   - Change tracking
   - Compliance reporting
   - Access logs

---

## 16. Known Issues & Limitations

### 16.1 Current Issues

1. **Authentication Inconsistency**
   - Some pages enforce authentication, others don't
   - No role separation between clients and staff
   - Stock management accessible to anyone

2. **No Booking-Return Linkage**
   - Returns are independent of bookings
   - Cannot verify if returned items were actually booked
   - No tracking of unreturned items

3. **Product List Hardcoded**
   - Product list in JavaScript instead of database-driven
   - Adding new products requires code changes
   - Product IDs must match database exactly

4. **Limited Error Handling**
   - Database errors exposed to users
   - No graceful degradation
   - No user-friendly error pages

5. **No Data Validation**
   - Insufficient server-side validation
   - Can submit invalid quantities
   - No duplicate booking prevention

6. **Session Security**
   - No session timeout
   - No session regeneration
   - Vulnerable to session fixation

---

### 16.2 Limitations

1. **Single Language**
   - English only interface
   - No localization support

2. **No Mobile App**
   - Web-only access
   - Basic mobile responsiveness

3. **No Real-time Updates**
   - Manual page refresh required
   - No WebSocket support

4. **Static Product Catalog**
   - Cannot add products without database access
   - No product management interface

5. **Basic Reporting**
   - Only stock comparison available
   - No booking reports
   - No revenue analytics

6. **No Payment Processing**
   - Manual payment handling required
   - No invoice generation

---

## 17. Deployment Checklist

### Pre-Deployment

- [ ] Database created with all tables
- [ ] Database credentials configured
- [ ] Initial products inserted
- [ ] Gallery images uploaded
- [ ] Background images uploaded
- [ ] File permissions set correctly
- [ ] Web server configured
- [ ] PHP version verified (7.0+)
- [ ] MySQLi extension enabled

### Security

- [ ] Change default database password
- [ ] Disable error display in production
- [ ] Enable error logging
- [ ] Set session cookie to HttpOnly
- [ ] Set session to use_only_cookies
- [ ] Implement HTTPS
- [ ] Add .htaccess protection for includes/
- [ ] Remove test accounts
- [ ] Validate all inputs server-side

### Testing

- [ ] Test registration flow
- [ ] Test login flow
- [ ] Test logout flow
- [ ] Test booking creation
- [ ] Test return submission
- [ ] Test missing item report
- [ ] Test stock report submission
- [ ] Test stock comparison view
- [ ] Test all navigation links
- [ ] Test form validations
- [ ] Test on multiple browsers
- [ ] Test mobile responsiveness

### Monitoring

- [ ] Set up error logging
- [ ] Configure backup system
- [ ] Monitor disk space
- [ ] Monitor database performance
- [ ] Set up uptime monitoring

---

## 18. Conclusion

This Decoration Rental Project is a functional web application that successfully digitizes the core operations of a decoration rental business. It provides essential features for client management, product bookings, returns, missing item reporting, and staff stock management.

### Strengths:
- Simple, straightforward implementation
- Core business processes covered
- Basic security measures in place (password hashing, prepared statements)
- Clean separation of concerns (includes, CSS, JS)

### Areas for Improvement:
- Security hardening (CSRF, XSS, access control)
- Enhanced user experience
- More robust validation
- Improved error handling
- Role-based permissions
- Real-time inventory management

### Development Recommendations:
For new developers taking over or reimplementing this system, consider:
1. Migrating to a modern PHP framework (Laravel, Symfony)
2. Implementing proper MVC architecture
3. Adding comprehensive testing
4. Creating a proper admin interface
5. Implementing the enhancements listed in Section 15

This documentation provides all the necessary information to understand, maintain, modify, or completely reimplement this decoration rental management system.

---

**Document Version**: 1.0  
**Last Updated**: March 3, 2026  
**Prepared For**: Development Team / Future Maintainers  
**Project**: NGTRUST LTD / DecoRwanda Decoration Rental System
