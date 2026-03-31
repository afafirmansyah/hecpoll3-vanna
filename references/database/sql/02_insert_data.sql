-- Insert Permissions Data
-- SQL Server Compatible Script

DECLARE @now DATETIME2 = GETDATE();

-- Insert permissions
INSERT INTO PERMISSIONS (name, display_name, description, created_at, updated_at)
SELECT * FROM (VALUES
    ('view_dashboard', 'View Dashboard', 'Access to main dashboard', @now, @now),
    ('view_cards', 'View Cards', 'View card information', @now, @now),
    ('export_cards', 'Export Cards', 'Export card data', @now, @now),
    ('view_vehicles', 'View Vehicles', 'View vehicle information', @now, @now),
    ('export_vehicles', 'Export Vehicles', 'Export vehicle data', @now, @now),
    ('view_transactions', 'View Transactions', 'View transaction records', @now, @now),
    ('export_transactions', 'Export Transactions', 'Export transaction data', @now, @now),
    ('edit_transactions', 'Edit Transactions', 'Modify transaction records', @now, @now),
    ('update_mileage', 'Update Mileage', 'Edit mileage in transactions', @now, @now),
    ('view_reconciliations', 'View Reconciliations', 'View reconciliation reports', @now, @now),
    ('export_reconciliations', 'Export Reconciliations', 'Export reconciliation data', @now, @now),
    ('view_daily_reports', 'View Daily Reports', 'View daily reports', @now, @now),
    ('export_daily_reports', 'Export Daily Reports', 'Export daily report data', @now, @now),
    ('view_daily_ratio', 'View Daily Ratio', 'View daily ratio reports', @now, @now),
    ('export_daily_ratio', 'Export Daily Ratio', 'Export daily ratio data', @now, @now),
    ('view_events', 'View Events', 'View system events', @now, @now),
    ('export_events', 'Export Events', 'Export event data', @now, @now),
    ('view_users', 'View Users', 'View user accounts', @now, @now),
    ('manage_users', 'Manage Users', 'Create, edit, delete users', @now, @now),
    ('manage_roles', 'Manage Roles', 'Manage user roles and permissions', @now, @now)
) AS v(name, display_name, description, created_at, updated_at)
WHERE NOT EXISTS (SELECT 1 FROM PERMISSIONS WHERE PERMISSIONS.name = v.name);

PRINT 'Permissions inserted';

-- Insert roles
INSERT INTO ROLES (name, display_name, description, created_at, updated_at)
SELECT * FROM (VALUES
    ('administrator', 'Administrator', 'Full system access with all permissions', @now, @now),
    ('editor', 'Editor', 'Can view and edit most data', @now, @now),
    ('viewer', 'Viewer', 'Read-only access to system data', @now, @now),
    ('mileage_editor', 'Mileage Editor', 'Can view transactions and edit mileage only', @now, @now)
) AS v(name, display_name, description, created_at, updated_at)
WHERE NOT EXISTS (SELECT 1 FROM ROLES WHERE ROLES.name = v.name);

PRINT 'Roles inserted';

-- Insert terminal configurations
INSERT INTO TERMINAL_CONFIGURATIONS (operation_type, terminal_ids, created_at, updated_at)
SELECT * FROM (VALUES
    ('decantation', '[3]', @now, @now),
    ('toploading', '[1,2]', @now, @now),
    ('fuel_dispensing', '[4,5,6]', @now, @now)
) AS v(operation_type, terminal_ids, created_at, updated_at)
WHERE NOT EXISTS (SELECT 1 FROM TERMINAL_CONFIGURATIONS WHERE TERMINAL_CONFIGURATIONS.operation_type = v.operation_type);

PRINT 'Terminal configurations inserted';

PRINT 'Data insertion completed!';