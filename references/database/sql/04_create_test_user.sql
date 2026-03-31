-- Create test user with simple password
-- Run this to create a user you can definitely login with

DECLARE @now DATETIME2 = GETDATE();
DECLARE @adminRoleId BIGINT;

-- Get admin role ID
SELECT @adminRoleId = id FROM ROLES WHERE name = 'administrator';

-- Delete existing test user if exists
DELETE FROM WEBUSERS WHERE email = 'admin@test.com';

-- Create simple test user
INSERT INTO WEBUSERS (name, username, email, password, role_id, is_active, email_verified_at, created_at, updated_at)
VALUES (
    'Test Admin',
    'admin',
    'admin@test.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password
    @adminRoleId,
    1,
    @now,
    @now,
    @now
);

PRINT 'Test user created:';
PRINT 'Email: admin@test.com';
PRINT 'Password: password';

-- Also update the main admin user with fresh hash
UPDATE WEBUSERS 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    role_id = @adminRoleId,
    is_active = 1,
    updated_at = @now
WHERE email = 'fauzi@hectronic.in';

PRINT 'Updated fauzi@hectronic.in password to: password';