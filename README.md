# Bus Ticketing System API

API for a bus ticketing system.

## Technologies Used

- **Laravel 11.**
- **MySQL**
- **Docker**
- **Docker-Compose**
- **MailHog**    - For email testing, after registering a user, you can check the email in MailHog at http://localhost:8025

## Run with Docker

1. Copy `.env.example` to `.env` and update the environment variables if needed.

2. Run docker compose -f deploy/docker-compose.yml --env-file ./.env up --build

This will start the application and automatically run the migrations and seed the database.


# API Documentation

## Do not forget to get cookie XSRF-TOKEN before post requests, and add them to the headers.

** GET ** {{base_url}}/sanctum/csrf-cookie

## **Authentication Routes**

### **POST** `/register`
- **Description**: Registers a new user.
- **Request**:
    ```json
    {
        "email": "user@example.com",
        "first_name": "John",
        "last_name": "Doe",
        "password": "password",
        "password_confirmation": "password"
    }
    ```
- **Response**:
    ```json
    {
        "message": "User registered successfully. Check your email for verification."
    }
    ```
- **Notes**: Sends an email verification link.

---

### **POST** `/login`
- **Description**: Logs in a user and returns a token.
- **Request**:
    ```json
    {
        "email": "user@example.com",
        "password": "password"
    }
    ```
- **Response**:
    ```json
    {
        "access_token": "eyJ0eXAiOiJKV1Qi...",
        "token_type": "Bearer",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "user@example.com"
        }
    }
    ```

---

### **GET** `/verify-email`
- **Description**: Verifies a user’s email using a link.
- **Query Parameters**:
    - `id`: User ID.
    - `hash`: Verification hash.
- **Response**:
    ```json
    {
        "message": "Email verified successfully."
    }
    ```

---

### **POST** `/logout`
- **Description**: Logs out the currently authenticated user.
- **Headers**:
    - `Authorization: Bearer <token>`
- **Response**:
    ```json
    {
        "message": "Logged out successfully."
    }
    ```

---

## **Public Routes**

### **GET** `/schedules`
- **Description**: Lists all available schedules for public access.
- **Query Parameters**:
    - `start_stop_id` ID of the starting stop.
    - `end_stop_id` ID of the ending stop.
    - `day`: Travel day.
    - `month`: Travel month.
- **Response**:
    ```json
    [
        {
            "schedule_id": 1,
            "departure_time": "2024-12-01 15:30:00",
            "route_name": "Kutaisi - Tbilisi",
            "available_seats": 10
        }
    ]
    ```

---

## **User Routes**

### **POST** `/buy-ticket`
- **Description**: Buys a ticket for a user.
- **Headers**:
    - `Authorization: Bearer <token>`
- **Request**:
    ```json
    {
        "schedule_id": 1,
        "start_stop_id": 2,
        "end_stop_id": 5,
        "quantity": 2
    }
    ```
- **Response**:
    ```json
    {
        "message": "Purchase successful",
        "purchase_id": 1,
        "total_price": 100
    }
    ```

---

### **GET** `/get-user-purchases`
- **Description**: Lists all purchases for the authenticated user.
- **Headers**:
    - `Authorization: Bearer <token>`
- **Response**:
    ```json
    [
        {
            "purchase_id": 1,
            "schedule_id": 5,
            "route_name": "Kutaisi - Tbilisi",
            "total_price": 150,
            "tickets": [
                {
                    "ticket_id": 101,
                    "start_stop_id": 3,
                    "end_stop_id": 4,
                    "price": 50
                }
            ]
        }
    ]
    ```

---

### **GET** `/get-all-purchases`
- **Description**: Lists all purchases (Admin only).
- **Headers**:
    - `Authorization: Bearer <admin-token>`
- **Response**:
    ```json
    [
        {
            "purchase_id": 1,
            "user": "John Doe",
            "schedule_id": 5,
            "route_name": "Kutaisi - Tbilisi",
            "total_price": 150
        }
    ]
    ```

---

## **Bus Routes**

### **POST** `/bus`
- **Description**: Creates a new bus (Admin only).
- **Headers**:
    - `Authorization: Bearer <admin-token>`
- **Request**:
    ```json
    {
        "name": "Express Bus",
        "capacity": 50
    }
    ```
- **Response**:
    ```json
    {
        "id": 1,
        "name": "Express Bus",
        "capacity": 50
    }
    ```

### **GET** `/bus/{id}`
- **Description**: Retrieves details of a specific bus.
- **Headers**:
    - `Authorization: Bearer <admin-token>`
- **Response**:
    ```json
    {
        "id": 1,
        "name": "Express Bus",
        "capacity": 50
    }
    ```

---

### **GET** `/buses`
- **Description**: Retrieves all buses.
- **Headers**:
    - `Authorization: Bearer <admin-token>`
- **Response**:
    ```json
    [
        {
            "id": 1,
            "name": "Express Bus",
            "capacity": 50
        }
    ]
    ```

---

### **PUT** `/bus/{id}`
- **Description**: Updates a bus (Admin only).
- **Headers**:
    - `Authorization: Bearer <admin-token>`
- **Request**:
    ```json
    {
        "name": "Super Express Bus",
        "capacity": 60
    }
    ```
- **Response**:
    ```json
    {
        "id": 1,
        "name": "Super Express Bus",
        "capacity": 60
    }
    ```

---

### **DELETE** `/bus/{id}`
- **Description**: Deletes a bus (Admin only).
- **Headers**:
    - `Authorization: Bearer <admin-token>`
- **Response**:
    ```json
    {
        "message": "Deleted successfully."
    }
    ```

---

## **Route, Schedule, Stop, and User Routes**

The **CRUD operations** for `Route`, `Schedule`, `Stop`, and `User` follow the same structure as the **Bus Routes** described above.

**Example for `Route`:**
- **POST** `/route`: Creates a route.
- **GET** `/route/{id}`: Retrieves a specific route.
- **GET** `/routes`: Retrieves all routes.
- **PUT** `/route/{id}`: Updates a route.
- **DELETE** `/route/{id}`: Deletes a route.

**Replace `route` with `schedule`, `stop`, or `user` to access the respective resources.**

---

### **Special Note for Schedules**

When creating a `Schedule`, include `totalSeats` in the request to automatically populate the `seat_availability` table:
```json
{
    "bus_id": 1,
    "route_id": 1,
    "stop_id": 2,
    "arrival_time": "15:30:00",
    "departure_time": "15:35:00",
    "date": "2024-12-01",
    "totalSeats": 50
}
```

