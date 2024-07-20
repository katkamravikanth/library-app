# Library Application

## Installation

1. Clone the repository
2. Run `composer install`
3. Copy `.env.dist` and rename to `.env` file
4. Set up the database configuration in `.env` file
5. Run migrations: `php bin/console doctrine:migrations:migrate`
6. Start the server: `symfony server:start` or `php -S localhost:8000 -t public/`

### Data Seeding

Run `php bin/console doctrine:fixtures:load`

### Postman Collection

Import the provided Postman collection to test the APIs.
