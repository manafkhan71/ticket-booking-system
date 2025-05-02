# Simple Ticket Booking System (PHP & MySQL)

This is a basic event ticket booking system developed using Core PHP, MySQL, and Vanilla JavaScript (AJAX). Users can register, log in, view available events, book tickets, view their booking history, and cancel bookings.

## Features

- User Registration & Login (with password hashing)
- Event Listing with Available Seats
- AJAX Ticket Booking (no page reload)
- Booking History Page
- Cancel Booking Feature
- Prevent Duplicate Bookings
- Session-Based Access Control
- Secure PDO Prepared Statements

## Tech Stack

- **Backend:** Core PHP
- **Frontend:** HTML, CSS, JavaScript (AJAX)
- **Database:** MySQL (phpMyAdmin friendly)

## Setup Instructions

1. Clone or download this repository.
2. Place it inside your `htdocs` folder (if using XAMPP).
3. Create a MySQL database named `ticket_booking`.
4. Import the `db.sql` file using phpMyAdmin.
5. Update `config.php` if your DB username/password is different.
6. Run the project in browser:
