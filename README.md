TAALIM: Assessment Management System
TAALIM is a centralized web-based platform designed to streamline assessment management in higher education. It facilitates the submission, review, and validation of assessment materials and student answer samples, ensuring a smooth workflow between Subject Coordinators, Subject Matter Experts (SME), and academic administrators.

Key Features
Role-Based Access Control: Secure modules tailored for Subject Coordinators, SME assessment verification, and administrators.

Centralized Submission: Streamlines the collection of assessment materials and student samples.

Workflow Tracking: Real-time assessment status tracking to ensure compliance and timely completion.

Authentication & Security: Robust user authentication, including secure password reset functionality.

Document Management: Efficient handling of academic records and supporting files.

🛠️ Tech Stack
Framework: Laravel (PHP)

Database: MySQL

Frontend Components: JavaScript, CSS (with Blade templating)

Environment: Node.js, npm

Installation & Setup
To run this project locally, ensure you have Composer and Node.js installed.

Clone the repository:

Bash


git clone https://github.com/your-username/taalim.git
cd taalim

2.  **Install PHP dependencies:**
    ```bash
composer install
Install frontend dependencies:

Bash


npm install && npm run dev

4.  **Configure environment:**
    *   Copy the example environment file: `cp .env.example .env`
    *   Update your database credentials in the `.env` file.

5.  **Run migrations:**
    ```bash
php artisan migrate
Serve the application:

php artisan serve
