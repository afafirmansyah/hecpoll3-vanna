-- Update permissions to match existing structure

DECLARE @now DATETIME2 = GETDATE();

-- Update existing permissions to match the structure
UPDATE PERMISSIONS SET display_name = 'View Dashboard', description = 'View Dashboard' WHERE name = 'view_dashboard';
UPDATE PERMISSIONS SET display_name = 'View Cards', description = 'View Cards' WHERE name = 'view_cards';
UPDATE PERMISSIONS SET display_name = 'Export Cards', description = 'Export Cards' WHERE name = 'export_cards';
UPDATE PERMISSIONS SET display_name = 'View Vehicles', description = 'View Vehicles' WHERE name = 'view_vehicles';
UPDATE PERMISSIONS SET display_name = 'Export Vehicles', description = 'Export Vehicles' WHERE name = 'export_vehicles';
UPDATE PERMISSIONS SET display_name = 'View Transactions', description = 'View Transactions' WHERE name = 'view_transactions';
UPDATE PERMISSIONS SET display_name = 'Export Transactions', description = 'Export Transactions' WHERE name = 'export_transactions';
UPDATE PERMISSIONS SET display_name = 'Edit Mileage in Transactions', description = 'Allow user to edit mileage values in transaction records' WHERE name = 'update_mileage';
UPDATE PERMISSIONS SET display_name = 'View Events', description = 'View Events' WHERE name = 'view_events';
UPDATE PERMISSIONS SET display_name = 'Export Events', description = 'Export Events' WHERE name = 'export_events';
UPDATE PERMISSIONS SET display_name = 'View Reconciliations', description = 'View Reconciliations' WHERE name = 'view_reconciliations';
UPDATE PERMISSIONS SET display_name = 'Export Reconciliations', description = 'Export Reconciliations' WHERE name = 'export_reconciliations';
UPDATE PERMISSIONS SET display_name = 'View Daily Reports', description = 'View Daily Reports' WHERE name = 'view_daily_reports';
UPDATE PERMISSIONS SET display_name = 'Export Daily Reports', description = 'Export Daily Reports' WHERE name = 'export_daily_reports';
UPDATE PERMISSIONS SET display_name = 'View Daily Ratio', description = 'View Daily Ratio' WHERE name = 'view_daily_ratio';
UPDATE PERMISSIONS SET display_name = 'Export Daily Ratio', description = 'Export Daily Ratio' WHERE name = 'export_daily_ratio';
UPDATE PERMISSIONS SET display_name = 'Manage Daily Ratio', description = 'Manage Daily Ratio' WHERE name = 'manage_daily_ratio';
UPDATE PERMISSIONS SET display_name = 'Manage Users', description = 'Manage Users' WHERE name = 'manage_users';

-- Insert missing permissions
INSERT INTO PERMISSIONS (name, display_name, description, created_at, updated_at)
SELECT * FROM (VALUES
    ('edit_transactions', 'Edit Transactions', NULL, @now, @now),
    ('view_only', 'View Only', NULL, @now, @now),
    ('edit_dashboard', 'Edit Dashboard Settings', NULL, @now, @now)
) AS v(name, display_name, description, created_at, updated_at)
WHERE NOT EXISTS (SELECT 1 FROM PERMISSIONS WHERE PERMISSIONS.name = v.name);

PRINT 'Permissions updated to match existing structure';

-- Show all permissions
SELECT id, name, display_name, description FROM PERMISSIONS ORDER BY id;