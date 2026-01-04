# Health Club Management ERP (CodeIgniter 3)

A web-based health club management ERP **admin system** built with CodeIgniter 3.

This project focuses on the **administrative and back-office side** of gym and fitness center operations,
including member management, payments, attendance, and staff administration.

---

## 🏋️ What is this project?

This application provides the core functionality needed to operate a health club or gym efficiently.
It was built using **CodeIgniter 3** and focuses on simplicity, performance, and practical real-world usage.

By using this project, you can build your own **gym or fitness center management system** based on a proven ERP-style structure.

---

## ✨ Features

- Member registration and management
- Membership plans and payment tracking
- Attendance management
- Trainer / staff management
- Basic sales and billing records
- Admin dashboard
- Role-based access control
- Clean and extendable CodeIgniter 3 structure

---

## 🗄 Database Setup (Required)

This project **requires a database schema** before it can run.

### 1️⃣ Get the schema file

Download `schema.sql` from the following repository:

👉 https://github.com/humake-dev/humake-dev

### 2️⃣ Create database and import schema

```bash
mysql -u root -p
CREATE DATABASE healthclub;
USE healthclub;
SOURCE schema.sql;
```

## 🚀 Getting Started

```bash
git clone https://github.com/humake-dev/admin.git
cd admin/public
php -S localhost:20020
then visit

http://localhost:20020


> 로컬 환경에서 바로 실행해볼 수 있습니다.

---

## 🛠 Tech Stack

- PHP
- CodeIgniter 3
- MySQL / MariaDB
- Bootstrap (UI)
- jQuery


## 📌 Server Requirements

PHP version 7 or newer is recommended.
> PHP 7 이상 환경을 권장합니다.


## ✍️ Author

[Jedaeroweb](https://www.jedaeroweb.co.kr)
> 작성자 = 제대로웹