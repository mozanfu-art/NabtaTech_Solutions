-- NabtaTech Solutions MSP Platform Schema

CREATE DATABASE IF NOT EXISTS nabtatech_office
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE nabtatech_office;

-- Core users and identity
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    role ENUM('admin', 'manager', 'receptionist', 'secretary', 'hr', 'finance', 'support', 'devops', 'account_manager') NOT NULL,
    department VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Internal operations
CREATE TABLE IF NOT EXISTS visitors (
    visitor_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    company_name VARCHAR(100),
    contact_phone VARCHAR(20),
    contact_email VARCHAR(100),
    purpose_of_visit TEXT,
    person_to_meet VARCHAR(100),
    department VARCHAR(50),
    badge_number VARCHAR(20) UNIQUE,
    check_in_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    check_out_time DATETIME NULL,
    status ENUM('checked_in', 'checked_out', 'cancelled') DEFAULT 'checked_in',
    notes TEXT,
    created_by VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_check_in (check_in_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS appointments (
    appointment_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(120) NOT NULL,
    participant_name VARCHAR(100) NOT NULL,
    participant_type ENUM('internal', 'client', 'vendor') DEFAULT 'internal',
    owner_name VARCHAR(100) NOT NULL,
    department VARCHAR(50),
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    duration_minutes INT DEFAULT 30,
    channel ENUM('onsite', 'online', 'phone') DEFAULT 'onsite',
    status ENUM('scheduled', 'confirmed', 'completed', 'cancelled') DEFAULT 'scheduled',
    notes TEXT,
    created_by VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_date_time (appointment_date, appointment_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS correspondence (
    correspondence_id INT AUTO_INCREMENT PRIMARY KEY,
    reference_number VARCHAR(50) UNIQUE,
    correspondence_type ENUM('email', 'letter', 'memo', 'ticket_update') NOT NULL,
    direction ENUM('incoming', 'outgoing') NOT NULL,
    from_sender VARCHAR(200),
    to_recipient VARCHAR(200),
    subject VARCHAR(300),
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    status ENUM('open', 'replied', 'forwarded', 'closed') DEFAULT 'open',
    department VARCHAR(50),
    assigned_to VARCHAR(100),
    summary TEXT,
    action_required BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS hr_employees (
    employee_id INT AUTO_INCREMENT PRIMARY KEY,
    employee_code VARCHAR(30) UNIQUE NOT NULL,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(120),
    phone VARCHAR(20),
    department VARCHAR(60),
    job_title VARCHAR(80),
    hire_date DATE,
    employment_status ENUM('active', 'on_leave', 'terminated') DEFAULT 'active',
    manager_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS hr_leave_requests (
    leave_id INT AUTO_INCREMENT PRIMARY KEY,
    employee_code VARCHAR(30) NOT NULL,
    leave_type ENUM('annual', 'sick', 'unpaid', 'emergency') NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    reason TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approved_by VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_emp_status (employee_code, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS finance_transactions (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_code VARCHAR(30) UNIQUE NOT NULL,
    transaction_date DATE NOT NULL,
    category ENUM('invoice', 'expense', 'payroll', 'subscription') NOT NULL,
    description VARCHAR(255),
    amount DECIMAL(12,2) NOT NULL,
    direction ENUM('inflow', 'outflow') NOT NULL,
    payment_status ENUM('pending', 'paid', 'overdue') DEFAULT 'pending',
    client_name VARCHAR(120),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_date (transaction_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Client technical operations
CREATE TABLE IF NOT EXISTS clients (
    client_id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(120) NOT NULL,
    industry VARCHAR(80),
    contact_person VARCHAR(120),
    contact_email VARCHAR(120),
    contact_phone VARCHAR(25),
    service_tier ENUM('basic', 'standard', 'premium') DEFAULT 'standard',
    contract_start DATE,
    contract_end DATE,
    status ENUM('active', 'inactive', 'prospect') DEFAULT 'active',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS support_tickets (
    ticket_id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_number VARCHAR(30) UNIQUE NOT NULL,
    client_id INT NULL,
    title VARCHAR(180) NOT NULL,
    issue_type ENUM('network', 'device', 'cloud', 'erp_crm', 'security', 'backup', 'user_admin', 'other') NOT NULL,
    severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    status ENUM('new', 'in_progress', 'waiting_client', 'resolved', 'closed') DEFAULT 'new',
    assigned_to VARCHAR(100),
    opened_by VARCHAR(100),
    resolution_notes TEXT,
    opened_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(client_id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_issue_type (issue_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS service_jobs (
    job_id INT AUTO_INCREMENT PRIMARY KEY,
    job_code VARCHAR(30) UNIQUE NOT NULL,
    client_id INT NULL,
    service_type ENUM('network_setup', 'device_installation', 'troubleshooting', 'cloud_services', 'business_systems', 'cybersecurity', 'documentation', 'backup_recovery') NOT NULL,
    scope_summary TEXT,
    engineer_name VARCHAR(100),
    start_date DATE,
    due_date DATE,
    status ENUM('planned', 'active', 'blocked', 'completed') DEFAULT 'planned',
    completion_percent INT DEFAULT 0,
    FOREIGN KEY (client_id) REFERENCES clients(client_id) ON DELETE SET NULL,
    INDEX idx_service_status (service_type, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS device_inventory (
    asset_id INT AUTO_INCREMENT PRIMARY KEY,
    asset_tag VARCHAR(30) UNIQUE NOT NULL,
    client_id INT NULL,
    asset_type ENUM('router', 'switch', 'firewall', 'server', 'workstation', 'laptop', 'printer', 'other') NOT NULL,
    vendor VARCHAR(60),
    model VARCHAR(80),
    serial_number VARCHAR(80),
    deployment_status ENUM('stock', 'deployed', 'maintenance', 'retired') DEFAULT 'stock',
    location VARCHAR(120),
    assigned_engineer VARCHAR(100),
    last_service_date DATE,
    next_service_date DATE,
    FOREIGN KEY (client_id) REFERENCES clients(client_id) ON DELETE SET NULL,
    INDEX idx_deployment_status (deployment_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Enterprise platforms
CREATE TABLE IF NOT EXISTS enterprise_workflows (
    workflow_id INT AUTO_INCREMENT PRIMARY KEY,
    workflow_code VARCHAR(30) UNIQUE NOT NULL,
    platform ENUM('erp', 'crm', 'sap_style', 'cloud') NOT NULL,
    workflow_name VARCHAR(160) NOT NULL,
    owner_name VARCHAR(100),
    stage ENUM('design', 'build', 'test', 'deploy', 'operate') DEFAULT 'design',
    status ENUM('on_track', 'at_risk', 'blocked', 'done') DEFAULT 'on_track',
    target_go_live DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_platform_status (platform, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS devops_pipelines (
    pipeline_id INT AUTO_INCREMENT PRIMARY KEY,
    pipeline_name VARCHAR(120) NOT NULL,
    repository VARCHAR(180),
    environment ENUM('dev', 'staging', 'production') NOT NULL,
    last_run_at DATETIME,
    last_result ENUM('success', 'failed', 'running', 'queued') DEFAULT 'queued',
    deploy_frequency ENUM('daily', 'weekly', 'on_demand') DEFAULT 'on_demand',
    owner_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS documents (
    document_id INT AUTO_INCREMENT PRIMARY KEY,
    document_number VARCHAR(50) UNIQUE NOT NULL,
    document_title VARCHAR(200) NOT NULL,
    document_type VARCHAR(50),
    category VARCHAR(50),
    department VARCHAR(50),
    version VARCHAR(10) DEFAULT '1.0',
    status ENUM('draft', 'pending_review', 'approved', 'archived') DEFAULT 'draft',
    author VARCHAR(100),
    reviewer VARCHAR(100),
    approved_by VARCHAR(100),
    review_date DATE,
    approval_date DATE,
    tags TEXT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed data
INSERT INTO users (username, password_hash, full_name, email, role, department) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mozan Ahmed', 'mozanfu@gmail.com', 'admin', 'Management'),
('reception', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Reception Desk', 'reception@nabtatech.com', 'receptionist', 'Reception'),
('support1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'IT Support Engineer', 'support@nabtatech.com', 'support', 'Technical Operations');

INSERT INTO clients (client_name, industry, contact_person, contact_email, contact_phone, service_tier, contract_start, contract_end, status, notes) VALUES
('Qatar Retail Group', 'Retail', 'Maha Khalid', 'maha@qrg.com', '+97450011223', 'premium', CURDATE() - INTERVAL 4 MONTH, CURDATE() + INTERVAL 8 MONTH, 'active', 'Managed infrastructure and service desk'),
('Desert Logistics', 'Logistics', 'Hassan Adel', 'hassan@desertlog.com', '+97444556677', 'standard', CURDATE() - INTERVAL 2 MONTH, CURDATE() + INTERVAL 10 MONTH, 'active', 'Network refresh and cloud migration'),
('Pearl Clinics', 'Healthcare', 'Dr. Sara T', 'it@pearlclinics.qa', '+97433334444', 'basic', CURDATE() - INTERVAL 1 MONTH, CURDATE() + INTERVAL 5 MONTH, 'active', 'Backup and endpoint support');

INSERT INTO support_tickets (ticket_number, client_id, title, issue_type, severity, status, assigned_to, opened_by) VALUES
('TKT-2026-1001', 1, 'Branch firewall VPN unstable', 'network', 'high', 'in_progress', 'Support L2', 'Maha Khalid'),
('TKT-2026-1002', 2, 'ERP login permission issue', 'erp_crm', 'medium', 'new', 'Support L1', 'Hassan Adel'),
('TKT-2026-1003', 3, 'Daily backup failed overnight', 'backup', 'critical', 'waiting_client', 'Support L3', 'IT Coordinator');

INSERT INTO service_jobs (job_code, client_id, service_type, scope_summary, engineer_name, start_date, due_date, status, completion_percent) VALUES
('JOB-2026-2101', 1, 'network_setup', 'Core switch replacement and VLAN redesign', 'Eng. Omar', CURDATE() - INTERVAL 7 DAY, CURDATE() + INTERVAL 5 DAY, 'active', 65),
('JOB-2026-2102', 2, 'cloud_services', 'Migrate file services to cloud storage with IAM hardening', 'Eng. Lina', CURDATE() - INTERVAL 3 DAY, CURDATE() + INTERVAL 12 DAY, 'active', 40),
('JOB-2026-2103', 3, 'backup_recovery', 'Backup policy design and restore validation drill', 'Eng. Hadi', CURDATE() - INTERVAL 1 DAY, CURDATE() + INTERVAL 6 DAY, 'planned', 10);

INSERT INTO enterprise_workflows (workflow_code, platform, workflow_name, owner_name, stage, status, target_go_live, notes) VALUES
('WF-ERP-101', 'erp', 'Procure-to-Pay Automation', 'PM Rania', 'test', 'on_track', CURDATE() + INTERVAL 30 DAY, 'Integrated approvals with finance controls'),
('WF-CRM-202', 'crm', 'Lead-to-Cash Sales Workflow', 'PM Kareem', 'build', 'at_risk', CURDATE() + INTERVAL 21 DAY, 'Pending API integration with call center'),
('WF-SAP-303', 'sap_style', 'Ticket-to-Billing Service Chain', 'PM Tarek', 'deploy', 'on_track', CURDATE() + INTERVAL 14 DAY, 'Cross-module notifications enabled');

INSERT INTO devops_pipelines (pipeline_name, repository, environment, last_run_at, last_result, deploy_frequency, owner_name) VALUES
('nabtatech-msp-api', 'github.com/nabtatech/msp-api', 'staging', NOW() - INTERVAL 5 HOUR, 'success', 'daily', 'DevOps Team'),
('nabtatech-portal', 'github.com/nabtatech/client-portal', 'production', NOW() - INTERVAL 2 DAY, 'failed', 'weekly', 'DevOps Team');

INSERT INTO hr_employees (employee_code, full_name, email, phone, department, job_title, hire_date, employment_status, manager_name) VALUES
('EMP-001', 'Lina Farouk', 'lina@nabtatech.com', '+97460000111', 'DevOps', 'DevOps Engineer', CURDATE() - INTERVAL 300 DAY, 'active', 'CTO'),
('EMP-002', 'Omar Khaled', 'omar@nabtatech.com', '+97460000112', 'Support', 'Network Engineer', CURDATE() - INTERVAL 200 DAY, 'active', 'Support Manager');

INSERT INTO finance_transactions (transaction_code, transaction_date, category, description, amount, direction, payment_status, client_name) VALUES
('FIN-2026-001', CURDATE() - INTERVAL 3 DAY, 'invoice', 'Managed services monthly invoice', 18500.00, 'inflow', 'paid', 'Qatar Retail Group'),
('FIN-2026-002', CURDATE() - INTERVAL 2 DAY, 'expense', 'Firewall appliance procurement', 6200.00, 'outflow', 'paid', NULL),
('FIN-2026-003', CURDATE() - INTERVAL 1 DAY, 'payroll', 'February payroll batch', 41200.00, 'outflow', 'pending', NULL);

INSERT INTO documents (document_number, document_title, document_type, category, department, version, status, author, description) VALUES
('DOC-OPS-001', 'MSP Incident Response Playbook', 'Procedure', 'Security', 'Support', '1.2', 'approved', 'SOC Team', 'Incident handling for client environments'),
('DOC-OPS-002', 'Client Onboarding Checklist', 'Checklist', 'Operations', 'Account Management', '2.0', 'approved', 'PMO', 'Standard onboarding steps for new managed services clients');
