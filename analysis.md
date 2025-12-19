# Kunjachan Missionary Bhavan — Project Analysis

## Overview
- **Purpose:** Comprehensive management platform for institutions, inmates, medical workflows, and guardian relations with public site and admin backoffice.
- **Framework:** Laravel 12 (PHP ^8.2), Pest for tests, Vite/Tailwind/Alpine for frontend.
- **Key Domains:** Institutions, Inmates, Medical Records, Medications, Lab Tests, Appointments, Locations/Blocks/Allocation, Guardians, Donations, Blog, Notifications, Support Tickets, Reports, PDFs.

## Tech Stack & Environment
- **Backend:** Laravel 12; packages include `barryvdh/laravel-dompdf`, `league/flysystem-aws-s3-v3`.
- **Frontend:** Vite 7, Tailwind 3, Alpine.js, Axios; optional Echo/Pusher for realtime.
- **Data:** MySQL 8+ (per README), migrations define schema across domains.
- **Queues/Jobs:** Composer `dev` script runs `queue:listen`; background events/notifications rely on Laravel queue.
- **PDF:** DomPDF with custom `PdfManager` and `DompdfRenderer`.
- **Third-party:** SES (AWS), Postmark, Resend, Slack (config-driven; optional by env).

## Access Roles & Dashboards
- **Public:** Home, About, Timeline, Gallery, Contact; Institutions listing & detail; Blog listing & posts; Donation page.
- **Authenticated:** Profile edit/update/delete.
- **Developer:** Full management of Institutions, Inmates, Users, Guardians, Blocks/Locations (allocation), Doctors assignment, Requests, Tickets.
- **System Admin:** Global management (Institutions, Inmates, Users, Guardians, Payments, Allocation, Medicines, Medication reports, Doctors scheduling, AJAX tabs and APIs).
- **Admin (Institution scope):** Inmates CRUD + assignment (doctor/location), Documents, Staff, Users, Institutions, Blocks/Locations management, Medicines inventory (institution scoped), Guardian messages, Medication reports, AJAX APIs.
- **Doctor:** Inmates (read), Medical Records CRUD, Medications CRUD, Lab Tests full lifecycle, Appointments calendar & feed, Therapy logs, Counseling notes.
- **Nurse:** Inmates (read), Lab Tests partial update/upload results, Medication Logs, Medication schedule & quick logging, Examinations.
- **Staff:** Inmates create/show/store, Medication logs & schedule, Examinations, Allocation edit/update, Lab Tests partial update.
- **Guardian:** Dashboard and messaging (send).

## Core Features by Domain
- **Institutions & Donations:**
  - Public institutions index/show; System Admin manages institutions (tabs: overview/users/inmates/donations/settings) and donation settings.
- **Inmates Management:**
  - CRUD across roles; assignments (doctor, location); documents upload/share; search (System Admin); report download.
- **Guardians & Messaging:**
  - CRUD for guardians; guardian messages via admin/system admin with reply flows; guardian dashboard and send.
- **Medical Records & Medications:**
  - Doctor adds/updates medications; Nurse/Staff log medication administrations; global/institution views of live meds, logs, usage, assignees, low stock, history; medication attendance report.
- **Lab Tests:**
  - Doctor orders/edits/updates; Nurse/Staff index/show/partial update; event-driven notifications (ordered, uploaded, rejected).
- **Appointments:**
  - Doctor manages calendar: index, feed (JSON), CRUD.
- **Locations, Blocks & Allocation:**
  - Hierarchical blocks with locations; APIs for cascading selects; allocation dashboards; inmate location assignment; institution-scoped endpoints.
- **Blog:**
  - Public index/show; Admin/System Admin manage blogs.
- **Support Tickets & Bug Reporting:**
  - Auth users submit/view/reply; Developer/System Admin ticket dashboards; toggle bug-reporting per user.
- **Notifications:**
  - User-facing notifications index/feed, mark-all, mark-read; multiple domain notifications (appointments, lab tests, birthdays, tickets).
- **PDF Reports:**
  - Inmate profile PDF via template `pdf.inmates.profile` and `PdfManager` streaming download.

## Data Model Overview (from migrations)
- **Core:** `users`, `institutions`, `inmates`, `guardians` (+ guardian links to inmates/users), `blocks`, `locations`, `location_assignments`.
- **Medical:** `medical_records`, `medications`, `medication_logs`, `lab_tests`, `examinations`.
- **Care Plans & Notes:** `mental_health_plans`, `rehabilitation_plans`, `geriatric_care_plans`, `therapy_session_logs`, `counseling_progress_notes`, `case_log_entries`.
- **Education:** `educational_records` (with subjects/grades).
- **Operations:** `action_requests`, `support_tickets`, `ticket_replies`, `notifications`, `appointments`, `doctor_handoffs`, `doctor_inmate` pivot.
- **Inventory:** `medicines`, `medicine_inventories`.
- **Finance & Public:** `inmate_payments`, `donation_settings`, `blogs`.
- **Meta:** cache/jobs tables; various `add_*` and `update_*` migration augmentations (roles, flags, fields).

## Workflows
- **Admission & Profile:** Inmate admission fields, `AdmissionNumberGenerator`, profile view and PDF download stream.
- **Medication Attendance:** Institution/global pages to log administrations, visualize live schedules, and report attendance.
- **Lab Test Lifecycle:** Doctor orders (fires `LabTestOrderedEvent`), Nurse/Staff update statuses, upload results; notifications drive user awareness.
- **Allocation:** Admin/System Admin/Developer manage blocks/locations; APIs assist cascading selects and room picker UIs; assign inmates.
- **Guardian Communication:** Guardians send messages; admins reply within guardian show pages; notifications emitted.
- **Doctor Assignments:** Assign/transfer doctor per inmate; feed and emergency schedule endpoints for doctor.

## Integrations & Configuration
- **Email:** SES (AWS) or Postmark/Resend via `config/services.php` (env-driven).
- **Slack:** Optional notifications channel (env-driven).
- **Storage:** AWS S3 via Flysystem; `S3Healthcheck` command suggests S3 readiness checks.
- **PDF:** `config/pdf.php` maps templates to views; `config/dompdf.php` tuned for smaller outputs and Helvetica default font.

## Events, Notifications, Commands
- **Event:** `LabTestOrderedEvent`.
- **Notifications:** `EmergencyAppointmentScheduled`, `InmateBirthday`, `LabResultRejected`, `LabResultUploaded`, `LabTestOrdered`, `NewTicketReply`, `TransferOfCareNotification`.
- **Commands:** `NotifyInmateBirthdays`, `S3Healthcheck` (names imply scheduled routines).

## Frontend
- **Build:** Vite 7 with Tailwind 3, PostCSS; `laravel-vite-plugin`.
- **UI:** Alpine.js for interactivity, Axios for HTTP; optional Echo/Pusher for realtime.
- **Scripts:** `npm run dev` for Vite; part of composer `dev` concurrent process.

## Testing
- **Framework:** Pest (with Laravel plugin); `tests/Feature` includes `Auth/`, `ProfileTest.php`, and `SystemAdmin/` folder; unit tests scaffold present.
- **Composer `test`:** Runs `artisan test` with config clearing.

## Setup & Run
- **Requirements:** Composer requires PHP ^8.2; README states PHP 8.1 (discrepancy — use PHP 8.2+).
- **Install:** `composer install`; configure `.env` (mail/queue/S3 as needed).
- **Migrate:** `php artisan migrate`.
- **Dev:** `composer run dev` starts server, queue listener, and Vite concurrently.

## Current Status Summary
- **Implemented:** Extensive route groups across roles; controllers for major domains; models and migrations cover broad feature set; PDF pipeline configured; notifications and commands present; public site and blog included.
- **Operational Needs:** Ensure queue worker is running for notifications/events; configure email (SES/Postmark/Resend) and Slack if used; set up S3 if document storage is remote.
- **Docs:** `docs/PROJECT_UNDERSTANDING.md` is empty; README is minimal and has PHP version mismatch.

## Discrepancies & TODOs
- **PHP Version:** README lists PHP 8.1; `composer.json` requires ^8.2. Update README.
- **Docs:** Fill out `docs/PROJECT_UNDERSTANDING.md` with domain/process diagrams and role capabilities.
- **Env:** Verify and document required env vars (`AWS_*`, `MAIL_*`, `SLACK_*`, queue driver).
- **Testing Coverage:** Expand Feature tests for role-based routes and critical workflows (medication logs, lab tests, allocation).
- **PDF Catalog:** Add more templates (e.g., medication attendance, lab test summary) to `config/pdf.php`.

---
Generated on 2025-12-17 from repository inspection.
