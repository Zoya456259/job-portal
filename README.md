
# Job Portal Management System

A PHP and MySQL web application where employers can post jobs, job seekers can apply for jobs, and an admin can manage users, categories, jobs, and applications. This project was built for learning and practicing **role-based access control (RBAC)** and **CRUD operations**.

## Features

### Admin
- View all registered users.
- Edit and delete Employer and Job Seeker accounts.
- Create, view, edit, and delete job categories.
- View all posted jobs and delete jobs when needed.
- View applications submitted across the portal.

### Employer
- Create job postings with a category, description, location, salary, and application deadline.
- View, edit, and delete their own job postings.
- View applications submitted to their jobs, including the applicant's name, email, and cover letter.
- Accept or reject applications.

### Job Seeker
- Create and update a profile with phone, education, skills, and experience.
- Browse jobs whose application deadlines have not passed.
- Apply to a job with a cover letter.
- View submitted applications and their statuses.

### General
- Session-based login and logout.
- Separate access checks for Admin, Employer, and Job Seeker pages.
- Password hashing with PHP's `password_hash()` and verification with `password_verify()`.
- Prepared statements for database operations.
- Shared CSS styling for the homepage, forms, dashboards, and tables.

## Technology Stack

- **Frontend:** HTML, CSS
- **Backend:** PHP
- **Database:** MySQL / MariaDB
- **Local development:** XAMPP (Apache and MySQL)

## Requirements

- XAMPP with Apache, MySQL, and PHP
- A web browser
- A code editor such as Visual Studio Code

## Installation and Setup

### 1. Place the project in XAMPP

Copy or clone this repository into the XAMPP web root. The expected Windows path is:

```text
C:\xampp\htdocs\job-portal
```

Start **Apache** and **MySQL** from the XAMPP Control Panel.

### 2. Create the database

1. Open http://localhost/phpmyadmin.
2. Open the **SQL** tab.
3. Run the following SQL to create the database and tables.

```sql
CREATE DATABASE IF NOT EXISTS job_portal;
USE job_portal;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'employer', 'job_seeker') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE job_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employer_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(100) NOT NULL,
    salary VARCHAR(100),
    deadline DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES job_categories(id) ON DELETE CASCADE
);

CREATE TABLE job_seeker_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    phone VARCHAR(20),
    education VARCHAR(150),
    skills TEXT,
    experience TEXT,
    profile_picture VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    job_seeker_id INT NOT NULL,
    cover_letter TEXT,
    cv VARCHAR(255),
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (job_seeker_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### 3. Configure the database connection

Open `config/database.php` and confirm the connection settings match your local MySQL setup:

```php
<?php

$host = "localhost";
$dbname = "job_portal";
$username = "root";
$password = ""; // Default XAMPP setup; update if your MySQL password is different.

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

?>
```

### 4. Create an Admin account

Public registration is intended for Employers and Job Seekers. Create the first Admin account manually using a temporary PHP file so the password is stored as a hash.

Create `create_admin.php` in the project root (`job-portal/create_admin.php`) with the following code. **Change the password before running it.**

```php
<?php

require_once "config/database.php";

$name = "Admin";
$email = "admin@jobportal.com";
$password = "REPLACE_WITH_A_STRONG_PASSWORD"; // Change this first.
$role = "admin";
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

if ($stmt->execute()) {
    echo "Admin account created. Delete create_admin.php now.";
} else {
    echo "Could not create Admin account. Check whether the email already exists.";
}

?>
```

1. Replace `REPLACE_WITH_A_STRONG_PASSWORD` with your own password.
2. Open http://localhost/job-portal/create_admin.php once.
3. After the account is created, **delete `create_admin.php` immediately**.
4. Log in with `admin@jobportal.com` and the password you chose.

**Security:** Never commit a real password or an admin-creation script containing your password to GitHub. If you use this temporary setup script, delete it after creating the account and before uploading the project.

### 5. Run the project

Open the homepage:

http://localhost/job-portal/

Use **Create an Account** to register an Employer or Job Seeker account, then log in. Add categories from the Admin panel before creating jobs.

## Main Project Structure

```text
job-portal/
├── admin/
│   ├── categories/
│   │   ├── create.php
│   │   ├── edit.php
│   │   ├── delete.php
│   │   └── index.php
│   ├── applications.php
│   ├── dashboard.php
│   ├── delete_job.php
│   ├── delete_user.php
│   ├── edit_user.php
│   ├── jobs.php
│   └── users.php
├── assets/
│   └── css/
│       └── style.css
├── authentication/
│   ├── login.php
│   ├── logout.php
│   └── register.php
├── config/
│   └── database.php
├── employer/
│   ├── jobs/
│   │   ├── create.php
│   │   ├── delete.php
│   │   ├── edit.php
│   │   └── index.php
│   ├── applications.php
│   ├── dashboard.php
│   └── update_application.php
├── job_seeker/
│   ├── applications.php
│   ├── apply.php
│   ├── browse_jobs.php
│   ├── dashboard.php
│   └── profile.php
├── index.php
└── README.md
```

## Application Workflow

1. An Admin signs in and creates job categories.
2. An Employer registers, signs in, and posts a job under a category.
3. A Job Seeker creates a profile and browses available jobs.
4. The Job Seeker submits an application with a cover letter.
5. The Employer reviews applications and accepts or rejects them.
6. The Job Seeker checks the application status from **My Applications**.
7. The Admin can review users, jobs, categories, and applications.

## Database Tables

| Table | Purpose |
|---|---|
| `users` | Stores account details and each user's role. |
| `job_categories` | Stores categories used to classify jobs. |
| `jobs` | Stores job posts and the employer/category relationships. |
| `job_seeker_profiles` | Stores profile details for Job Seekers. |
| `applications` | Stores applications, cover letters, and application statuses. |

## Notes

- This is a learning project intended for local development. Review security, validation, error handling, file permissions, and production configuration before deploying it publicly.
- Do not upload passwords, database credentials, or private user data to GitHub.
- The `cv` and `profile_picture` database fields are present in the schema, but CV upload was not implemented in the current application workflow.
- Deleting a user, job, or category may also delete related records because the database uses foreign keys with `ON DELETE CASCADE`. Use test data when trying deletion features.

## Future Improvements

- Add pagination and search/filter options.
- Improve validation and user-friendly error messages.
- Add password reset and email verification.
- Add CV upload with file type/size validation and secure storage.
- Add automated tests and improve production security.

## License

No license has been specified yet. Add a license file if you want others to be able to reuse, modify, or distribute this project under defined terms.


## Author 
Zoya Ali