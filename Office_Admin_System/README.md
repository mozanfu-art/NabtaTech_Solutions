# NabtaTech Solutions MSP Platform

NabtaTech Solutions is modeled as an **IT Solutions & Managed Services Provider (MSP)**.
This web platform implements internal company operations, client technical services, and enterprise platforms in one integrated system.

## 1) Implemented Scope

### A. Internal Company Systems
- Reception: visitor registration and checkout
- Secretary: scheduling and correspondence workflow
- HR: employee records and leave approvals
- Finance: transaction ledger and payment status actions
- IT Support Desk: ticket registration and resolution actions
- Management Reporting: KPI summaries and risk board

### B. Client IT Services
- Client directory with service tiers and contract dates
- Service operations board covering:
  - network setup
  - device installation
  - troubleshooting
  - cloud services
  - business systems (ERP/CRM)
  - cybersecurity
  - documentation
  - backup & recovery
- Device inventory lifecycle (stock/deployed/maintenance/retired)

### C. Enterprise Platforms
- ERP/CRM/SAP-style workflow tracker
- Cloud workflow tracking
- DevOps pipeline monitor
- Documentation center (versioned records)

## 2) Main Pages (all clickable from header)

- `public/dashboard.php`
- `public/reception.php`
- `public/secretary.php`
- `public/hr.php`
- `public/finance.php`
- `public/support.php`
- `public/clients.php`
- `public/operations.php`
- `public/platforms.php`
- `public/documents.php`
- `public/reports.php`

Legacy pages now redirect to relevant new modules:
- `public/visitors.php` -> `reception.php`
- `public/appointments.php` -> `secretary.php`
- `public/correspondence.php` -> `secretary.php`
- `public/rooms.php` -> `operations.php`
- `public/records.php` -> `reports.php`

## 3) Database

Schema file:
- `database/schema.sql`

Includes:
- users
- visitors
- appointments
- correspondence
- hr_employees
- hr_leave_requests
- finance_transactions
- clients
- support_tickets
- service_jobs
- device_inventory
- enterprise_workflows
- devops_pipelines
- documents

Seed users:
- `admin`
- `reception`
- `support1`

## 4) Local Testing (WampServer) - Step by Step

1. Place project in Wamp web root (already in your case):
   - `C:\wamp64\www\NabtaTech_Solutions\Office_Admin_System`

2. Create environment file:
   - Copy `.env.example` to `.env`.
   - Set values (default Wamp usually works):
     - `DB_HOST=127.0.0.1`
     - `DB_PORT=3306`
     - `DB_NAME=nabtatech_office`
     - `DB_USER=root`
     - `DB_PASS=` (empty unless your MySQL has password)

3. Import database schema:
   - Open phpMyAdmin (`http://localhost/phpmyadmin`)
   - Create/import using `database/schema.sql`
   - Or CLI:
     ```powershell
     mysql -u root -p < database\schema.sql
     ```

4. Optional: normalize demo passwords:
   ```powershell
   cd C:\wamp64\www\NabtaTech_Solutions\Office_Admin_System
   php reset_passwords.php
   ```

5. Open app:
   - `http://localhost/NabtaTech_Solutions/Office_Admin_System/public/login.php`

6. Login credentials for testing:
   - Username: `admin`
   - Password: `admin123` (after reset script)

7. Functional test checklist (recommended exact order):
   - Dashboard: confirm KPI cards load.
   - Reception: create visitor -> click `Check-Out`.
   - Secretary: create schedule item -> click `Complete`; create correspondence -> click `Close`.
   - HR: add employee; submit leave request; click `Approve`.
   - Finance: add transaction; click `Mark Paid` on pending row.
   - Clients: add new client record.
   - Support: create ticket linked to client; click `Resolve`.
   - Operations: create service job; click `Complete`; add device asset.
   - Platforms: add enterprise workflow and pipeline.
   - Documents: add a document record.
   - Reports: verify KPIs reflect created data.

8. Syntax sanity check (already used):
   ```powershell
   Get-ChildItem -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }
   ```

## 5) Deploy to Railway - Step by Step

Files already prepared:
- `Dockerfile`
- `apache.conf`
- `railway.json`

1. Push repository to GitHub.

2. In Railway:
   - Create new project.
   - Add service from GitHub repo.
   - Add **MySQL** service.

3. Configure app environment variables in Railway app service:
- `DB_HOST=${{MYSQLHOST}}`
- `DB_PORT=${{MYSQLPORT}}`
- `DB_NAME=${{MYSQLDATABASE}}`
- `DB_USER=${{MYSQLUSER}}`
- `DB_PASS=${{MYSQLPASSWORD}}`
- `APP_NAME=NabtaTech Solutions`

4. Deploy app service (Railway builds using Dockerfile).

5. Import schema into Railway MySQL:
   - Use Railway MySQL connection details in local DB client, or Railway shell.
   - Run `database/schema.sql`.

6. Post-deploy checks:
   - Open Railway app URL -> login page loads.
   - Login with seeded user.
   - Execute same functional checklist used locally.

## 6) Notes

- This is a simulation platform for portfolio/demo purposes.
- Add role-based authorization rules next if you want per-module access control.
