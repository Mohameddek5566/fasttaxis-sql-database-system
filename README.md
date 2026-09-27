# FastTaxis Database Application

A role-based database application for managing a taxi business. The system models drivers, vehicle owners, clients, bookings, vehicles and maintenance records, then presents tailored dashboards for operational users.

## Features

- MySQL relational schema and reusable setup script.
- PHP application with login and role-based access for directors, staff, drivers and owners.
- Operational pages for bookings, vehicles, drivers, clients and maintenance.
- Entity-relationship design and interface previews.
- Reporting queries for business operations.

## Run locally

1. Import `database/Fast_Taxi.sql` into MySQL.
2. Place `application` in a PHP/XAMPP web directory.
3. Update `application/config/db.php` if your local database settings differ.
4. Open the application's `public` folder through the local server.

The included configuration uses XAMPP's local development defaults and contains no private production credentials.

## Tools

MySQL, SQL, PHP, HTML, CSS, XAMPP and Draw.io.
