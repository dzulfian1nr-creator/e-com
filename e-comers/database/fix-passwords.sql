USE e_commerce;

-- Reset passwords dengan hash yang benar untuk password 'admin123'
UPDATE users SET password = '$2y$10$yHr/eNRQZ32tKDqP5V7t4ug6pWoJGum9CTM0/e4kCZ9XvDtZ8X8mC' WHERE email = 'admin@ecommerce.com';
UPDATE users SET password = '$2y$10$yHr/eNRQZ32tKDqP5V7t4ug6pWoJGum9CTM0/e4kCZ9XvDtZ8X8mC' WHERE email = 'john@example.com';
UPDATE users SET password = '$2y$10$yHr/eNRQZ32tKDqP5V7t4ug6pWoJGum9CTM0/e4kCZ9XvDtZ8X8mC' WHERE email = 'jane@example.com';

-- Verify
SELECT 'Hash update completed' as status;
SELECT email, CONCAT(LEFT(password, 20), '...') as password_hash, LENGTH(password) as hash_length FROM users;
