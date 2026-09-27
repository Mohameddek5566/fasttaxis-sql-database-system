# FastTaxis SQL Database System

An SQL database project for managing the information and operations of a taxi business. The core work is the relational database: its tables, relationships, constraints, sample records and reporting queries. PHP, HTML and CSS were then used to demonstrate how different users can interact with the database through a web interface.

## Features

- MySQL relational schema, relationships and reusable setup script.
- Structured data for drivers, owners, clients, vehicles, bookings and maintenance.
- SQL queries that support operational reporting and data retrieval.
- A PHP, HTML and CSS interface that demonstrates role-based database access for directors, staff, drivers and owners.
- Operational pages for bookings, vehicles, drivers, clients and maintenance.
- Entity-relationship design and interface previews.
- Reporting queries for business operations.

## Run locally

1. Import `database/Fast_Taxi.sql` into MySQL.
2. Place `application` in a PHP/XAMPP web directory.
3. Update `application/config/db.php` if your local database settings differ.
4. Open the application's `public` folder through the local server.

The included configuration uses XAMPP's local development defaults and contains no private production credentials.

## Database focus

MySQL and SQL are the main technologies. PHP, HTML, CSS and XAMPP support the working database interface; Draw.io was used for the ERD.
