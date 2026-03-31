-- Create 3 random test users with password = 'password'

DECLARE @now DATETIME2 = GETDATE();
DECLARE @passwordHash NVARCHAR(255) = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'; -- password

-- Insert 3 test users
INSERT INTO WEBUSERS (name, username, email, password, role_id, is_active, email_verified_at, created_at, updated_at) VALUES
('John Smith', 'johnsmith', 'john@test.com', @passwordHash, 1, 1, @now, @now, @now),
('Sarah Johnson', 'sarahjohnson', 'sarah@test.com', @passwordHash, 5, 1, @now, @now, @now),
('Mike Wilson', 'mikewilson', 'mike@test.com', @passwordHash, 1, 1, @now, @now, @now);

PRINT 'Created 3 test users:';
PRINT '1. john@test.com / password (Administrator)';
PRINT '2. sarah@test.com / password (Engineering)';
PRINT '3. mike@test.com / password (Administrator)';

-- Show created users
SELECT id, name, username, email, role_id, is_active
FROM WEBUSERS 
WHERE email IN ('john@test.com', 'sarah@test.com', 'mike@test.com');