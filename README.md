# Student-Hub-Portal
Semester 3 Web Development Frameworks project — StudentHub management portal.

## Local database setup

Import `Database/schema.sql` into MySQL to create the `studenthub` database and tables. Database connection values can be set with `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, and optionally `DB_CHARSET` (defaults: `localhost`, `studenthub`, `root`, empty password, and `utf8mb4`).

Create the initial super administrator once from a terminal with `php Admin/setup_admin.php`. The script prompts for the administrator ID, name, email, department, and password; alternatively, set `ADMIN_ID`, `ADMIN_NAME`, `ADMIN_EMAIL`, `ADMIN_DEPARTMENT`, and `ADMIN_SETUP_PASSWORD` in the environment before running it. Setup is disabled once any administrator exists.
