# SQL Production Setup Guide

## Files Created

1. **01_create_tables.sql** - Creates database structure (tables, columns, constraints)
2. **02_insert_data.sql** - Inserts permissions, roles, and terminal configurations
3. **03_assign_permissions.sql** - Assigns permissions to roles and creates admin user
4. **run_all.sql** - Master script that runs all scripts (for SQL Server Management Studio)
5. **setup.bat** - Windows batch file for automated setup

## Quick Setup (Recommended)

### Option 1: Using Batch File (Windows)
```cmd
cd database\sql
setup.bat
```
Follow the prompts to enter your database connection details.

### Option 2: Using SQL Server Management Studio
1. Open SQL Server Management Studio
2. Connect to your database
3. Open and execute `run_all.sql`

### Option 3: Manual Execution
Run each script in order:
```cmd
sqlcmd -S server_name -d database_name -E -i "01_create_tables.sql"
sqlcmd -S server_name -d database_name -E -i "02_insert_data.sql"
sqlcmd -S server_name -d database_name -E -i "03_assign_permissions.sql"
```

## What Gets Created

### Tables
- **ROLES** - User roles (Administrator, Editor, Viewer, Mileage Editor)
- **PERMISSIONS** - System permissions
- **ROLE_PERMISSIONS** - Links roles to permissions
- **TERMINAL_CONFIGURATIONS** - Terminal operation configurations
- **USER_ACCESS** - User access control (if WEBUSERS exists)

### WEBUSERS Table Updates
If WEBUSERS table exists, these columns are added:
- `username` - User login name
- `role_id` - Link to ROLES table
- `is_active` - User active status
- `last_login_at` - Last login timestamp

### Default Data
- **20 Permissions** - All system permissions
- **4 Roles** - Administrator, Editor, Viewer, Mileage Editor
- **Admin User** - fauzi@hectronic.in (password: "password")
- **Terminal Configs** - Decantation, Toploading, Fuel Dispensing

## Default Login
- **Email**: fauzi@hectronic.in
- **Password**: password
- **Role**: Administrator

**⚠️ Change the password immediately after first login!**

## Roles and Permissions

### Administrator
- Full access to everything
- Can manage users and roles

### Editor
- View and edit most data
- Cannot manage users
- Can edit transactions and mileage

### Viewer
- Read-only access to all data
- Cannot edit anything

### Mileage Editor
- Can only view transactions
- Can edit mileage values
- Limited dashboard access

## Troubleshooting

### Connection Issues
- Ensure SQL Server is running
- Check server name and database name
- Verify authentication method (Windows vs SQL Server)
- Check firewall settings

### Permission Errors
- Ensure database user has CREATE, ALTER, INSERT permissions
- Run as database owner or sysadmin

### Script Errors
- Check if tables already exist (scripts are safe to re-run)
- Verify SQL Server version compatibility
- Check for syntax errors in output

## Safety Features

- ✅ Checks if tables/columns exist before creating
- ✅ Uses INSERT WHERE NOT EXISTS to avoid duplicates
- ✅ Safe to run multiple times
- ✅ Preserves existing data
- ✅ Compatible with SQL Server 2012+

## Rollback

If you need to remove the role system:
```sql
-- Remove foreign keys first
ALTER TABLE WEBUSERS DROP CONSTRAINT FK_webusers_role_id;
ALTER TABLE USER_ACCESS DROP CONSTRAINT FK_user_access_user_id;

-- Remove added columns
ALTER TABLE WEBUSERS DROP COLUMN role_id, is_active, username, last_login_at;

-- Drop tables
DROP TABLE USER_ACCESS;
DROP TABLE TERMINAL_CONFIGURATIONS;
DROP TABLE ROLE_PERMISSIONS;
DROP TABLE PERMISSIONS;
DROP TABLE ROLES;
```