-- Seeder Data

USE supplier_saas;

-- Create a Test Company
INSERT INTO companies (name, email, subscription_status, trial_ends_at) 
VALUES ('Acme Corp', 'admin@acme.com', 'active', '2030-01-01 00:00:00');

SET @company_id = LAST_INSERT_ID();

-- Create Company Admin
INSERT INTO users (company_id, name, email, password, role) 
VALUES (@company_id, 'Acme Admin', 'admin@acme.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'company_admin');
-- Password is 'password'

-- Create Evaluator
INSERT INTO users (company_id, name, email, password, role) 
VALUES (@company_id, 'John Evaluator', 'john@acme.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'evaluator');

-- Create Suppliers
INSERT INTO suppliers (company_id, name, contact_person, email, phone, address) VALUES 
(@company_id, 'Global Tech Supplies', 'Alice Smith', 'alice@globaltech.com', '555-0101', '123 Tech Blvd'),
(@company_id, 'Best Office Gear', 'Bob Jones', 'bob@bestoffice.com', '555-0102', '456 Paper Lane'),
(@company_id, 'Fast Logistics', 'Charlie Brown', 'charlie@fastlogistics.com', '555-0103', '789 Ship Rd');

-- Create Criteria
INSERT INTO criteria (company_id, name, weight, max_score) VALUES 
(@company_id, 'Quality', 40.00, 10),
(@company_id, 'Price', 30.00, 10),
(@company_id, 'Delivery Speed', 20.00, 10),
(@company_id, 'Communication', 10.00, 10);
