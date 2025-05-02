CREATE DATABASE IF NOT EXISTS ticket_booking;
USE ticket_booking;

CREATE TABLE IF NOT EXISTS users (
  id INT(11) NOT NULL AUTO_INCREMENT,
  username VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS events (
  id INT(11) NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  date DATE NOT NULL,
  venue VARCHAR(255) NOT NULL,
  available_seats INT(11) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS bookings (
  id INT(11) NOT NULL AUTO_INCREMENT,
  user_id INT(11) NOT NULL,
  event_id INT(11) NOT NULL,
  booked_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  quantity INT(11) NOT NULL,
  total_amount DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (id),
  KEY user_id (user_id),
  KEY event_id (event_id),
  CONSTRAINT bookings_ibfk_1 FOREIGN KEY (user_id) REFERENCES users(id),
  CONSTRAINT bookings_ibfk_2 FOREIGN KEY (event_id) REFERENCES events(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO events (id, name, date, venue, available_seats, price) VALUES
(4, 'Concert A', '2025-05-10', 'Hall 1', 95, 100.00),
(5, 'Concert B', '2025-06-15', 'Hall 2', 80, 135.00),
(6, 'Concert C', '2025-06-30', 'Hall 3', 99, 125.00),
(7, 'Conference X', '2025-07-01', 'Auditorium', 50, 150.00);
