# SD_SEC42_G01_42
# 🐾 VetGroom Hub: Veterinary and Grooming Appointment System

## Project Overview
The VetGroom Hub is a web-based application developed to streamline and automate appointment scheduling and service management for veterinary and grooming facilities. This system replaces manual processes to reduce human errors, prevent scheduling conflicts, and enhance the client experience through digital records and an online booking interface.

| Detail | Value |
| :--- | :--- |
| **System** | Web-Based Application |
| **Technology** | HTML, CSS, JavaScript, PHP, MySQL  |
| **Framework** | Bootstrap  |
| **User Roles** | Administrator, Staff, Customer, Guest  |
| **Repository Owner** | Low Jia Wei (Project Leader/GitHub Owner)  |
| **Final Documentation Due** | 31 October 2025  |

---

## 💻 Installation Manual

Follow these steps to set up and run the VetGroom Hub project locally.

### Step 1: Install a Local Web Server (XAMPP)

The project requires a PHP and MySQL environment. We recommend using XAMPP Server.

* **Download Link:** [https://www.apachefriends.org/download.html].

### Step 2: Set up Project Files

1.  Navigate to the installation directory of your local server (e.g., `C:\xampp\htdocs\` for XAMPP).
2.  Clone the project or copy the entire **`VETGROOM_HUB`** folder into this directory.
3.  The main website files are located in `VETGROOM_HUB/codes/`.

### Step 3: Database Setup

1.  Start your Apache and MySQL services via the XAMPP/WAMP control panel.
2.  Open your web browser and navigate to **phpMyAdmin** (usually `http://localhost/phpmyadmin`).
3.  **Create a New Database** named `if0_40217768_vetgroom` (same as the SQL dump).
4.  Select the new database, go to the **Import** tab, and upload the following file from the project's database folder:
    * `VETGROOM_HUB/database/if0_40217768_vetgroom.sql`

### ⚠️ Step 4: Code File Path Correction (Critical Requirement)

**ATTENTION PROGRAMMERS/INSTALLERS:**

The project files have been restructured to meet the requirement of separating code by user role. This means that **all relative file paths** (`include`, `require`, `href`, `action`) within the PHP and HTML code **must be updated** to reflect the new directory structure. Please check and modify file paths in the code base, especially for links to shared assets (CSS/JS) and core backend logic.

Follow these steps for file extraction if you don't want to update the code:
1.  Create a new folder with any name. This will be your main folder.
2.  Extract all the files from `VETGROOM_HUB/database/` into the main folder.
3.  Inside `VETGROOM_HUB/codes/`, continue extracting all the files inside `admin`, `assets`, `backend`, `customer`, `staff`, `guest`, and also `index.php` to the main folder.
4.  The system should work correctly.

### Step 5: Access the System

1.  Open your browser and navigate to the project's entry point:
    * **Local Link:** `http://localhost/index.php`
2.  **Live Link:** [https://vetgroomhub.free.nf]

---

## 🔗 Project Links

* **GitHub Repository (Source Code):** `https://github.com/jiawei916/SD_SEC42_G01_42`
* **Live Project Website:** [https://vetgroomhub.free.nf]

---

## 🔑 Access Credentials

Use the following test accounts to log in to the different user dashboards.

| Role | Username | Email | Password |
| :--- | :--- | :--- | :--- |
| **Administrator** | Administrator | admin@gmail.com | [password] |
| **Staff** | name | newstaff@gmail.com | [password] |
| **Customer** | Username | femic77057@artvara.com | [password] |
