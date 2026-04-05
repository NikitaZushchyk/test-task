# Laravel API Versioning

This project implements a REST API for managing company records with a robust, universal versioning system. It uses a polymorphic `versions` table and a reusable trait to track changes to models automatically.

## Features Implemented

*   **Universal Versioning**: A `HasVersions` trait that automatically tracks changes (creates/updates) to model attributes and logs them in a polymorphic `versions` table.
*   **Company Management**: Create and update company records (`title`, `edrpou`, `status`, `type_id`, `created_date`).
*   **Duplicate Detection**: Ensures that a company with a specific EDRPOU code can only exist once. Updates payload simply appends a new version.
*   **Dockerized Environment**: Fully containerized using Docker, with PHP-FPM, Nginx, and PostgreSQL.

## API Endpoints

*   `POST /api/company`
    *   **Description**: Creates a new company or updates an existing one based on the `edrpou` code. Every upsert action logs a new state in the `versions` table.
    *   **Payload Example**:
        ```json
        {
          "title": "My Company",
          "edrpou": "12345678",
          "status": "active",
          "type_id": 1,
          "created_date": "2024-01-01"
        }
        ```
*   `GET /api/company/{edrpou}/versions`
    *   **Description**: Retrieves the full version history for a specific company by its EDRPOU code.

## Testing

Feature tests are implemented to ensure correct upsert logic, version preservation, and duplicate prevention.

*   `tests/Feature/CompanyUpsertTest.php`: Tests various scenarios, including creating a new company, updating it to create a new version, and ignoring unchanged data.

## Getting Started

Follow these steps to run the project locally using Docker.

1.  **Clone the repository and navigate to the project directory** (if you haven't already).
2.  **Set up environment variables:**
    Copy the provided Docker environment file:
    ```bash
    cp .env.docker .env
    ```
3.  **Start the Docker containers:**
    ```bash
    docker-compose up -d --build
    ```
4.  **Install dependencies:**
    ```bash
    docker-compose exec app composer install
    ```
5.  **Generate application key:**
    ```bash
    docker-compose exec app php artisan key:generate
    ```
6.  **Run database migrations:**
    ```bash
    docker-compose exec app php artisan migrate
    ```

### Running Tests

To run the feature tests and verify the functionality:

```bash
docker-compose exec app php artisan test
```
