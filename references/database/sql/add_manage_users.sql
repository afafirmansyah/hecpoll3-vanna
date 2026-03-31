-- Add manage_users permission back to role_id = 1

DECLARE @now DATETIME2 = GETDATE();

-- Check if manage_users permission exists
IF NOT EXISTS (SELECT 1 FROM PERMISSIONS WHERE name = 'manage_users')
BEGIN
    INSERT INTO PERMISSIONS (name, display_name, description, created_at, updated_at)
    VALUES ('manage_users', 'Manage Users', 'Manage Users', @now, @now);
    PRINT 'Created manage_users permission';
END

-- Get permission ID
DECLARE @manageUsersPermissionId BIGINT;
SELECT @manageUsersPermissionId = id FROM PERMISSIONS WHERE name = 'manage_users';

-- Add to administrator role if not exists
IF NOT EXISTS (SELECT 1 FROM ROLE_PERMISSIONS WHERE role_id = 1 AND permission_id = @manageUsersPermissionId)
BEGIN
    INSERT INTO ROLE_PERMISSIONS (role_id, permission_id, created_at, updated_at)
    VALUES (1, @manageUsersPermissionId, NULL, NULL);
    PRINT 'Added manage_users permission to Administrator role';
END

-- Show current permissions for role_id = 1
SELECT rp.id, rp.role_id, rp.permission_id, p.name, p.display_name
FROM ROLE_PERMISSIONS rp
JOIN PERMISSIONS p ON rp.permission_id = p.id
WHERE rp.role_id = 1
ORDER BY rp.permission_id;