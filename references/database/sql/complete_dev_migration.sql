-- Complete Production Migration Script with Dev Data
-- Run this script to create all tables and insert exact data from dev

-- Create ROLES table and insert data
IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='ROLES' AND xtype='U')
BEGIN
    CREATE TABLE ROLES (
        id BIGINT IDENTITY(1,1) PRIMARY KEY,
        name NVARCHAR(255) NOT NULL UNIQUE,
        display_name NVARCHAR(255) NOT NULL,
        description NTEXT NULL,
        created_at DATETIME2 NULL,
        updated_at DATETIME2 NULL
    );
    PRINT 'ROLES table created';
END

DELETE FROM ROLES;
SET IDENTITY_INSERT ROLES ON;
INSERT INTO ROLES (id, name, display_name, description, created_at, updated_at) VALUES
(1, 'administrator', 'Administrator', 'Full system access', '2025-12-03 08:21:44.957', '2025-12-03 08:21:44.957'),
(5, 'engineering', 'Engineering', NULL, '2025-12-03 14:33:49.863', '2025-12-05 00:34:53.300');
SET IDENTITY_INSERT ROLES OFF;
PRINT 'ROLES data inserted';

-- Create PERMISSIONS table and insert data
IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='PERMISSIONS' AND xtype='U')
BEGIN
    CREATE TABLE PERMISSIONS (
        id BIGINT IDENTITY(1,1) PRIMARY KEY,
        name NVARCHAR(255) NOT NULL UNIQUE,
        display_name NVARCHAR(255) NOT NULL,
        description NTEXT NULL,
        created_at DATETIME2 NULL,
        updated_at DATETIME2 NULL
    );
    PRINT 'PERMISSIONS table created';
END

DELETE FROM PERMISSIONS;
SET IDENTITY_INSERT PERMISSIONS ON;
INSERT INTO PERMISSIONS (id, name, display_name, description, created_at, updated_at) VALUES
(1, 'view_dashboard', 'View Dashboard', 'View Dashboard', '2025-12-03 08:21:45.010', '2025-12-03 08:21:45.010'),
(2, 'view_cards', 'View Cards', 'View Cards', '2025-12-03 08:21:45.030', '2025-12-03 08:21:45.030'),
(3, 'export_cards', 'Export Cards', 'Export Cards', '2025-12-03 08:21:45.037', '2025-12-03 08:21:45.037'),
(4, 'view_vehicles', 'View Vehicles', 'View Vehicles', '2025-12-03 08:21:45.040', '2025-12-03 08:21:45.040'),
(5, 'export_vehicles', 'Export Vehicles', 'Export Vehicles', '2025-12-03 08:21:45.043', '2025-12-03 08:21:45.043'),
(6, 'view_transactions', 'View Transactions', 'View Transactions', '2025-12-03 08:21:45.043', '2025-12-03 08:21:45.043'),
(7, 'export_transactions', 'Export Transactions', 'Export Transactions', '2025-12-03 08:21:45.047', '2025-12-03 08:21:45.047'),
(8, 'update_mileage', 'Edit Mileage in Transactions', 'Allow user to edit mileage values in transaction records', '2025-12-03 08:21:45.047', '2025-12-04 12:32:51.243'),
(9, 'view_events', 'View Events', 'View Events', '2025-12-03 08:21:45.050', '2025-12-03 08:21:45.050'),
(10, 'export_events', 'Export Events', 'Export Events', '2025-12-03 08:21:45.050', '2025-12-03 08:21:45.050'),
(11, 'view_reconciliations', 'View Reconciliations', 'View Reconciliations', '2025-12-03 08:21:45.053', '2025-12-03 08:21:45.053'),
(12, 'export_reconciliations', 'Export Reconciliations', 'Export Reconciliations', '2025-12-03 08:21:45.057', '2025-12-03 08:21:45.057'),
(13, 'view_daily_reports', 'View Daily Reports', 'View Daily Reports', '2025-12-03 08:21:45.057', '2025-12-03 08:21:45.057'),
(14, 'export_daily_reports', 'Export Daily Reports', 'Export Daily Reports', '2025-12-03 08:21:45.060', '2025-12-03 08:21:45.060'),
(15, 'view_daily_ratio', 'View Daily Ratio', 'View Daily Ratio', '2025-12-03 08:21:45.060', '2025-12-03 08:21:45.060'),
(16, 'export_daily_ratio', 'Export Daily Ratio', 'Export Daily Ratio', '2025-12-03 08:21:45.063', '2025-12-03 08:21:45.063'),
(17, 'manage_daily_ratio', 'Manage Daily Ratio', 'Manage Daily Ratio', '2025-12-03 08:21:45.063', '2025-12-03 08:21:45.063'),
(18, 'manage_users', 'Manage Users', 'Manage Users', '2025-12-03 08:21:45.067', '2025-12-03 08:21:45.067'),
(19, 'edit_transactions', 'Edit Transactions', NULL, '2025-12-03 14:29:12.577', '2025-12-03 14:29:12.577'),
(20, 'view_only', 'View Only', NULL, '2025-12-03 14:29:12.583', '2025-12-03 14:29:12.583'),
(21, 'edit_dashboard', 'Edit Dashboard Settings', NULL, '2025-12-04 11:01:22.797', '2025-12-04 11:01:22.797');
SET IDENTITY_INSERT PERMISSIONS OFF;
PRINT 'PERMISSIONS data inserted';

-- Create ROLE_PERMISSIONS table and insert data
IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='ROLE_PERMISSIONS' AND xtype='U')
BEGIN
    CREATE TABLE ROLE_PERMISSIONS (
        id BIGINT IDENTITY(1,1) PRIMARY KEY,
        role_id BIGINT NOT NULL,
        permission_id BIGINT NOT NULL,
        created_at DATETIME2 NULL,
        updated_at DATETIME2 NULL,
        CONSTRAINT FK_role_permissions_role_id FOREIGN KEY (role_id) REFERENCES ROLES(id) ON DELETE CASCADE,
        CONSTRAINT FK_role_permissions_permission_id FOREIGN KEY (permission_id) REFERENCES PERMISSIONS(id) ON DELETE CASCADE,
        CONSTRAINT UQ_role_permission UNIQUE (role_id, permission_id)
    );
    PRINT 'ROLE_PERMISSIONS table created';
END

DELETE FROM ROLE_PERMISSIONS;
SET IDENTITY_INSERT ROLE_PERMISSIONS ON;
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
(116, 5, 1, NULL, NULL),
(122, 1, 1, NULL, NULL),
(129, 1, 21, NULL, NULL),
(176, 5, 2, NULL, NULL),
(177, 5, 15, NULL, NULL),
(178, 5, 13, NULL, NULL),
(179, 5, 9, NULL, NULL),
(180, 5, 11, NULL, NULL),
(181, 5, 6, NULL, NULL),
(182, 5, 4, NULL, NULL),
(183, 5, 3, NULL, NULL),
(184, 5, 16, NULL, NULL),
(185, 5, 14, NULL, NULL),
(186, 5, 10, NULL, NULL),
(187, 5, 12, NULL, NULL),
(188, 5, 7, NULL, NULL),
(189, 5, 5, NULL, NULL),
(198, 5, 8, NULL, NULL),
(199, 5, 17, NULL, NULL);
SET IDENTITY_INSERT ROLE_PERMISSIONS OFF;
PRINT 'ROLE_PERMISSIONS data inserted';

-- Create TERMINAL_CONFIGURATIONS table and insert data
IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='TERMINAL_CONFIGURATIONS' AND xtype='U')
BEGIN
    CREATE TABLE TERMINAL_CONFIGURATIONS (
        id BIGINT IDENTITY(1,1) PRIMARY KEY,
        operation_type NVARCHAR(255) NOT NULL,
        terminal_ids NTEXT NOT NULL,
        created_at DATETIME2 NULL,
        updated_at DATETIME2 NULL
    );
    PRINT 'TERMINAL_CONFIGURATIONS table created';
END

DELETE FROM TERMINAL_CONFIGURATIONS;
SET IDENTITY_INSERT TERMINAL_CONFIGURATIONS ON;
INSERT INTO TERMINAL_CONFIGURATIONS (id, operation_type, terminal_ids, created_at, updated_at) VALUES
(1, 'decantation', '[3]', '2025-12-04 10:51:48.127', '2025-12-17 08:50:15.997'),
(2, 'toploading', '[1,2]', '2025-12-04 10:51:48.140', '2025-12-17 08:50:16.010'),
(3, 'fuel_dispensing', '[4,5,6]', '2025-12-04 10:51:48.143', '2025-12-04 10:51:48.143');
SET IDENTITY_INSERT TERMINAL_CONFIGURATIONS OFF;
PRINT 'TERMINAL_CONFIGURATIONS data inserted';

-- Update WEBUSERS table (add columns if not exist, then update existing users)
IF EXISTS (SELECT * FROM sysobjects WHERE name='WEBUSERS' AND xtype='U')
BEGIN
    -- Add missing columns
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('WEBUSERS') AND name = 'username')
        ALTER TABLE WEBUSERS ADD username NVARCHAR(255) NULL;
    
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('WEBUSERS') AND name = 'role_id')
    BEGIN
        ALTER TABLE WEBUSERS ADD role_id BIGINT NULL;
        ALTER TABLE WEBUSERS ADD CONSTRAINT FK_webusers_role_id FOREIGN KEY (role_id) REFERENCES ROLES(id) ON DELETE SET NULL;
    END
    
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('WEBUSERS') AND name = 'is_active')
        ALTER TABLE WEBUSERS ADD is_active BIT NOT NULL DEFAULT 1;
    
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('WEBUSERS') AND name = 'last_login_at')
        ALTER TABLE WEBUSERS ADD last_login_at DATETIME2 NULL;
    
    -- Update existing user
    UPDATE WEBUSERS 
    SET username = 'afafirmansyah', role_id = 1, is_active = 1
    WHERE email = 'fauzi@hectronic.in';
    
    PRINT 'WEBUSERS table updated';
END

PRINT '';
PRINT '=== Migration Completed Successfully ===';
PRINT 'Tables created and data imported from dev environment';
PRINT 'Login: fauzi@hectronic.in / existing password';
PRINT 'Role: Administrator with full access';