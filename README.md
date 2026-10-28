InkSpace — Blog Management System
A secure, database-driven blog management web application developed as part of the ApexPlanet Software Pvt. Ltd. Web Development Internship — Task 5.
InkSpace allows users to register, log in, create and manage blog posts, search published content, and navigate posts using pagination. The project demonstrates practical web development with PHP, MySQL, HTML, CSS, and JavaScript, along with authentication, CRUD operations, and security-conscious coding practices.
> **Project:** InkSpace — Blog Management System
> **Internship:** ApexPlanet Software Pvt. Ltd. — Web Development Internship
> **Current Task:** Task 5
> **Development Environment:** Windows + XAMPP

Table of Contents
Project Overview
Key Features
Development Progress: Tasks 1–5
Technology Stack
Project Structure
Requirements
Installation and Setup
Database Structure
Security Practices
Testing Checklist
Learning Outcomes
Future Improvements
Internship Information

Project Overview
InkSpace is a web-based blogging application designed to demonstrate the development of a database-driven website using PHP and MySQL.
Users can create an account, authenticate, publish blog posts, explore available posts, search content, and edit or delete posts through the application interface. The project was developed incrementally, starting with Git and GitHub setup and progressing through authentication, CRUD functionality, search, pagination, interface improvements, and security enhancements.
The application is developed and tested locally using XAMPP.
Key Features
User Authentication
User registration with a username and password.
Password hashing with PHP's `password_hash()`.
Password verification with `password_verify()`.
Session-based authentication.
Session ID regeneration after successful login.
Logout functionality that clears the session.
Blog Management
Create new blog posts.
View posts in the Explore Posts dashboard.
Edit existing posts.
Delete posts using a POST request.
Display post titles, content, and publication dates.
Search and Navigation
Search posts by title or content.
Paginate the list of posts.
Handle empty search results.
Validate pagination input.
User Interface
Shared CSS styling for a consistent visual experience.
Styled registration and login pages.
Responsive styling for the post dashboard and article forms.
Success and error feedback for supported operations.
Confirmation before deleting a post.
Security-Oriented Implementation
Prepared SQL statements for queries involving user input.
HTML output escaping using `htmlspecialchars()`.
CSRF token validation for protected post operations.
Server-side validation of submitted values.
Authentication checks on protected pages.
> **Note:** The feature list describes functionality implemented or worked on during development. Use the testing checklist below to verify the behavior of the current version before describing every item as fully tested.
---
Development Progress: Tasks 1–5
Task 1 — Git and GitHub Setup
Created and configured a GitHub repository.
Initialized Git version control.
Added project files to the repository.
Practiced committing and pushing code to GitHub.
Task 2 — Authentication and CRUD Operations
Implemented user registration and login.
Added password hashing and password verification.
Introduced session-based authentication.
Connected PHP to a MySQL database.
Implemented blog post creation, listing, editing, and deletion.
Used prepared statements for database queries involving user input.
Task 3 — Search, Pagination, and UI Improvements
Added search by post title and content.
Implemented pagination for the post listing.
Added navigation between result pages.
Improved page layout and styling with CSS.
Displayed post titles, content, and publication information.
Worked on empty-result handling and invalid pagination input.
Task 4 — Security Enhancements
Security-focused improvements included:
Checking authentication before serving protected pages.
Regenerating the session ID after successful login.
Clearing session data and destroying the session during logout.
Generating CSRF tokens with PHP's `random_bytes()`.
Validating submitted CSRF tokens with `hash_equals()`.
Escaping user-generated content before rendering it in HTML.
Using prepared statements for user-supplied SQL values.
Validating post IDs and required fields.
Using POST requests for post deletion.
Task 5 — UI Refinement, Feedback, and Finalization
Work on Task 5 has focused on refining the existing application and preparing it for review:
Improved styling for the Create Article and Edit Article pages.
Refined the Explore Posts dashboard layout.
Added success and error notifications for supported post operations.
Added automatic dismissal behavior for notifications.
Reviewed the registration and login page styling.
Continued checking search, pagination, and post-management workflows.
Prepared project documentation and a final testing checklist.
Task 5 should be considered complete only after the required internship deliverables and the current implementation have been tested and verified.
---
Technology Stack
Category    Technology  Purpose
Frontend    HTML5   Page structure and forms
Styling CSS3    Layout, visual design, and responsive styling
Client-side behavior    JavaScript  Interactive behavior and notification dismissal
Backend PHP Server-side logic, authentication, and CRUD operations
Database    MySQL   Persistent storage for users and posts
Local server    XAMPP (Apache and MySQL)    Local development environment
Code editor Visual Studio Code  Development and debugging
Version control Git Tracking code changes
Repository hosting  GitHub  Hosting and sharing the source code
---
Project Structure
The main application structure is expected to resemble the following. The actual files in the repository may differ as development continues.
```text

ApexPlanet-Task-5/

│

├── auth/

│   ├── register.php

│   ├── login.php

│   └── logout.php

│

├── config/

│   └── database.php

│

├── posts/

│   ├── index.php

│   ├── create.php

│   ├── edit.php

│   ├── delete.php

│   ├── posts.css

│   ├── editor.css

│   └── edit.css

│

├── index.php

├── style.css

└── README.md

```
Keep the structure in this document aligned with the actual repository. Do not commit local configuration secrets, database passwords, or unnecessary environment files.
---
Requirements
Install or have access to:
Windows, Linux, or macOS.
XAMPP with Apache, PHP, and MySQL/MariaDB.
A web browser.
Visual Studio Code or another code editor.
Git (optional, for cloning and version control).
---
Installation and Setup
1. Clone the repository
If the project repository is named `ApexPlanet-Task-5`, run:
```bash

git clone https://github.com/Goutamrana2006/ApexPlanet-Task-5.git

```
If the repository has a different name or URL, replace the command with the correct repository address.
Alternatively, download the repository as a ZIP file and extract it.
2. Place the project in XAMPP
Copy the project folder into the XAMPP web root. For the folder name shown above, the path is:
```text

C:\xampp\htdocs\ApexPlanet-Task-5

```
Ensure that the folder contains `index.php`, `auth/`, `config/`, and `posts/`.
3. Start Apache and MySQL
Open the XAMPP Control Panel.
Start Apache.
Start MySQL.
Confirm that both services start successfully.
4. Create the database
Open phpMyAdmin:
```text

http://localhost/phpmyadmin

```
Create a database named:
```text

blog

```
The application expects a `users` table and a `posts` table. Use the actual schema and constraints from your project when creating them; see Database Structure.
5. Configure the database connection
Open:
```text

config/database.php

```
Set the MySQL host, database name, username, and password to match your local XAMPP configuration.
For a typical local XAMPP installation, the database host is often `localhost` and the database name is `blog`. Use the credentials configured on your own machine.
Never commit real passwords, API keys, or other private credentials to GitHub.
6. Run the application
Open the local project URL in your browser:
```text

http://localhost/ApexPlanet-Task-5/

```
Register an account, log in, and test the available blog-management features.
---
Database Structure
The application code shared during development uses a `users` table and a `posts` table. The following describes the columns referenced by the shared code; confirm the complete column types, indexes, constraints, and relationships against the database in your current project.
`users` table
Column  Purpose
`id`    Unique user identifier
`username`  Username used for registration and login
`password`  Hashed password
Recommended database considerations:
Make `id` a primary key.
Add a unique constraint to `username` if usernames must be unique.
Store password hashes in a column large enough for hashes produced by PHP's password-hashing API.
`posts` table
Column  Purpose
`id`    Unique post identifier
`title` Blog post title
`content`   Blog post body
`created_at`    Post creation timestamp
Important: The post queries shared during development select `id`, `title`, `content`, and `created_at`. They do not establish that the current `posts` table has an author or `user_id` column. If the application is intended to support private ownership of posts, review and, if needed, migrate the schema to associate each post with its author before enforcing ownership rules.
---
Security Practices
InkSpace includes security-oriented measures intended to reduce common web application risks.
Password Handling
Uses `password_hash()` when registering users.
Uses `password_verify()` when authenticating users.
Does not intentionally store plaintext passwords.
Session Management
Uses PHP sessions to track authenticated users.
Regenerates the session ID after successful login.
Protects pages by checking for an authenticated user.
The logout implementation is intended to clear session data and destroy the session.
SQL Injection Prevention
Uses prepared statements for queries involving user-supplied values.
Binds values to SQL parameters rather than concatenating those values into query strings.
Cross-Site Scripting (XSS) Prevention
Escapes values displayed in HTML with `htmlspecialchars()`.
The shared post listing uses `ENT_QUOTES` and `ENT_SUBSTITUTE` with UTF-8 output encoding.
Cross-Site Request Forgery (CSRF) Protection
Generates session-based tokens with `random_bytes()`.
Includes tokens in protected post forms.
Validates submitted tokens using `hash_equals()`.
Rejects invalid or missing tokens before protected post changes are performed.
Input Validation and Deletion
Checks required form fields on the server.
Validates post IDs as integers.
Rejects empty post titles and content in the relevant post workflows.
Uses POST for deletion rather than a direct GET-based delete link.
Security Limitations and Verification
These measures improve security but do not guarantee that the application is free of vulnerabilities. Before submission, verify authorization for every post operation, session-cookie configuration, CSRF coverage across all state-changing forms, database constraints, and handling of unexpected input.
In particular, authentication and ownership are different controls: requiring a user to log in does not by itself prevent that user from editing or deleting another user's post. Confirm that the application enforces ownership if that is a requirement.
---
Testing Checklist
Use this checklist before the final submission.
Authentication
[ ] Register with valid information.
[ ] Attempt to register an existing username.
[ ] Log in with valid credentials.
[ ] Confirm invalid credentials are rejected.
[ ] Confirm protected pages redirect unauthenticated users.
[ ] Log out and verify the session is no longer authenticated.
Blog Operations
[ ] Create a post with a valid title and content.
[ ] Confirm the new post appears in the listing.
[ ] Edit a post and confirm the changes are displayed.
[ ] Delete a post and confirm it is removed.
[ ] Confirm invalid post IDs are handled safely.
[ ] Verify that users cannot modify posts they do not own, if ownership is part of the application requirements.
Search and Pagination
[ ] Search by a matching title.
[ ] Search by matching content.
[ ] Search for text that returns no results.
[ ] Navigate between pagination pages.
[ ] Test invalid or out-of-range page values.
Security and Interface
[ ] Verify password hashes are stored instead of plaintext passwords.
[ ] Verify CSRF validation for each protected state-changing form.
[ ] Confirm user-generated text is safely escaped when rendered.
[ ] Confirm database operations use appropriate prepared statements.
[ ] Check success and error notifications.
[ ] Check automatic notification dismissal.
[ ] Test the layout on desktop and mobile screen sizes.
[ ] Test the setup instructions using a clean database when possible.
Mark an item complete only after you have tested it.
---
Learning Outcomes
During the development of InkSpace, the project provided practice in:
Building web pages with HTML and CSS.
Writing server-side PHP code.
Handling form submissions and validating input.
Connecting PHP applications to MySQL.
Implementing CRUD operations.
Working with password hashing and session-based authentication.
Using prepared SQL statements.
Applying CSRF protection and HTML output escaping.
Debugging a local application with XAMPP.
Tracking changes with Git and publishing source code on GitHub.
Improving UI consistency and preparing technical documentation.
---
Future Improvements
Possible future enhancements include:
A dedicated article details page.
A My Articles page for each user's own posts.
Author profiles and accurate author attribution.
Categories and tags.
Comments and likes.
Password visibility controls.
A password recovery workflow.
Further accessibility and responsive-design improvements.
These are optional ideas and are not represented as completed features.
---
Internship Information
Organization: ApexPlanet Software Pvt. Ltd.
Program: Web Development Internship
Project: InkSpace — Blog Management System
Current Task: Task 5
Primary Technologies: PHP, MySQL, HTML5, CSS3, JavaScript
Development Environment: XAMPP and Visual Studio Code
This project is developed for educational purposes as part of the ApexPlanet Web Development Internship.
---
Acknowledgment
Thank you to ApexPlanet Software Pvt. Ltd. for the opportunity to practice web development through progressive, hands-on internship tasks.
---