
---

# SCANBL-3: Web-Based Barcode Scanner and Inventory Management System

## Overview

**SCANBL-3** is a web-based application developed for **PT. Meiji Pharmaceutical Indonesia** to streamline and enhance the process of barcode scanning and inventory management. This system replaces the legacy desktop application **SCANDB**, which was built using **C++ Visual Basic** and is no longer compatible with modern operating systems. SCANBL-3 is designed to be **multi-platform**, accessible via any modern web browser, and built using **HTML, CSS, JavaScript, PHP (Native)**, and **MySQL** for database management.

The application provides an efficient and holistic approach to managing inventory data through barcode scanning, enabling faster data input and real-time tracking of product information. SCANBL-3 is tailored to meet the specific needs of PT. Meiji Pharmaceutical Indonesia, ensuring seamless integration with their existing workflows.

---

## Key Features

### 1. **Landing Page**
   - Displays the company profile and a brief description of the SCANBL-3 application.
   - Serves as the entry point for users accessing the system.

### 2. **Login Page**
   - Allows users to log in as either an **Operator (User)** or **Admin**.
   - Different roles have access to different pages and functionalities:
     - **Operator**: Dashboard, Overview, Add Master, Add, Add Edit, Inventory, Log Activity, Preview, Profile, Report Product.
     - **Admin**: All Operator pages plus **Accounts** management.

### 3. **Dashboard Page**
   - Provides a comprehensive overview of system activities through visual statistics:
     - **Total Scans Today**: Displays the number of scans performed today.
     - **Active Lot**: Shows the number of active lot numbers.
     - **Active Product**: Displays the number of active products.
     - **Line Chart**: Tracks total scans and active lot numbers over a 30-day period.
     - **Pie Chart**: Visualizes product distribution and scan counts.
     - **Activity Trend Chart**: Tracks insertions, updates, and deletions over the last 30 days.
   - Built using **Chart.js** for dynamic and interactive data visualization.

### 4. **Overview Page**
   - Displays a table with columns: **No, Barcode/RSS Code, Product, Jam Code, Date, Counter, No. Lot, Action (Delete)**.
   - Includes a **search/filter** feature to find data by product or barcode/RSS code.
   - Notifications for found or missing data.

### 5. **Add Master Page**
   - Allows users to add product data to the `add_master` table in the database.
   - Fields: **Product Name, RSS Code, Jam Code**.
   - Features: **Add** button to save data, **Clear** button to reset fields.
   - Sets up data for dropdowns in the **Add Page**.

### 6. **Add Page**
   - Fields: **Product (dropdown), RSS Code (auto-filled), Jam Code (auto-filled), No. Lot, Date (auto-filled), Counter, System Counter (for barcode scanning)**.
   - Automatically sends data to the `add_product` table in the database after scanning.
   - Notifications for incomplete input.
   - **Clear** button to reset fields.

### 7. **Preview Page**
   - Allows users to select a product and lot number for previewing data.
   - **Preview** button redirects to the **Report Product Page**.
   - Notifications for incomplete selections.

### 8. **Report Product Page**
   - Displays data based on the selected product and lot number.
   - Table columns: **No, RSS Code, Product, Jam Code, Date, Counter, No. Lot**.
   - Export options: **PDF, Excel, Print**.
   - **Back** button to return to the Preview Page.

### 9. **Inventory Page**
   - Displays a table with columns: **No, Barcode (RSS Code), Product, Jam Code, Actions (Edit, Delete)**.
   - **Edit**: Modal window to update product data with validation for duplicate RSS codes.
   - **Delete**: Modal window to confirm deletion by entering the product name.
   - Search feature for filtering data by product or barcode/RSS code.

### 10. **Log Activity Page**
   - Displays a log of all activities performed by users.
   - Table columns: **No, Barcode, Product, Jam Code, Date, Counter, No. Lot, Change Type, Change Date, Operator**.
   - Tracks **Insert, Update, Delete** actions.
   - Search feature for filtering data by product, barcode/RSS code, or Jam Code.

### 11. **Accounts Page (Admin Only)**
   - Allows admins to add, edit, and delete user accounts.
   - Fields: **Name, Username, Password, Role (Operator/Admin), Date**.
   - **Edit**: Modal window to update account details with validation for duplicate usernames.
   - **Delete**: Modal window to confirm deletion by entering the username.
   - **Reset Password**: Resets password to default (`12345678`).
   - Search feature for filtering accounts by name or username.

### 12. **Profile Page (Operator/User)**
   - Displays user profile information and activity statistics.
   - Fields: **Name, Username, Date**.
   - **Edit**: Modal window to update the user's name.
   - **Change Password**: Modal window to update the password.
   - Visualizations:
     - **Hourly Activity Distribution (Line Chart)**: Tracks user activities by hour.
     - **Change Type Distribution (Pie Chart)**: Tracks insertions, updates, and deletions.
     - **Daily Progress (Line Chart)**: Tracks total products and lot numbers added.

---

## Security Features
SCANBL-3 incorporates several security measures to protect against common vulnerabilities:
1. **Prepared Statements**: Used for MySQL queries with bound parameters to prevent SQL injection.
2. **HTML Special Characters**: Ensures that any displayed data is rendered as text, preventing XSS attacks.
3. **Session Management**: Users must log in to access the system. Session validation (`!isset($_SESSION['user_id']`) ensures unauthorized users cannot access restricted pages.

---

## Technologies Used
- **Frontend**: HTML, CSS, JavaScript (Chart.js for visualizations).
- **Backend**: PHP (Native).
- **Database**: MySQL.
- **Security**: Prepared statements, HTML special characters, session management.

---

## Installation and Setup
1. Clone the repository:
   ```bash
   git clone https://github.com/your-repo/SCANBL-3.git
   ```
2. Import the database schema from the `db_scanbl3 (New).sql` file into your MySQL server.
3. Deploy the application on a web server (e.g., Apache, Nginx).
4. Access the application via your web browser.

---

## Contributing
We welcome contributions to improve SCANBL-3. Please follow these steps:
1. Fork the repository.
2. Create a new branch for your feature or bug fix.
3. Submit a pull request with a detailed description of your changes.

---

## License
This project is licensed under the **MIT License**. See the `LICENSE` file for details.

---

## Acknowledgments
- **PT. Meiji Pharmaceutical Indonesia** for providing the opportunity to develop this system.
- Our mentors and colleagues for their guidance and support during the internship.

---

For any inquiries or issues, please contact us at:

harisrifkyjuliantoro14@gmail.com

ariestianto456@gmail.com

razzanch@gmail.com

---

**SCANBL-3** – Streamlining Inventory Management with Precision and Efficiency.

---
