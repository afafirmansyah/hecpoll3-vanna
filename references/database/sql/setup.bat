@echo off
REM Production Database Setup - SQL Server
REM Run this batch file to execute all SQL scripts

echo === Laravel BISM Production Database Setup ===
echo.

REM Check if sqlcmd is available
sqlcmd -? >nul 2>&1
if %errorlevel% neq 0 (
    echo Error: sqlcmd not found. Please install SQL Server Command Line Tools.
    echo Download from: https://docs.microsoft.com/en-us/sql/tools/sqlcmd-utility
    pause
    exit /b 1
)

REM Get database connection details
set /p SERVER="Enter SQL Server name (e.g., localhost\SQLEXPRESS): "
set /p DATABASE="Enter database name: "
set /p USERNAME="Enter username (or press Enter for Windows Authentication): "

if "%USERNAME%"=="" (
    set AUTH_PARAMS=-E
    echo Using Windows Authentication
) else (
    set /p PASSWORD="Enter password: "
    set AUTH_PARAMS=-U %USERNAME% -P %PASSWORD%
    echo Using SQL Server Authentication
)

echo.
echo Connecting to: %SERVER%
echo Database: %DATABASE%
echo.

REM Test connection
echo Testing database connection...
sqlcmd -S %SERVER% -d %DATABASE% %AUTH_PARAMS% -Q "SELECT GETDATE() as CurrentTime" -h -1
if %errorlevel% neq 0 (
    echo Error: Cannot connect to database. Please check your connection details.
    pause
    exit /b 1
)

echo Connection successful!
echo.

REM Run the setup scripts
echo Running database setup scripts...
echo.

echo 1. Creating database structure...
sqlcmd -S %SERVER% -d %DATABASE% %AUTH_PARAMS% -i "01_create_tables.sql"
if %errorlevel% neq 0 (
    echo Error running 01_create_tables.sql
    pause
    exit /b 1
)

echo 2. Inserting data...
sqlcmd -S %SERVER% -d %DATABASE% %AUTH_PARAMS% -i "02_insert_data.sql"
if %errorlevel% neq 0 (
    echo Error running 02_insert_data.sql
    pause
    exit /b 1
)

echo 3. Assigning permissions...
sqlcmd -S %SERVER% -d %DATABASE% %AUTH_PARAMS% -i "03_assign_permissions.sql"
if %errorlevel% neq 0 (
    echo Error running 03_assign_permissions.sql
    pause
    exit /b 1
)

echo.
echo === Setup Complete ===
echo ✓ Database structure created/updated
echo ✓ Roles and permissions system installed
echo ✓ Default admin user created
echo ✓ Terminal configurations added
echo.
echo === Login Credentials ===
echo Email: fauzi@hectronic.in
echo Password: password (change immediately!)
echo.
echo Production setup completed successfully!
pause