# Event Scoring System

This is a simple event scoring system built using the LAMP stack (Linux, Apache, MySQL, PHP) for a local development environment. It allows admins to add judges, judges to score users, and displays a dynamic scoreboard with real-time updates.

## Setup Instructions

### Install XAMPP

- Download and install XAMPP from https://www.apachefriends.org/.
- Start the Apache and MySQL modules from the XAMPP control panel.

### Set Up the Database

1. Open phpMyAdmin in your browser (e.g., http://localhost/phpmyadmin).
2. Create a new database named `event_scoring`.
3. Run the following SQL to create tables and insert initial data:

```sql
CREATE TABLE judges (
    id VARCHAR(50) PRIMARY KEY,
    display_name VARCHAR(100)
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100)
);

CREATE TABLE scores (
    judge_id VARCHAR(50),
    user_id INT,
    points INT,
    PRIMARY KEY (judge_id, user_id),
    FOREIGN KEY (judge_id) REFERENCES judges(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO users (name) VALUES ('Alex'), ('Mitchel'), ('Dennis'), ('David'), ('Bravin');
```

4. Update the MySQL user privileges (e.g., for the root user with password `newpassword`):

```sql
ALTER USER 'root'@'localhost' IDENTIFIED BY 'newpassword';
GRANT ALL PRIVILEGES ON event_scoring.* TO 'root'@'localhost';
FLUSH PRIVILEGES;
```

### Deploy the Application

- Copy all project files (`index.php`, `admin.php`, `judge.php`, `scoreboard.php`, `logout.php`, `db.php`, `style.css`) directly to the `htdocs` folder of your XAMPP installation (e.g., `C:\xampp\htdocs\`).
- Access the home page at `http://localhost/index.php`.

## Usage

### Home Page

Visit `http://localhost/index.php` to see the landing page with links to other sections.

### Admin Panel

Go to `http://localhost/admin.php` to add judges by entering a unique Judge ID and Display Name.

### Judge Portal

Access `http://localhost/judge.php`, enter a Judge ID (previously added via Admin Panel), and score users (1-100 points). Judges can also delete scores with a toast confirmation. Success and error messages auto-disappear after 5 seconds.

### Public Scoreboard

View the dynamic scoreboard at `http://localhost/scoreboard.php`, which updates every 5 seconds via AJAX.

### Logout

Judges can log out from any page (if logged in) via the navbar, redirecting to `http://localhost/logout.php`.

## Database Schema

### Judges

- `id` (VARCHAR(50), PRIMARY KEY): Unique identifier for the judge.
- `display_name` (VARCHAR(100)): Judge's display name.

### Users

- `id` (INT, AUTO_INCREMENT, PRIMARY KEY): Unique identifier for the user.
- `name` (VARCHAR(100)): User's name.

### Scores

- `judge_id` (VARCHAR(50), FOREIGN KEY): References `judges(id)`.
- `user_id` (INT, FOREIGN KEY): References `users(id)`.
- `points` (INT): Score assigned by the judge to the user (1-100).
- Composite PRIMARY KEY (`judge_id`, `user_id`): Ensures each judge scores each user only once.

## Assumptions

- No login system is required for admins or judges; Judge IDs are manually validated.
- Users are pre-registered and manually added to the `users` table for simplicity.
- Each judge can assign points to each user only once, but scores can be updated.
- Points must be numerical and between 1 and 100.
- The scoreboard highlights top scorers with "gold," "silver," and "bronze" styling for the top three users based on average scores.

## Design Choices

### Database Structure

- Used a relational schema with foreign keys for data integrity.
- The composite primary key in `scores` ensures unique judge-user scores, with `ON DUPLICATE KEY UPDATE` for score updates.

### PHP Constructs

- Utilized PDO for secure database interactions and prepared statements to prevent SQL injection.

### Dynamic Updates

- Implemented AJAX with jQuery to refresh the scoreboard every 5 seconds, providing real-time updates without full page reloads.

### Sessions

- Used PHP sessions to track judge identity in the Judge Portal, enabling a simple logout mechanism.

### Frontend

- Kept the design simple with custom CSS (no framework) for a lightweight experience.
- Styled the navbar with a black background, white links, orange hover/active states, and a red logout link.
- Replaced `<li>` tags in the navbar with `<div class="nav-item">` to avoid numbers in tags, maintaining consistent styling.
- Added toast notifications for delete confirmations in the Judge Portal, with a centered modal-like design.
- Implemented a 5-second timeout for success/error messages in the Judge Portal, fading them out smoothly.
- Ensured responsive design with a collapsible navbar and adjusted form widths for mobile devices (< 600px).

### Relative Paths

- Used relative paths for all file references (e.g., `href="judge.php"`, `src="style.css"`) to ensure flexibility in deployment location.

## LAMP Stack Integration

- **Linux**: Assumed as the OS via XAMPP (also compatible with Windows/Mac).
- **Apache**: Serves PHP files and handles HTTP requests (e.g., form submissions, page loads).
- **MySQL**: Stores and retrieves data efficiently, queried via PDO in PHP.
- **PHP**: Processes requests, interacts with MySQL, and generates dynamic HTML content.

## Optional Features (If More Time)

- Secure login for admins and judges with password hashing and role-based access.
- User registration interface for admins to add users dynamically.
- Real-time updates using WebSockets instead of AJAX polling for better performance.
- Enhanced responsive design with a CSS framework (e.g., Bootstrap) for a polished look.
- Detailed score history and judge-specific views (e.g., scores per judge).
- Input validation for special characters in Judge IDs and Display Names.
- A progress bar for message timeouts in the Judge Portal.

## Publicly Accessible Link

This is a local development project hosted on GitHub for review: https://github.com/alexnasir/-Event-Scoring-System.git.To preview locally, follow the setup instructions above. For a live demo: 


