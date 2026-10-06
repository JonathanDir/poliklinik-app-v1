# Poliklinik Management System

A web-based clinic management application developed using Laravel. The system provides a structured foundation for managing users with different roles, patient registration, authentication, dashboards, and relationships between clinic-related data.

## Overview

Poliklinik Management System is a Laravel-based web application developed as part of a Web Developer project.

The application implements a multi-role authentication system for Admin, Doctor, and Patient users. It also uses Laravel Eloquent ORM to establish relationships between clinic data models and integrates a MySQL database as the primary data storage.

The project focuses on building a clean application structure using Laravel Blade components, Tailwind CSS, DaisyUI, middleware-based authorization, database migrations, seeders, and Eloquent relationships.

## Features

### Authentication & Authorization

- User login and logout
- Patient registration
- Multi-role authentication
- Role-based access control using middleware
- Separate dashboards for Admin, Doctor, and Patient
- Unauthorized role access protection

### Patient Registration

- Patient registration form
- Patient identity information
- Automatic Medical Record Number (`no_rm`) generation
- Email uniqueness validation
- Password hashing
- Patient role assignment

### User Roles

The application currently supports three user roles:

| Role | Description |
|------|-------------|
| Admin | Provides access to the administrative dashboard |
| Doctor | Provides access to the doctor dashboard and doctor-related functionality |
| Patient | Provides access to the patient dashboard and registration-related functionality |

### Eloquent Relationships

The application uses Laravel Eloquent ORM to define relationships between clinic models.

Implemented relationships include:

- `Poli` has many `User` records representing doctors
- `User` belongs to `Poli`
- `User` has many `JadwalPeriksa`
- `JadwalPeriksa` belongs to `User` as a doctor
- `JadwalPeriksa` has many `DaftarPoli`
- `DaftarPoli` belongs to a patient
- `DaftarPoli` belongs to `JadwalPeriksa`
- `DaftarPoli` has many `Periksa`
- `Periksa` belongs to `DaftarPoli`
- `Periksa` has many `DetailPeriksa`
- `DetailPeriksa` belongs to `Periksa`
- `DetailPeriksa` belongs to `Obat`
- `Obat` has many `DetailPeriksa`

## Main Models

The project contains the following main Eloquent models:

- `User`
- `Poli`
- `JadwalPeriksa`
- `DaftarPoli`
- `Periksa`
- `Obat`
- `DetailPeriksa`

## Database Structure

The application uses MySQL with the following main tables:

- `users`
- `poli`
- `jadwal_periksa`
- `daftar_poli`
- `periksa`
- `obat`
- `detail_periksa`

Database relationships are implemented using Laravel migrations, foreign keys, and Eloquent ORM relationships.

## UI & Layout

The frontend uses:

- Laravel Blade
- Tailwind CSS
- DaisyUI
- Font Awesome
- Responsive layout components

The application layout is organized into reusable components, including:

- Header
- Sidebar
- Footer
- Application layout
- Guest authentication layout

This approach helps reduce duplicated markup and follows the DRY (Don't Repeat Yourself) principle.

## Technology Stack

| Technology | Purpose |
|------------|---------|
| Laravel | Backend web framework |
| PHP | Programming language |
| MySQL | Relational database |
| Eloquent ORM | Database interaction and relationships |
| Blade | Server-side templating |
| Tailwind CSS | Utility-first CSS framework |
| DaisyUI | UI component library |
| Vite | Frontend asset development |
| JavaScript | Client-side functionality |

## Project Structure

```text
poliklinik-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Middleware/
│   └── Models/
│
├── bootstrap/
├── config/
├── database/
│   ├── migrations/
│   └── seeders/
│
├── public/
├── resources/
│   └── views/
│       ├── auth/
│       ├── admin/
│       ├── dokter/
│       ├── pasien/
│       └── components/
│
├── routes/
│   └── web.php
│
├── .gitignore
├── artisan
├── composer.json
├── package.json
└── README.md



Author
Jonathan Naufal Farrel