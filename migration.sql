CREATE DATABASE IF NOT EXISTS Orizon;
USE Orizon;

-- Countries table
CREATE TABLE countries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- Trips table
CREATE TABLE trips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    available_seats INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Pivot table (Many-to-Many relationship)
CREATE TABLE IF NOT EXISTS trips_countries (
    trip_id INT,
    country_id INT,
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
    FOREIGN KEY (country_id) REFERENCES countries(id) ON DELETE CASCADE,
    PRIMARY KEY (trip_id, country_id)
);