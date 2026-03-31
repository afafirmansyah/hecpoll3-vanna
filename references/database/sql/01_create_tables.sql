-- Production Database Setup for Laravel BISM
-- SQL Server Compatible Script

-- Check if tables exist and create them if they don't

-- Create ROLES table
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
ELSE
    PRINT 'ROLES table already exists';

-- Create PERMISSIONS table
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
ELSE
    PRINT 'PERMISSIONS table already exists';

-- Create ROLE_PERMISSIONS table
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
ELSE
    PRINT 'ROLE_PERMISSIONS table already exists';

-- Check if WEBUSERS table exists, if not create it
IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='WEBUSERS' AND xtype='U')
BEGIN
    CREATE TABLE WEBUSERS (
        id BIGINT IDENTITY(1,1) PRIMARY KEY,
        name NVARCHAR(255) NOT NULL,
        email NVARCHAR(255) NOT NULL UNIQUE,
        email_verified_at DATETIME2 NULL,
        password NVARCHAR(255) NOT NULL,
        remember_token NVARCHAR(100) NULL,
        created_at DATETIME2 NULL,
        updated_at DATETIME2 NULL,
        username NVARCHAR(255) NULL,
        role_id BIGINT NULL,
        is_active BIT NOT NULL DEFAULT 1,
        last_login_at DATETIME2 NULL,
        CONSTRAINT FK_webusers_role_id FOREIGN KEY (role_id) REFERENCES ROLES(id) ON DELETE SET NULL
    );
    PRINT 'WEBUSERS table created';
END
ELSE
BEGIN
    PRINT 'WEBUSERS table exists, adding missing columns...';
    
    -- Add username column if not exists
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('WEBUSERS') AND name = 'username')
    BEGIN
        ALTER TABLE WEBUSERS ADD username NVARCHAR(255) NULL;
        PRINT 'Added username column to WEBUSERS';
    END

    -- Add role_id column if not exists
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('WEBUSERS') AND name = 'role_id')
    BEGIN
        ALTER TABLE WEBUSERS ADD role_id BIGINT NULL;
        ALTER TABLE WEBUSERS ADD CONSTRAINT FK_webusers_role_id FOREIGN KEY (role_id) REFERENCES ROLES(id) ON DELETE SET NULL;
        PRINT 'Added role_id column to WEBUSERS';
    END

    -- Add is_active column if not exists
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('WEBUSERS') AND name = 'is_active')
    BEGIN
        ALTER TABLE WEBUSERS ADD is_active BIT NOT NULL DEFAULT 1;
        PRINT 'Added is_active column to WEBUSERS';
    END

    -- Add last_login_at column if not exists
    IF NOT EXISTS (SELECT * FROM sys.columns WHERE object_id = OBJECT_ID('WEBUSERS') AND name = 'last_login_at')
    BEGIN
        ALTER TABLE WEBUSERS ADD last_login_at DATETIME2 NULL;
        PRINT 'Added last_login_at column to WEBUSERS';
    END
END

-- Create TERMINAL_CONFIGURATIONS table
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
ELSE
    PRINT 'TERMINAL_CONFIGURATIONS table already exists';

-- Create USER_ACCESS table
IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='USER_ACCESS' AND xtype='U')
BEGIN
    CREATE TABLE USER_ACCESS (
        id BIGINT IDENTITY(1,1) PRIMARY KEY,
        user_id BIGINT NOT NULL,
        station_ids NTEXT NULL,
        terminal_ids NTEXT NULL,
        created_at DATETIME2 NULL,
        updated_at DATETIME2 NULL,
        CONSTRAINT FK_user_access_user_id FOREIGN KEY (user_id) REFERENCES WEBUSERS(id) ON DELETE CASCADE
    );
    PRINT 'USER_ACCESS table created';
END
ELSE
    PRINT 'USER_ACCESS table already exists';

PRINT 'Database structure setup completed!';