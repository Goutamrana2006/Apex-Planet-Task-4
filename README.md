# ApexPlanet Internship – Blog Management System (Task 4):

A simple and secure blog management web application developed as part of the **ApexPlanet Software Pvt. Ltd. Web Development Internship**.

The project demonstrates PHP, MySQL, authentication, CRUD operations, search, pagination, and security improvements using a local XAMPP development environment.

## Project Overview

The Blog Management System allows users to register, log in, create blog posts, view posts, edit existing posts, and delete posts.

The project was developed in multiple tasks, progressing from basic Git and GitHub setup to database-driven functionality and security enhancements.

## Technologies Used

- **Frontend:** HTML5, CSS3
- **Backend:** PHP
- **Database:** MySQL
- **Local Server:** XAMPP (Apache and MySQL)
- **Development Environment:** Visual Studio Code
- **Version Control:** Git
- **Repository Hosting:** GitHub

## Features

### Task 1 – Git and GitHub Setup

- Created and configured a GitHub repository.
- Initialized Git version control for the project.
- Added project files to the repository.
- Practiced committing and pushing code to GitHub.

### Task 2 – Authentication and CRUD Operations

- User registration with username and password.
- Secure password storage using PHP password hashing.
- User login using password verification.
- Session-based authentication.
- Create new blog posts.
- View existing blog posts.
- Edit blog posts.
- Delete blog posts.
- MySQL database integration using PHP.
- Prepared statements for database queries involving user input.

### Task 3 – Search, Pagination, and UI Improvements

- Search blog posts using a search field.
- Display blog posts in a paginated format.
- Navigate between pages of blog posts.
- Improve the user interface using CSS.
- Display post titles, content, and relevant post information.
- Handle empty search results and invalid pagination inputs.

### Task 4 – Security Enhancements

The application includes security improvements to protect authentication, user input, and database operations.

#### 1. Session-Based Authentication

- Protected pages check whether the user is authenticated.
- Unauthenticated users are redirected to the login page.
- Login sessions store the authenticated user's ID and username.

#### 2. Session ID Regeneration

- The session ID is regenerated after successful password verification.
- This helps protect against session fixation attacks.

#### 3. Secure Logout

- Logout clears session variables.
- The session cookie is expired when cookies are enabled.
- The PHP session is destroyed.
- Users are redirected to the login page after logout.

#### 4. CSRF Protection

- The application uses session-based CSRF tokens for protected form submissions.
- Tokens are generated using PHP's cryptographically secure `random_bytes()` function.
- Forms include a hidden CSRF token field.
- Submitted tokens are checked against the session token using `hash_equals()`.
- Invalid or missing tokens are rejected before protected database changes are performed.

CSRF protection is implemented for the relevant post creation, editing, and deletion operations.

#### 5. XSS Prevention

- User-generated content is escaped before being displayed in HTML.
- PHP's `htmlspecialchars()` is used with `ENT_QUOTES` and `ENT_SUBSTITUTE`.
- This helps prevent user input from being interpreted as executable HTML or JavaScript.

#### 6. Prepared SQL Statements

- Prepared statements are used for database queries involving user-supplied values.
- User input is bound to SQL parameters instead of directly concatenated into queries.
- This helps prevent SQL injection attacks.

#### 7. Input Validation

- Required fields are checked before processing.
- Post IDs are validated as integers.
- Title and content lengths are restricted.
- Unexpected input types are rejected.
- Empty post titles and content are not accepted.

#### 8. Secure Post Deletion

- Post deletion uses a POST request instead of a direct GET link.
- The delete operation checks authentication and CSRF protection.
- Post IDs are validated before executing the deletion query.

## Project Structure

```text
ApexPlanet-Task-4/
│
├── auth/
│   ├── register.php
│   ├── login.php
│   └── logout.php
│
├── config/
│   └── database.php
│
├── posts/
│   ├── create.php
│   ├── index.php
│   ├── edit.php
│   └── delete.php
│
├── index.php
├── style.css
└── README.md
```

## Installation and Setup

### Prerequisites

Install the following:

- XAMPP
- Visual Studio Code
- Git

### Step 1 – Clone the Repository

Open a terminal and run:

```bash
git clone https://github.com/Goutamrana2006/ApexPlanet-Task-4.git
```

Alternatively, download the repository as a ZIP file and extract it.

### Step 2 – Place the Project in XAMPP

Copy the project folder into:

```text
C:\xampp\htdocs\ApexPlanet-Task-4
```

### Step 3 – Start Apache and MySQL

1. Open the XAMPP Control Panel.
2. Start Apache.
3. Start MySQL.

### Step 4 – Create the Database

Open phpMyAdmin:

```text
http://localhost/phpmyadmin
```

Create a database named:

```text
blog
```

The application requires the following tables.

**Users table**

| Column | Description |
|---|---|
| id | Unique user ID |
| username | User's unique username |
| password | Hashed password |

**Posts table**

| Column | Description |
|---|---|
| id | Unique post ID |
| title | Blog post title |
| content | Blog post content |
| created_at | Post creation timestamp |

Use the actual column types and constraints defined in your project's database schema.

### Step 5 – Configure Database Connection

Open:

```text
config/database.php
```

Configure the MySQL host, database name, username, and password according to your local XAMPP setup.

Do not commit real database passwords or other private credentials to GitHub.

### Step 6 – Run the Application

Open your browser and visit:

```text
http://localhost/ApexPlanet-Task-4/
```

Register a new account and log in to start using the blog management system.

## Security Testing

The following security checks are included in the project's development and testing process:

- Login and logout behavior.
- Redirecting unauthenticated users from protected pages.
- Session ID regeneration after successful login.
- XSS prevention through HTML output escaping.
- Prepared SQL statements for user-input queries.
- CSRF token validation for protected form submissions.
- Prevention of direct GET-based post deletion.

Security testing is an ongoing process. These measures improve application security but do not guarantee protection against every possible attack.

## Learning Outcomes

Through this project, I practiced:

- PHP programming and server-side form handling.
- MySQL database connectivity and CRUD operations.
- User authentication and session management.
- Prepared statements and input validation.
- CSRF protection and XSS prevention.
- Git version control and GitHub repository management.
- Testing and debugging a PHP application in XAMPP.

## Internship

**Organization:** ApexPlanet Software Pvt. Ltd.  
**Program:** Web Development Internship  
**Project:** Blog Management System

This project was developed for educational purposes as part of the internship tasks.


