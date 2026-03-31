#######################################################
Fuel Management System - BISM Project
#######################################################

This repository contains a customized **Fuel Management System (FMS)** specifically developed for **BISM**. Built using the **Laravel Framework**, this dashboard serves as an enterprise-grade interface for the **Hectronic Hecpoll 3** system, providing tailored analytics and operational monitoring for fleet and fuel inventory.

*******************
Key Features
*******************

- **BISM Custom Analytics:** Tailored KPI tracking designed to meet BISM's specific operational requirements.
- **Hecpoll 3 Integration:** Robust data synchronization with Hectronic Hecpoll 3 hardware and databases.
- **Advanced Fleet Monitoring:** Detailed logs of fuel transactions per vehicle, driver, and site location.
- **Automated Reporting:** Specialized reporting module for BISM's periodic fuel consumption and cost analysis.
- **Secure Access Control:** Multi-level user permissions to ensure data integrity and security.

**************************
Technical Specifications
**************************

- **Framework:** Laravel 12.x
- **Language:** PHP 8.3+
- **Database:** Microsoft SQL
- **UI Stack:** Bootstrap 5 / Tailwind CSS
- **System Integration:** Hectronic Hecpoll 3 (API/Database)

*******************
Installation Guide
*******************

1. **Clone the Project**
   .. code-block:: bash

      git clone https://github.com/afafirmansyah/fuel-management-system.git

2. **Project Setup**
   - Install PHP dependencies:
     
     .. code-block:: bash

        composer install

   - Create environment file and generate key:
     
     .. code-block:: bash

        cp .env.example .env
        php artisan key:generate

3. **Database Configuration**
   - Configure your database credentials in the ``.env`` file.
   - Run the migrations and seeders:
     
     .. code-block:: bash

        php artisan migrate --seed

4. **Frontend Assets**
   - Install and compile assets:
     
     .. code-block:: bash

        npm install && npm run build

5. **Run the Application**
   - Start the local server:
     
     .. code-block:: bash

        php artisan serve

*******************
Dashboard Interface
*******************

[Insert Screenshot of the BISM Dashboard here to showcase the specialized UI]

*******
License
*******

This project is licensed under the MIT License - see the `license.txt` file for details.

*********
Contact
*********

**Ahmad Fauzi Firmansyah**
- **GitHub:** `afafirmansyah <https://github.com/afafirmansyah>`_
- **LinkedIn:** `ahmad-fauzi-firmansyah <https://linkedin.com/in/ahmad-fauzi-firmansyah/>`_
