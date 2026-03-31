-- Update password for existing user
-- This will set password to 'password' for fauzi@hectronic.in

UPDATE WEBUSERS 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    updated_at = GETDATE()
WHERE email = 'fauzi@hectronic.in';

-- Verify the update
SELECT id, name, email, password, is_active 
FROM WEBUSERS 
WHERE email = 'fauzi@hectronic.in';

PRINT 'Password updated for fauzi@hectronic.in';
PRINT 'New password: password';