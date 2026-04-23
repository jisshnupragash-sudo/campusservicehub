# Campus Service Hub

A GitHub-ready PHP + MySQL + Bootstrap mini project for **DFP40443 Full Stack Web Development**.

## Features
- User registration and login
- Password hashing and verification
- Admin and User roles
- Secure session handling
- CRUD for services
- Image upload with validation
- AJAX live search using `fetch()`
- Prepared statements with PDO
- XSS protection with `htmlspecialchars()`
- Responsive Bootstrap UI

## Folder Structure
- `config/` - database and session config
- `includes/` - reusable helpers, header, footer
- `auth/` - register, login, logout
- `services/` - CRUD + AJAX search
- `assets/` - CSS and JS
- `uploads/` - uploaded images
- `sql/` - database import file

## Setup Instructions
1. Copy the `campus-service-hub` folder into your XAMPP `htdocs` directory.
2. Start Apache and MySQL in XAMPP.
3. Open phpMyAdmin and import `sql/campus_service_hub.sql`.
4. Open `config/database.php` and confirm your DB credentials.
5. Visit `http://localhost/campus-service-hub/`.

## Demo Accounts
- Admin: `admin@example.com` / `password123`
- User: `user@example.com` / `password123`

## GitHub Compatibility
- Includes `.gitignore`
- Keeps `uploads/` tracked with `.gitkeep`
- Clean folder structure for direct push to GitHub

## Notes
- The app uses absolute paths assuming the project folder is named `campus-service-hub` under `htdocs`.
- For shared hosting, update the paths if your deployment URL changes.
