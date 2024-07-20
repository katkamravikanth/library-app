# Library Application

## Installation

1. Clone the repository
2. Run `composer install`
3. Set up the database configuration in `.env` file
4. Run migrations: `php bin/console doctrine:migrations:migrate`
5. Start the server: `symfony server:start` or `php -S localhost:8000 -t public/`

### Data Seeding

Run `php bin/console doctrine:fixtures:load`

### Postman Collection

Import the provided Postman collection to test the APIs.
