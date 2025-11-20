## Hospital Operation Management System

Hospital Operation Management System is a Laravel 11 application for managing operating rooms, surgical schedules, medical equipment, and multi-role access (Admin, Doctor, Staff, Patient). The project ships with TailwindCSS-based dashboards, Excel/PDF import-export utilities, and seeded sample data for rapid evaluation.

### Requirements

- PHP 8.2+
- Composer
- Node.js 20+ & npm
- SQLite/MySQL/PostgreSQL database

### Installation

```bash
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate:fresh --seed
npm run build # or npm run dev for hot reload
php artisan serve
```

Seeded credentials:

| Role   | Email                        | Password |
|--------|------------------------------|----------|
| Admin  | admin@hospital.test          | password |
| Doctor | maya.pratama@hospital.test   | password |
| Staff  | sinta.rahma@hospital.test    | password |
| Patient| (see database seeder output) | password |

### Key Commands

- Import equipments: `POST /admin/import/equipments` (xlsx/csv file field `file`)
- Import patients: `POST /admin/patients/import`
- Export operations: `GET /admin/export/operations?from=YYYY-MM-DD&to=YYYY-MM-DD&doctor_id=&room_id=&status=&format=pdf|xlsx`
- Update room status: `PATCH /rooms/{room}/status`
- Submit doctor report: `POST /operations/{operation}/report`

### Testing

```bash
php artisan test
```

### Development Notes

- Dashboards use TailwindCSS with medical blue (`#2B6CB0`) and mint accent (`#38B2AC`).
- Excel import/export powered by `maatwebsite/excel`, PDF export via `barryvdh/laravel-dompdf`.
- Factories and seeders cover multi-role data, rooms, equipments, diseases, operations, and reports.
