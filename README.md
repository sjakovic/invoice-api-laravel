# Invoice Management API

A robust RESTful API built with Laravel for managing invoices, companies, and invoice items. This API provides a complete solution for handling business invoicing needs with features like PDF generation, company management, and detailed invoice tracking.

## Features

- 🔐 **Authentication & Authorization**
  - Secure user authentication using Laravel Sanctum
  - Role-based access control
  - Protected API endpoints

- 📄 **Invoice Management**
  - Create, read, update, and delete invoices
  - Automatic invoice numbering system
  - Support for multiple currencies
  - Tax and discount calculations
  - Due date tracking
  - Status management (paid, pending, overdue)

- 🏢 **Company Management**
  - Manage multiple companies per user
  - Detailed company information storage
  - Company-specific invoice tracking

- 📋 **Invoice Items**
  - Add multiple items to each invoice
  - Quantity and price management
  - Automatic total calculations

- 📑 **PDF Generation**
  - Professional PDF invoice generation
  - Download and stream options
  - Clean, professional layout
  - Company branding support

- 🔍 **Advanced Filtering**
  - Filter invoices by various criteria
  - Search functionality
  - Pagination support

## Technical Stack

- **Framework**: Laravel
- **Authentication**: Laravel Sanctum
- **PDF Generation**: DomPDF
- **Database**: MySQL/PostgreSQL
- **API Documentation**: OpenAPI/Swagger

## API Endpoints

### Authentication
- POST /api/register
- POST /api/login
- POST /api/logout

### Companies
- GET /api/companies
- POST /api/companies
- GET /api/companies/{id}
- PUT /api/companies/{id}
- DELETE /api/companies/{id}

### Invoices
- GET /api/invoices
- POST /api/invoices
- GET /api/invoices/{id}
- PUT /api/invoices/{id}
- DELETE /api/invoices/{id}
- GET /api/invoices/{id}/pdf/download
- GET /api/invoices/{id}/pdf/stream

### Invoice Items
- GET /api/invoices/{id}/items
- POST /api/invoices/{id}/items
- GET /api/invoices/{id}/items/{itemId}
- PUT /api/invoices/{id}/items/{itemId}
- DELETE /api/invoices/{id}/items/{itemId}

## Getting Started

1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
   ```
3. Copy environment file:
   ```bash
   cp .env.example .env
   ```
4. Generate application key:
   ```bash
   php artisan key:generate
   ```
5. Configure your database in `.env`
6. Run migrations:
   ```bash
   php artisan migrate
   ```
7. Seed the database (optional):
   ```bash
   php artisan db:seed
   ```
8. Start the development server:
   ```bash
   php artisan serve
   ```

## Testing

Run the test suite:
```bash
php artisan test
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is licensed under the MIT License - see the LICENSE file for details.
