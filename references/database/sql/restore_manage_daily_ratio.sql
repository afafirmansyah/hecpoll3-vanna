-- Restore manage_daily_ratio permission for menu control

DECLARE @now DATETIME2 = GETDATE();

-- Add back manage_daily_ratio permission
INSERT INTO PERMISSIONS (name, display_name, description, created_at, updated_at)
SELECT * FROM (VALUES
    ('manage_daily_ratio', 'Manage Daily Ratio', 'Access to daily ratio management menu', @now, @now)
) AS v(name, display_name, description, created_at, updated_at)
WHERE NOT EXISTS (SELECT 1 FROM PERMISSIONS WHERE PERMISSIONS.name = v.name);

-- Assign to administrator role
DECLARE @adminRoleId BIGINT;
SELECT @adminRoleId = id FROM ROLES WHERE name = 'administrator';

INSERT INTO ROLE_PERMISSIONS (role_id, permission_id, created_at, updated_at)
SELECT @adminRoleId, p.id, @now, @now
FROM PERMISSIONS p
WHERE p.name = 'manage_daily_ratio'
AND NOT EXISTS (
    SELECT 1 FROM ROLE_PERMISSIONS rp 
    WHERE rp.role_id = @adminRoleId AND rp.permission_id = p.id
);

PRINT 'Restored manage_daily_ratio permission for menu visibility control';

-- Show daily ratio permissions
SELECT id, name, display_name, description 
FROM PERMISSIONS 
WHERE name LIKE '%daily_ratio%' 
ORDER BY id;