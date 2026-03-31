-- Insert role_permissions data to match existing structure

-- Clear existing role_permissions
DELETE FROM ROLE_PERMISSIONS;

-- Enable identity insert
SET IDENTITY_INSERT ROLE_PERMISSIONS ON;

-- Insert the exact data (only role_id = 1)
INSERT INTO ROLE_PERMISSIONS (id, role_id, permission_id, created_at, updated_at) VALUES
(84, 1, 2, NULL, NULL),
(85, 1, 3, NULL, NULL),
(86, 1, 4, NULL, NULL),
(87, 1, 5, NULL, NULL),
(88, 1, 6, NULL, NULL),
(89, 1, 7, NULL, NULL),
(90, 1, 8, NULL, NULL),
(91, 1, 9, NULL, NULL),
(92, 1, 10, NULL, NULL),
(93, 1, 11, NULL, NULL),
(94, 1, 12, NULL, NULL),
(95, 1, 13, NULL, NULL),
(96, 1, 14, NULL, NULL),
(97, 1, 15, NULL, NULL),
(98, 1, 16, NULL, NULL),
(99, 1, 17, NULL, NULL),
(100, 1, 18, NULL, NULL),
(122, 1, 1, NULL, NULL),
(129, 1, 21, NULL, NULL);

-- Disable identity insert
SET IDENTITY_INSERT ROLE_PERMISSIONS OFF;

-- Reset identity seed to continue from 130
DBCC CHECKIDENT ('ROLE_PERMISSIONS', RESEED, 129);

PRINT 'Role permissions data inserted successfully';

-- Show summary
SELECT 
    r.name as role_name,
    COUNT(*) as permission_count
FROM ROLE_PERMISSIONS rp
JOIN ROLES r ON rp.role_id = r.id
GROUP BY r.name, rp.role_id
ORDER BY rp.role_id;