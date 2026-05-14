# Orizon API - Trip Management

Welcome to **Orizon**, a REST API built in PHP for managing trips and international destinations. The system allows you to create trips, associate them with multiple countries, and manage seat availability.

## 🚀 Features
- **CRUD Countries**: Create, read, update, and delete destinations.
- **CRUD Trips**: Full trip management (Create, Read, Update, Delete).
- **Advanced Filters**: Search trips by available seats and by country.
- **Security**: Native PDO prepared statements, full CORS support.

## 🛠️ Requirements
- **Local Server**: MAMP
- **Language**: PHP 8.3+
- **Extensions**: PDO, Apache mod_rewrite
- **Dependencies**: Composer, vlucas/phpdotenv

---

## 📂 Project Structure
```
Orizon/
├── App/
│   ├── controllers/
│   │   ├── CountryController.php
│   │   └── TripController.php
│   └── models/
│       ├── country.php
│       └── trip.php
├── config/
│   └── database.php
├── core/
│   └── Router.php
├── vendor/
├── .env
├── .env.example
├── .gitignore
├── .htaccess
├── composer.json
├── index.php
└── migration.sql
```

---

## 📡 API Documentation

**Base URL**: `http://localhost:8888/Orizon`

### Countries

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/country` | Returns all countries |
| `POST` | `/country` | Creates a new country |
| `PUT` | `/country/{id}` | Updates an existing country |
| `DELETE` | `/country/{id}` | Deletes a country |

### Trips

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/trip` | Returns all trips (supports filters) |
| `GET` | `/trip/{id}` | Returns a specific trip with its countries |
| `POST` | `/trip` | Creates a trip and associates countries |
| `PUT` | `/trip/{id}` | Updates seats and countries of an existing trip |
| `DELETE` | `/trip/{id}` | Deletes a trip and its links |

### Available filters on `GET /trip`
| Parameter | Type | Description |
| :--- | :--- | :--- |
| `country_id` | integer | Filter trips that include a specific country |
| `seats` | integer | Filter trips with at least N available seats |

Example: `GET /trip?country_id=1&seats=5`

---

## 📋 Request Examples

### Create a country
```json
POST /country
Content-Type: application/json

{ "name": "Italy" }
```

### Create a trip
```json
POST /trip
Content-Type: application/json

{
    "available_seats": 25,
    "country_ids": [1, 2]
}
```

### Update a trip
```json
PUT /trip/1
Content-Type: application/json

{
    "available_seats": 10,
    "country_ids": [1]
}
```
