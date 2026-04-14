# TCM - Training Course Management System

A comprehensive multi-tenant web application built with Laravel for managing training courses, enrollments, assessments, and certifications across multiple organizations.

## Overview

TCM is a SaaS platform designed to streamline training and certification processes. It supports multiple tenants (organizations) where each tenant can manage their own courses, trainers, trainees, and training programs. The system includes role-based access control with SuperAdmin, Admin, Trainer, and Trainee roles.

## Features

### Multi-Tenancy
- Isolated tenant environments with separate domains
- Centralized superadmin management
- Tenant-specific branding and customization

### User Management
- **SuperAdmin**: Manages tenants, subscriptions, and system-wide analytics
- **Admin**: Manages their organization's courses, users, and settings
- **Trainer**: Conducts training sessions, manages attendance, and assessments
- **Trainee**: Enrolls in courses, views schedules, and accesses certificates

### Core Functionality
- **Course Management**: Create and organize training courses
- **Enrollment System**: Handle trainee registrations and course assignments
- **Training Schedules**: Plan and manage training sessions
- **Attendance Tracking**: Record and monitor session attendance
- **Assessments**: Create and grade quizzes and evaluations
- **Certificate Generation**: Automated PDF certificate creation
- **Subscription Management**: Handle plan upgrades and renewals
- **Reporting & Analytics**: Comprehensive reports and dashboards
- **Notifications**: Email notifications for important events

### Technical Features
- Social authentication (Google OAuth)
- SocialAuth Controller for enhanced social login management
- PDF generation for certificates and reports
- Excel export for data analysis
- Real-time notifications
- Bandwidth tracking
- Activity logging
- New hosting method/function for improved deployment options

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Database**: MySQL/PostgreSQL with Eloquent ORM
- **Frontend**: Vite, Tailwind CSS, PostCSS
- **Authentication**: Laravel Sanctum, Socialite
- **Multi-Tenancy**: stancl/tenancy package
- **PDF Generation**: DomPDF, FPDF
- **Spreadsheet Handling**: PhpSpreadsheet
- **Testing**: Pest PHP
- **Queue Processing**: Laravel Queues
- **Caching**: Multiple cache backends

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js and npm
- MySQL/PostgreSQL database

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd tcm
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database setup**
   ```bash
   php artisan migrate --force
   php artisan db:seed
   ```

6. **Build assets**
   ```bash
   npm run build
   ```

7. **Start the application**
   ```bash
   php artisan serve
   ```

## Configuration

### Multi-Tenancy Setup
Configure central domains in `config/tenancy.php`:
```php
'central_domains' => [
    'app.tcm.com',
    // Add your central domain
],
```

### Social Authentication
Set up Google OAuth in `.env`:
```
GOOGLE_CLIENT_ID=your_client_id
GOOGLE_CLIENT_SECRET=your_client_secret
```

### Database
Configure your database connection in `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tcm
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## Usage

### SuperAdmin Access
- Access the central domain (e.g., app.tcm.com)
- Register/login as superadmin
- Manage tenants, view analytics, and system reports

### Tenant Access
- Each tenant has their own subdomain (e.g., company.tcm.com)
- Admins can manage courses, users, and settings
- Trainers conduct sessions and assessments
- Trainees enroll and participate in courses

## Testing

Run the test suite with Pest:
```bash
./vendor/bin/pest
```

## Deployment

### Production Considerations
- Set `APP_ENV=production` in `.env`
- Configure proper database credentials
- Set up queue workers for background processing
- Configure mail settings for notifications
- Enable SSL certificates
- Set up proper file permissions

### Queue Processing
Start queue workers:
```bash
php artisan queue:work
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests
5. Submit a pull request

## License

This project is licensed under the MIT License.

## Support

For support and questions, please contact the development team or create an issue in the repository.

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
