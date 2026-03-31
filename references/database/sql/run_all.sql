-- Master Production Setup Script
-- Run this script to set up the complete database structure and data

PRINT '=== Laravel BISM Production Setup ===';
PRINT 'Starting database setup...';

-- Step 1: Create tables and modify existing ones
PRINT '';
PRINT '1. Creating database structure...';
:r 01_create_tables.sql

-- Step 2: Insert basic data
PRINT '';
PRINT '2. Inserting permissions, roles, and configurations...';
:r 02_insert_data.sql

-- Step 3: Assign permissions to roles and create users
PRINT '';
PRINT '3. Assigning permissions and creating users...';
:r 03_assign_permissions.sql

PRINT '';
PRINT '=== Setup Summary ===';
PRINT '✓ Database structure created/updated';
PRINT '✓ Roles and permissions system installed';
PRINT '✓ Default admin user: fauzi@hectronic.in';
PRINT '✓ Terminal configurations added';
PRINT '';
PRINT '=== Login Credentials ===';
PRINT 'Email: fauzi@hectronic.in';
PRINT 'Password: password (change immediately!)';
PRINT '';
PRINT 'Production setup completed successfully!';