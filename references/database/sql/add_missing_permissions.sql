-- Add missing management permissions

DECLARE @now DATETIME2 = GETDATE();

-- Insert missing permissions
INSERT INTO PERMISSIONS (name, display_name, description, created_at, updated_at)
SELECT * FROM (VALUES
    ('edit_dashboard_settings', 'Edit Dashboard Settings', 'Modify dashboard configuration and settings', @now, @now),
    ('edit_mileage_transactions', 'Edit Mileage in Transactions', 'Edit mileage values in transaction records', @now, @now),
    ('manage_daily_ratio', 'Manage Daily Ratio', 'Full management of daily ratio reports and settings', @now, @now),
    ('manage_users', 'Manage Users', 'Create, edit, delete user accounts', @now, @now)
) AS v(name, display_name, description, created_at, updated_at)
WHERE NOT EXISTS (SELECT 1 FROM PERMISSIONS WHERE PERMISSIONS.name = v.name);

-- Assign new permissions to administrator role
DECLARE @adminRoleId BIGINT;
SELECT @adminRoleId = id FROM ROLES WHERE name = 'administrator';

INSERT INTO ROLE_PERMISSIONS (role_id, permission_id, created_at, updated_at)
SELECT @adminRoleId, p.id, @now, @now
FROM PERMISSIONS p
WHERE p.name IN ('edit_dashboard_settings', 'edit_mileage_transactions', 'manage_daily_ratio', 'manage_users')
AND NOT EXISTS (
    SELECT 1 FROM ROLE_PERMISSIONS rp 
    WHERE rp.role_id = @adminRoleId AND rp.permission_id = p.id
);

PRINT 'Added missing management permissions';
PRINT '- Edit Dashboard Settings';
PRINT '- Edit Mileage in Transactions';
PRINT '- Manage Daily Ratio';
PRINT '- Manage Users';
PRINT 'Permissions assigned to Administrator role';