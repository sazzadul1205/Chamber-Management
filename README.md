# Dental Chamber Management System

A comprehensive, enterprise-grade dental practice management solution with an integrated drag-and-drop page builder for creating stunning public-facing websites. Built with Laravel, React.js, and modern web technologies.

## 📋 Overview

The Dental Chamber Management System is a full-featured platform designed to streamline dental practice operations while providing powerful tools for managing your online presence. The system combines robust backend management capabilities with an intuitive frontend page builder, allowing dental practices to maintain both their clinical operations and website content from a single platform.

### Key Highlights
- **Dual Interface**: Separate admin backend and public-facing website with seamless integration
- **Visual Page Builder**: Drag-and-drop interface for creating custom website layouts without coding
- **Role-Based Access**: Granular permissions for Admin, Doctors, Receptionists, and Accountants
- **Real-Time Updates**: Live synchronization between appointments, treatments, and inventory
- **Mobile Responsive**: Fully responsive design that works on all devices

## 🏗 System Architecture

### Technology Stack
- **Backend Framework**: Laravel 10.x with PHP 8.1+
- **Frontend Framework**: React 18.x with Inertia.js
- **Database**: MySQL 8.0+ / PostgreSQL 14+
- **UI Components**: Tailwind CSS, Headless UI
- **Build Tools**: Vite, Laravel Mix
- **Additional Technologies**: 
  - Laravel Breeze for authentication scaffolding
  - Laravel Permission for role management
  - Laravel Backup for automated backups
  - Laravel Excel for import/export functionality

## ✨ Core Features

### 1. **Appointment Management System**
- Multi-provider calendar with real-time availability
- Online booking integration with automated reminders
- Queue management for walk-in patients
- Chair allocation and scheduling
- Conflict detection and prevention
- SMS/Email appointment reminders

### 2. **Patient Management**
- Comprehensive patient profiles with medical history
- Digital dental charts with tooth-by-tooth tracking
- Family account linking for group billing
- Treatment plan management with cost estimates
- Medical files and document storage
- Patient communication history

### 3. **Treatment & Clinical Features**
- Procedure catalog with standard codes
- Treatment session tracking and progress monitoring
- Digital prescriptions with e-signing capability
- Referral management between specialists
- Diagnosis codes (ICD-10 compatible)
- Treatment timeline visualization

### 4. **Inventory Management**
- Real-time stock tracking with low stock alerts
- Automated reorder point calculation
- Expiry date monitoring and alerts
- Usage tracking per treatment/procedure
- Supplier management and purchase orders
- Inventory valuation reports

### 5. **Financial Management**
- Multi-payment method support (Cash, Card, Insurance)
- Invoice generation with customizable templates
- Payment tracking per treatment/session
- Daily payment reports and reconciliation
- Outstanding balance tracking
- Insurance claim management
- Receipt printing and email delivery

### 6. **Staff Management**
- Role-based access control (Admin, Doctor, Receptionist, Accountant)
- Doctor schedules and leave management
- Performance metrics and reporting
- Activity audit logs for compliance
- Commission tracking for procedures

### 7. **Reports & Analytics**
- Appointment analytics and no-show tracking
- Revenue reports by period, doctor, procedure
- Patient retention and acquisition metrics
- Inventory usage and consumption reports
- Treatment success rates and statistics
- Export capabilities (PDF, Excel, CSV)

## 🎨 Page Builder System

### Component-Based Architecture
The integrated page builder allows for complete customization of the public-facing website through reusable components:

#### Available Section Types
1. **Top Slider** (3 variations)
   - Hero images with call-to-action
   - Video backgrounds
   - Animated text overlays

2. **About Sections** (3 variations)
   - Practice information
   - Team introductions
   - Facility showcases

3. **Services Display** (3 variations)
   - Service cards with icons
   - Detailed procedure lists
   - Pricing integration

4. **Our Dentists** (3 variations)
   - Team member profiles
   - Specialization highlights
   - Booking integration

5. **Testimonials** (3 variations)
   - Patient reviews carousel
   - Video testimonials
   - Rating displays

6. **Latest News** (3 variations)
   - Blog post previews
   - Industry updates
   - Practice announcements

7. **Pricing Sections** (3 variations)
   - Procedure cost tables
   - Package deals
   - Insurance information

8. **Book Appointment** (3 variations)
   - Quick booking forms
   - Multi-step booking flows
   - Insurance verification forms

### Custom Component Builder
- Create and save custom React components
- Drag-and-drop interface for layout design
- Real-time preview panel
- Responsive design testing
- Component version control
- Template saving and reuse

## 📁 Directory Structure

### Backend Structure (Laravel)
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   ├── Api/
│   │   ├── Auth/
│   │   └── Dashboard/
│   ├── Middleware/
│   └── Requests/
├── Models/
│   ├── Appointment.php
│   ├── Patient.php
│   ├── Treatment.php
│   ├── InventoryItem.php
│   └── User.php
├── Services/
│   ├── AppointmentService.php
│   ├── InventoryService.php
│   └── PaymentService.php
└── View/
    └── Components/
```

### Frontend Structure (React)
```
resources/js/
├── Components/         # Reusable UI components
├── Layouts/            # Layout templates
├── Pages/             # Page components
│   ├── Admin/         # Admin dashboard pages
│   ├── Auth/          # Authentication pages
│   ├── Home/          # Public website sections
│   └── Profile/       # User profile pages
└── Shared/            # Shared components
```

## 🚀 Installation Guide

### Prerequisites
- PHP >= 8.1
- Composer
- Node.js >= 16.x
- MySQL >= 8.0 / PostgreSQL >= 14
- Redis (optional, for caching)

### Step-by-Step Installation

1. **Clone the Repository**
```bash
git clone https://github.com/yourusername/dental-chamber-management.git
cd dental-chamber-management
```

2. **Install PHP Dependencies**
```bash
composer install
```

3. **Install Node Dependencies**
```bash
npm install
```

4. **Environment Configuration**
```bash
cp .env.example .env
php artisan key:generate
```

Configure your database and mail settings in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dental_chamber
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
```

5. **Database Setup**
```bash
php artisan migrate
php artisan db:seed
```

6. **Storage Link**
```bash
php artisan storage:link
```

7. **Build Assets**
```bash
npm run build
# For development:
npm run dev
```

8. **Start the Application**
```bash
php artisan serve
```

## 🎯 Usage Guide

### Initial Setup
1. Access the application at `http://localhost:8000`
2. Login with default admin credentials:
   - Email: admin@dental.com
   - Password: password
3. Complete the initial configuration wizard
4. Set up your practice profile and settings

### Creating a Website with Page Builder
1. Navigate to Admin → Page Builder
2. Choose a layout template or start from scratch
3. Drag and drop sections from the available components
4. Customize each section's content and styling
5. Preview changes in real-time
6. Publish when ready

### Managing Appointments
1. Access the appointment calendar
2. Create new appointments manually or via online booking
3. Assign chairs and doctors
4. Send reminders to patients
5. Track appointment status

### Processing Treatments
1. Create patient treatment plans
2. Schedule treatment sessions
3. Track procedures and materials used
4. Generate invoices automatically
5. Process payments

## 👥 User Roles & Permissions

### Administrator
- Full system access
- User management
- System configuration
- Financial reports
- Backup management

### Doctor
- View appointments
- Manage treatments
- Access patient records
- Create prescriptions
- View personal schedule

### Receptionist
- Manage appointments
- Patient registration
- Payment collection
- Basic reporting

### Accountant
- Financial reports
- Invoice management
- Payment reconciliation
- Insurance claims

## 🔒 Security Features

- **Authentication**: Multi-factor authentication support
- **Authorization**: Role-based access control with Laravel Permission
- **Data Encryption**: Encrypted sensitive patient data
- **Audit Trail**: Complete activity logging
- **GDPR Compliance**: Data export and deletion capabilities
- **Backup System**: Automated encrypted backups
- **Session Management**: Concurrent session control

## 📊 API Documentation

The system provides RESTful APIs for integration:
- Patient management endpoints
- Appointment scheduling
- Inventory queries
- Financial transactions
- Report generation

API documentation available at `/api/documentation`

## 🧪 Testing

```bash
# Run PHPUnit tests
php artisan test

# Run Laravel Dusk browser tests
php artisan dusk

# Run JavaScript tests
npm run test
```

## 🚀 Deployment

### Production Requirements
- PHP 8.1+ with required extensions
- MySQL 8.0+ or PostgreSQL 14+
- Redis for caching
- Supervisor for queue workers
- Nginx/Apache web server

### Deployment Steps
1. Set up production environment
2. Configure environment variables
3. Run migrations and seeders
4. Build assets for production
5. Set up queue worker
6. Configure backup schedule
7. Set up monitoring

## 📈 Performance Optimization

- Database query optimization with eager loading
- Redis caching for frequent queries
- Asset minification and bundling
- Image optimization and lazy loading
- Queue jobs for background processing
- Pagination for large datasets

## 🆘 Support & Maintenance

### Regular Maintenance Tasks
- Database backups (daily)
- Log file rotation (weekly)
- Cache clearing (as needed)
- Update dependencies (monthly)
- Security patches (as released)

### Troubleshooting
Common issues and solutions documented in [TROUBLESHOOTING.md](TROUBLESHOOTING.md)

## 🤝 Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

## 📄 License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.

## 🙏 Acknowledgments

- Laravel community for the amazing framework
- React team for the frontend library
- Tailwind CSS for the utility-first CSS framework
- All contributors who have helped shape this project

## 📞 Contact & Support

- **Documentation**: [docs.dentalchamber.com](https://docs.dentalchamber.com)
- **Support Email**: support@dentalchamber.com
- **Issue Tracker**: [GitHub Issues](https://github.com/yourusername/dental-chamber-management/issues)
- **Community Forum**: [community.dentalchamber.com](https://community.dentalchamber.com)

---

**Built with ❤️ for modern dental practices**