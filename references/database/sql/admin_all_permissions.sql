-- Give administrator role ALL permissions

-- Clear existing permissions for administrator role
DELETE FROM ROLE_PERMISSIONS WHERE role_id = 1;

-- Add ALL permissions to administrator role
INSERT INTO ROLE_PERMISSIONS (role_id, permission_id, created_at, updated_at)
SELECT 1, id, NULL, NULL
FROM PERMISSIONS;

PRINT 'Administrator role now has ALL permissions';

-- Show summary
SELECT 
    COUNT(*) as total_permissions_assigned,
    (SELECT COUNT(*) FROM PERMISSIONS) as total_permissions_available
FROM ROLE_PERMISSIONS 
WHERE role_id = 1;

-- Show all permissions for administrator
SELECT p.id, p.name, p.display_name
FROM ROLE_PERMISSIONS rp
JOIN PERMISSIONS p ON rp.permission_id = p.id
WHERE rp.role_id = 1
ORDER BY p.id;