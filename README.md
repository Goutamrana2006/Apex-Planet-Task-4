

# 🔐 Secure Blog Management System

### ApexPlanet Web Development Internship — Task 4

A PHP and MySQL-based blog application enhanced with security-focused practices, including secure session handling, CSRF protection, input validation, and safer database operations.

**Building Better Web Applications Through Secure Coding**



---

## 📌 Project Overview

This project was developed as part of the **ApexPlanet Software Pvt. Ltd. Web Development Internship**.

The application demonstrates a blog management system built with PHP and MySQL. Task 4 focuses on strengthening the application's security by applying secure coding practices to authentication, session management, form submissions, database queries, and user-generated content.

The project uses XAMPP for local development and testing.

## ✨ Key Features

### 🔐 Authentication and Session Security
- User registration and login.
- Password hashing and verification.
- Session-based authentication.
- Session ID regeneration after successful login.
- Session cleanup and logout handling.
- Authentication checks for protected pages.

### 🛡️ Security Enhancements
- CSRF token generation and validation for protected operations.
- Prepared SQL statements for queries involving user input.
- Server-side validation of submitted data.
- HTML output escaping to help prevent cross-site scripting (XSS).
- POST-based deletion of blog posts.
- Improved error handling to avoid exposing internal database details.

### 📝 Blog Management
- Create blog posts.
- View available posts.
- Edit existing posts.
- Delete posts through protected operations.
- Validate post IDs and required fields.

### 🎨 User Interface
- Login and registration pages.
- Blog post listing and management pages.
- CSS-based styling.
- User feedback for supported operations.

*Features should be verified against the actual code and tested application before being considered fully implemented.*

---

## 🧰 Technology Stack

| Technology | Purpose |
|---|---|
| HTML5 | Web page structure |
| CSS3 | Styling and layout |
| JavaScript | Client-side interactions, where implemented |
| PHP | Server-side application logic |
| MySQL | Database management |
| Apache | Local web server |
| XAMPP | Local development environment |
| Visual Studio Code | Code editor |
| Git | Version control |
| GitHub | Source code hosting |

---

## 📁 Project Structure

```text
Apex-Planet-Task-4/
│
├── auth/
│   ├── login.php
│   ├── register.php
│   └── logout.php
│
├── config/
│   └── database.php
│
├── posts/
│   ├── index.php
│   ├── create.php
│   ├── edit.php
│   └── delete.php
│
├── index.php
├── style.css
└── README.md
```

The structure above is a representative layout. Adjust it to match the actual files in your Task 4 repository.

---

## 🔒 Security Implementation

### 1. Password Security
Passwords should be hashed using PHP's `password_hash()` function and verified using `password_verify()` rather than stored as plaintext.

### 2. SQL Injection Prevention
Prepared statements are used for database queries involving user-provided values, reducing the risk of SQL injection.

### 3. Session Security
Session-based authentication protects restricted pages. Regenerating the session ID after successful login helps mitigate session fixation risks.

### 4. Cross-Site Request Forgery (CSRF) Protection
CSRF tokens should be generated using a cryptographically secure random generator and validated on protected state-changing requests.

### 5. Cross-Site Scripting (XSS) Prevention
User-generated content should be escaped before being inserted into HTML output, using functions such as `htmlspecialchars()` with appropriate encoding settings.

### 6. Input Validation
Server-side validation checks submitted values, required fields, and post identifiers before processing operations.

### 7. Safer Post Deletion
Post deletion should use a POST request, require appropriate authorization, and validate a CSRF token.

**Security note:** These practices help reduce common vulnerabilities, but they do not guarantee that an application is completely secure. Test all protected operations and verify authorization rules before deployment.

---

## ⚙️ Requirements

- Windows, Linux, or macOS.
- XAMPP with Apache, PHP, and MySQL/MariaDB.
- A modern web browser.
- Visual Studio Code or another code editor.
- Git, if using GitHub for version control.

---

## 🚀 Installation and Setup

### Step 1: Clone the repository

Replace the repository URL below if your Task 4 repository uses a different name.

```bash
git clone https://github.com/Goutamrana2006/Apex-Planet-Task-4.git
```

Navigate to the project directory:

```bash
cd Apex-Planet-Task-4
```

### Step 2: Place the project in XAMPP

For a standard Windows installation, place the project in:

```text
C:\xampp\htdocs\Apex-Planet-Task-4
```

Ensure that the project files and directories are present.

### Step 3: Start Apache and MySQL

1. Open the XAMPP Control Panel.
2. Start Apache.
3. Start MySQL.
4. Confirm that both services are running.

### Step 4: Create the database

Open phpMyAdmin:

```text
http://localhost/phpmyadmin/
```

Create the database required by your application.

If your configuration uses the database name `blog`, create a database named `blog`.

Create the required tables using the schema expected by your PHP code.

### Step 5: Configure the database

Open:

```text
config/database.php
```

Configure the database host, username, password, and database name to match your local environment.

Do not publish real database credentials or other secrets to GitHub.

### Step 6: Run the application

Open the corresponding local URL in your browser:

```text
http://localhost/Apex-Planet-Task-4/
```

Register an account, log in, and test the available functionality.

---

## 🧪 Testing Checklist

### Authentication
- [ ] Register a new account.
- [ ] Test duplicate username handling.
- [ ] Log in with valid credentials.
- [ ] Confirm invalid credentials are rejected.
- [ ] Verify protected pages require authentication.
- [ ] Test logout and session cleanup.

### Blog Management
- [ ] Create a blog post.
- [ ] View the created post.
- [ ] Edit an existing post.
- [ ] Delete a post.
- [ ] Test invalid post IDs and form inputs.
- [ ] Verify that users cannot modify posts they do not own, if ownership restrictions apply.

### Security
- [ ] Verify password hashing.
- [ ] Test prepared database queries.
- [ ] Test CSRF validation on protected operations.
- [ ] Check HTML output escaping.
- [ ] Verify session ID regeneration after login.
- [ ] Confirm internal database errors are not exposed to users.
- [ ] Verify logout invalidates the authenticated session.

### Interface
- [ ] Check the login and registration pages.
- [ ] Test the blog management interface.
- [ ] Verify success and error feedback.
- [ ] Check layouts on desktop and mobile screens.

Complete each checklist item only after testing it.

---

## 📚 Learning Outcomes

This task provided an opportunity to practise:

- Secure PHP application development.
- Session-based authentication.
- Password hashing and verification.
- SQL injection prevention using prepared statements.
- CSRF protection.
- XSS prevention through output escaping.
- Server-side input validation.
- Secure handling of post-management operations.
- Database integration with MySQL.
- Debugging and testing with XAMPP.
- Version control using Git and GitHub.

---

## 🔮 Future Improvements

Potential enhancements include:

- More comprehensive authorization and ownership controls.
- Password recovery.
- Improved form validation and accessibility.
- Automated security testing.
- Additional responsive-design improvements.
- Deployment to a properly configured production environment.

These are potential improvements rather than claims of existing functionality.

---

## 🏢 Internship Information

| Detail | Information |
|---|---|
| Organization | ApexPlanet Software Pvt. Ltd. |
| Program | Web Development Internship |
| Task | Task 4 — Security Enhancements |
| Project | Secure Blog Management System |
| Primary Technologies | PHP, MySQL, HTML5, CSS3, JavaScript |
| Development Environment | XAMPP and Visual Studio Code |
| Version Control | Git and GitHub |

---

## 🙏 Acknowledgment

I would like to thank **ApexPlanet Software Pvt. Ltd.** for providing the opportunity to develop practical web development skills through hands-on internship tasks.

This project represents my learning journey in PHP development, database integration, and security-conscious web application development.

---



**Developed by Goutam Rana**

*Learning • Building • Improving*

