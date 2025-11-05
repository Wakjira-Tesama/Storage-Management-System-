# 🏪 StoreSynk - Multi-Store E-commerce Management System

**StoreSynk** is a role-based web application designed to streamline store and inventory operations across multiple branches.  
It offers a centralized platform where **Admins**, **Store Managers**, **Branches**, and **Buyers** can collaborate efficiently — each having a dedicated dashboard and permissions.

---

## 🌟 Project Overview

The goal of **StoreSynk** is to simplify the management of items, requests, and sales across multiple store locations.  
It enables real-time updates, role-based workflows, and performance tracking through an integrated dashboard system.

### 🔧 Built With
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
- **Backend:** PHP (Core)
- **Database:** MySQL
- **Other Tools:** XAMPP/WAMP, phpMyAdmin

---

## 🧩 Core Features

| Role | Key Features |
|------|---------------|
| 👨‍💼 **Admin** | Full system control, user management, analytics, and activity logs |
| 🏢 **Store Manager** | Manage inventories, track stock, approve requests, and generate reports |
| 🌿 **Branch** | Manage branch-level store items, create and track requests |
| 🛒 **Buyer** | Purchase items, view bills, and check approved or pending requests |

---
[Admin Panel]
↓
[Store Manager]
↓
[Branch Interface]
↓
[Buyer Portal]

Each level of the hierarchy interacts through secure authentication and MySQL-based communication.  
All activities are logged in the **activity_log** table for transparency and auditing.

---

## 🗂️ Database Design Summary

- **users** – Stores login details, roles, and contact info  
- **inventory** – Global store stock and item details  
- **branch_inventory** – Stock data for individual branches  
- **branch_request** – Requests made from branches to main store  
- **item_requests** – Requests from buyers to branches  
- **item_suggestions** – Viewer suggestions for new items  
- **activity_log** – Tracks all user activities for accountability  

---

## 💡 User Roles and Interfaces

Below are the main dashboards for each user type with their key functionalities.

---

### 🛒 Buyer Dashboard

**Functionalities:**
- View and buy available items from assigned branch
- Track **Pending Requests**
- View **Approved Items** and print bills
- Manage personal request history

**Dashboard Example:**

![Buyer Dashboard](https://github.com/Wakjira-Tesama/StoreSynk/assets/buyer_dashboard.png)

The Buyer interface is simple and focused on purchasing and tracking request status.  
It displays options such as:
- **Buy Item**
- **Pending Requests**
- **Approved Items - Bill**

---

### 🏢 Store Manager Dashboard

**Functionalities:**
- Add new inventory items and update existing ones
- Monitor current inventory levels
- Approve or reject item requests from branches
- Generate and view **Usage Reports**

**Dashboard Example:**

![Store Manager Dashboard](https://github.com/Wakjira-Tesama/StoreSynk/assets/store_manager_dashboard.png)

The Store Manager panel centralizes stock and request management, allowing easy tracking of items and automatic quantity updates after approvals.

---

### 🌿 Branch Dashboard

**Functionalities:**
- Access branch-level store (view-only)
- Request items from the main store
- Manage and monitor **Pending Buyer Requests**
- Track personal requests in **My Item Requests**

**Dashboard Example:**

![Branch Dashboard](https://github.com/Wakjira-Tesama/StoreSynk/assets/branch_dashboard.png)

Branches act as intermediaries between buyers and the store manager.  
They can submit requests and monitor their fulfillment status.

---

### 👨‍💼 Admin Dashboard

**Functionalities:**
- Manage user accounts and assign roles
- Access complete system analytics
- Monitor logs and detect suspicious activities
- Generate usage and activity reports

**Dashboard Example:**

![Admin Dashboard](https://github.com/Wakjira-Tesama/StoreSynk/assets/admin_dashboard.png)

The Admin interface provides total control over system operations, including user privileges, performance charts, and audit trails.

---

## 📊 System Efficiency

| Improvement | Result |
|--------------|---------|
| **Optimized SQL Queries** | Reduced API response time by **25%** |
| **Role-Based Access Control** | Enhanced data security and workflow clarity |
| **Responsive Design** | Smooth experience on both desktop and mobile |
| **Automated Activity Logs** | Improved transparency and accountability |

---

## 🏗️ Project Folder Structure


---

## ⚙️ Installation & Setup

1. **Clone the Repository**
   ```bash
   git clone https://github.com/Wakjira-Tesama/StoreSynk.git
   cd StoreSynk
Database Setup

Open phpMyAdmin

Create a database named store_management

Import the SQL file store_management.sql from the project folder

Configure Database Connection
Run the App

Start XAMPP or WAMP

Open browser and visit: http://localhost/StoreSynk

🔒 Security Highlights

Role-based authentication and session management

Password hashing for secure storage

Input validation and prepared statements to prevent SQL injection

Access logging for every activity

📈 Future Improvements

Integration with RESTful APIs for external inventory sync

Notification system for pending approvals

Exportable reports (PDF, Excel)

Cloud deployment using AWS or Vercel

👨‍💻 Author



**Wakjira Tesama**  
Full-Stack Web Developer | Software Engineer  

📧 [wakjiratesama@gmail.com](mailto:wakjiratesama@gmail.com)  
🌐 [GitHub Profile](https://github.com/Wakjira-Tesama)  
💼 [LinkedIn Profile](https://www.linkedin.com/in/wakjira-tesama/)





## 🧭 System Architecture

