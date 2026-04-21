<p align="center">
  <img src="assets/images/logo.png" alt="OrbitCMS Logo" width="200">
</p>

<h1 align="center">OrbitCMS</h1>

A lightweight and modern Content Management System (CMS) designed for efficient employee and leave management. Built with modern PHP, OrbitCMS leverages a clean architecture and integrates with external APIs to provide robust functionality.

## ✨ Features

*   **Employee Management:** Core functionality for managing employee records.
*   **Leave Application System:** Allows employees to request and track leave.
*   **Holiday Integration:** Automatically fetches and displays public holidays using the Calendarific API.
*   **Location Services:** Utilizes the CountryStateCity API for location-based features.
*   **Email Notifications:** Sends reliable email notifications for key events using SMTP.

## ⚡ Recent Enhancements

This project is actively maintained. Recent updates have focused on improving performance, security, and user experience:

*   **Optimized Performance:** User data and notification counts are now intelligently cached in the session, significantly reducing unnecessary database queries on every page load.
*   **Dynamic UI:** The sidebar now instantly reflects changes made to the user's profile (e.g., name or profile picture) without requiring a re-login, thanks to improved session handling.
*   **Real-time Notifications:** The notification count for pending leaves automatically refreshes in the background when the user switches to the application tab, ensuring the information is always current.
*   **Security Hardening:** Input fields on the profile page have been secured against Cross-Site Scripting (XSS) attacks to protect user data.

## 🚀 Tech Stack

*   **Back-End:** PHP 8+
*   **Database:** MySQL
*   **Dependency Management:** Composer
*   **Key Libraries:**
    *   `vlucas/phpdotenv` for environment variable management.
    *   `phpmailer/phpmailer` for handling emails.
    *   `league/iso3166` for country and subdivision data.
*   **External APIs:**
    *   Calendarific API
    *   CountryStateCity API
    *   Brevo (via SMTP)

## 🔧 Getting Started

Follow these instructions to get a local copy up and running for development and testing purposes.

### Prerequisites

*   PHP 8.0 or higher
*   Composer
*   A web server (like Apache or Nginx)
*   A MySQL database server

### Installation

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/BobbyHOI/OrbitCMS.git
    cd OrbitCMS
    ```

2.  **Install PHP dependencies:**
    ```bash
    composer install
    ```

3.  **Run the Setup Wizard:**
    Navigate to `http://<your-local-domain>/database/setup.php` in your web browser and follow the on-screen instructions. The wizard will:
    *   Create your `.env` file.
    *   Set up the database.
    *   Import the necessary tables.
    *   Configure default admin and manager accounts.

4.  **Log In!**
    Once the setup is complete, you can log in to your new OrbitCMS instance.

## 🐳 Docker Setup (Alternative)

For a containerized setup, you can use the provided Docker configuration.

1.  **Build and run the containers:**
    ```bash
    docker-compose up -d --build
    ```
2.  **Access the setup wizard:**
    Navigate to `http://localhost:8080/database/setup.php` to configure the application.
    *Note: When filling out the setup form, use `db` as the **Database Host**.* 

## 🔐 Configuration

All configuration is handled via the setup wizard, which creates a `.env` file in the root of the project. For security reasons, **this file is not and should not be committed to version control.**

---

This project serves as a demonstration of modern PHP development practices, including secure credential management, dependency management with Composer, and integration with third-party APIs.
