# LexisNexis Document Management System

## 📌 Overview
A full-stack document management system with the following stack:

- **Backend:** PHP 8.3 (Apache), MySQL 8.3  
- **Frontend:** Angular (served by NGINX in production, Angular CLI in development)  
- **Deployment:** Fully Dockerized with separate development and production `docker-compose` files  

---

## ✅ Prerequisites
- [Docker Desktop](https://www.docker.com/products/docker-desktop)  
- [Docker Compose](https://docs.docker.com/compose/)  

---

## 🚀 Quick Start

### Development
1. **Build and start all services**  
   ```sh
   docker-compose -f docker-compose.dev.yml up --build
   ```

   - Frontend (Angular CLI): [http://localhost:4200](http://localhost:4200)  
   - Backend (PHP/Apache): [http://localhost:8080](http://localhost:8080)  
   - MySQL: `localhost:3306` (service name: `db`)  

2. **Run backend tests**  
   ```sh
   ./vendor/bin/phpunit tests/
   ```

3. **Run frontend tests**  
   ```sh
   ng test
   ```

---

### Production
1. **Build and start all services**  
   ```sh
   docker-compose -f docker-compose.prod.yml up --build -d
   ```

   - Frontend (NGINX): [http://localhost](http://localhost)  
   - Backend (PHP/Apache): [http://localhost:8080](http://localhost:8080)  

---

## 🖥️ Running Locally Without Docker (Advanced)

### Backend
1. Install PHP 8.3 and required extensions.  
2. Copy environment variables:  
   ```sh
   cp backend/.env.example backend/.env
   ```
   Adjust as needed.  
3. Start Apache or PHP’s built-in server.  

### Frontend
1. Install dependencies:  
   ```sh
   cd frontend && npm install
   ```
2. Run dev server:  
   ```sh
   ng serve
   ```

### MySQL
1. Install and run MySQL 8.3 locally.  
2. Configure connection in `backend/.env`.  

---

## ⚙️ Environment Variables
- In Docker: defined in `docker-compose` files.  
- Running locally: copy `backend/.env.example` → `backend/.env` and update as needed.  

---

## 🔧 Common Tasks
- **Run backend tests:**  
  ```sh
  ./vendor/bin/phpunit tests/
  ```
- **Run frontend tests:**  
  ```sh
  ng test
  ```
- **Change API URL:**  
  - Update `frontend/src/environments/environment.ts`  
  - Update backend `.env` or `docker-compose` file  

---

## 🐛 Troubleshooting
- **Database connection errors** → Use `MYSQL_HOST=db` in Docker, or `localhost` when running locally.  
- **PHP extension errors** → Ensure required extensions are installed (see `backend/Dockerfile`).  
- **Angular build errors** → Run `npm install` inside the `frontend` directory.  

---
