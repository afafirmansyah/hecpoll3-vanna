-- Remove manage_daily_ratio permission since daily ratio has no input/delete functionality

-- Remove from role_permissions first
DELETE FROM ROLE_PERMISSIONS 
WHERE permission_id IN (SELECT id FROM PERMISSIONS WHERE name = 'manage_daily_ratio');

-- Remove the permission
DELETE FROM PERMISSIONS WHERE name = 'manage_daily_ratio';

PRINT 'Removed manage_daily_ratio permission - daily ratio is view/export only';

-- Show remaining permissions
SELECT id, name, display_name, description 
FROM PERMISSIONS 
WHERE name LIKE '%daily_ratio%' 
ORDER BY id;