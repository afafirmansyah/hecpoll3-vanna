-- Assign Permissions to Roles
-- SQL Server Compatible Script

DECLARE @now DATETIME2 = GETDATE();
DECLARE @adminRoleId BIGINT, @editorRoleId BIGINT, @viewerRoleId BIGINT, @mileageEditorRoleId BIGINT;

-- Get role IDs
SELECT @adminRoleId = id FROM ROLES WHERE name = 'administrator';
SELECT @editorRoleId = id FROM ROLES WHERE name = 'editor';
SELECT @viewerRoleId = id FROM ROLES WHERE name = 'viewer';
SELECT @mileageEditorRoleId = id FROM ROLES WHERE name = 'mileage_editor';

-- Administrator gets ALL permissions
INSERT INTO ROLE_PERMISSIONS (role_id, permission_id, created_at, updated_at)
SELECT @adminRoleId, p.id, @now, @now
FROM PERMISSIONS p
WHERE NOT EXISTS (
    SELECT 1 FROM ROLE_PERMISSIONS rp 
    WHERE rp.role_id = @adminRoleId AND rp.permission_id = p.id
);

-- Editor permissions
INSERT INTO ROLE_PERMISSIONS (role_id, permission_id, created_at, updated_at)
SELECT @editorRoleId, p.id, @now, @now
FROM PERMISSIONS p
WHERE p.name IN (
    'view_dashboard', 'view_cards', 'export_cards', 'view_vehicles', 'export_vehicles',
    'view_transactions', 'export_transactions', 'edit_transactions', 'update_mileage',
    'view_reconciliations', 'export_reconciliations', 'view_daily_reports', 'export_daily_reports',
    'view_daily_ratio', 'export_daily_ratio', 'view_events', 'export_events', 'view_users'
)
AND NOT EXISTS (
    SELECT 1 FROM ROLE_PERMISSIONS rp 
    WHERE rp.role_id = @editorRoleId AND rp.permission_id = p.id
);

-- Viewer permissions
INSERT INTO ROLE_PERMISSIONS (role_id, permission_id, created_at, updated_at)
SELECT @viewerRoleId, p.id, @now, @now
FROM PERMISSIONS p
WHERE p.name IN (
    'view_dashboard', 'view_cards', 'view_vehicles', 'view_transactions',
    'view_reconciliations', 'view_daily_reports', 'view_daily_ratio', 'view_events'
)
AND NOT EXISTS (
    SELECT 1 FROM ROLE_PERMISSIONS rp 
    WHERE rp.role_id = @viewerRoleId AND rp.permission_id = p.id
);

-- Mileage Editor permissions
INSERT INTO ROLE_PERMISSIONS (role_id, permission_id, created_at, updated_at)
SELECT @mileageEditorRoleId, p.id, @now, @now
FROM PERMISSIONS p
WHERE p.name IN ('view_dashboard', 'view_transactions', 'update_mileage')
AND NOT EXISTS (
    SELECT 1 FROM ROLE_PERMISSIONS rp 
    WHERE rp.role_id = @mileageEditorRoleId AND rp.permission_id = p.id
);

PRINT 'Role permissions assigned';

-- Create default admin user
IF NOT EXISTS (SELECT 1 FROM WEBUSERS WHERE email = 'fauzi@hectronic.in')
BEGIN
    INSERT INTO WEBUSERS (name, username, email, password, role_id, is_active, email_verified_at, created_at, updated_at)
    VALUES (
        'Ahmad Fauzi Firmansyah',
        'afafirmansyah', 
        'fauzi@hectronic.in',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- Hash for 'password'
        @adminRoleId,
        1,
        @now,
        @now,
        @now
    );
    PRINT 'Admin user created';
END
ELSE
BEGIN
    UPDATE WEBUSERS 
    SET role_id = @adminRoleId, is_active = 1, updated_at = @now
    WHERE email = 'fauzi@hectronic.in';
    PRINT 'Admin user updated';
END

-- Update existing users without roles to admin
UPDATE WEBUSERS 
SET role_id = @adminRoleId, is_active = 1, updated_at = @now
WHERE role_id IS NULL;

PRINT 'Existing users updated with admin role';

-- Create test user for easy login
IF NOT EXISTS (SELECT 1 FROM WEBUSERS WHERE email = 'admin@test.com')
BEGIN
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
    PRINT 'Test user created: admin@test.com / password';
END

-- Show created users
SELECT 
    id, name, username, email, 
    CASE WHEN is_active = 1 THEN 'Active' ELSE 'Inactive' END as status,
    r.display_name as role
FROM WEBUSERS u
LEFT JOIN ROLES r ON u.role_id = r.id
WHERE u.email IN ('fauzi@hectronic.in', 'admin@test.com')
ORDER BY u.created_at DESC;

PRINT 'Role assignment completed!';