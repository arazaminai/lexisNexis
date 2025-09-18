# SOLUTION.md

## Design Overview

This project is a full-stack document management system with a PHP 8.3 backend (REST API) and an Angular frontend. It uses MySQL for storage and supports Docker-based development and production deployments.

### Key Design Choices

- **Separation of Concerns:**  
  The backend (PHP) and frontend (Angular) are developed and deployed as separate services, communicating via REST API.
- **Dockerized Workflow:**  
  Both dev and prod environments are fully containerized for consistency and easy onboarding.
- **Environment Management:**  
  All sensitive and environment-specific configuration is managed via Docker Compose environment variables, not checked-in `.env` files.
- **Stateless Containers:**  
  Persistent data (uploads, cache, MySQL data) is stored in Docker volumes or bind mounts.
- **Testing:**  
  PHPUnit for backend, Angular/Karma for frontend, both runnable inside containers.

### Trade-offs

- **Single vs. Multi-container:**  
  Chose multi-container (frontend, backend, db) for clarity, scalability, and easier debugging, at the cost of slightly more complex orchestration.
- **PHP 8.3 via Ubuntu vs. Official PHP Image:**  
  Used Ubuntu + ondrej/php for maximum extension flexibility, but this increases build time compared to the official PHP image.
- **Frontend Dev Server in Dev Only:**  
  In dev, Angular runs with hot reload on its own port; in prod, it is served as static files via NGINX for performance.
- **No .env in VCS:**  
  All secrets/config are passed via Compose, not committed, for security and portability.

### Notable Implementation Details

- **Backend:**  
  - Uses PDO for MySQL, with connection retries for Docker startup race conditions.
  - All environment variables are read via `getenv()` for Docker compatibility.
  - Document and search services are modular and unit-tested.
- **Frontend:**  
  - Angular CLI for dev, NGINX for prod.
  - API URL is configurable via environment.
- **Database:**  
  - MySQL 8.3, initialized via Compose.
  - Data persisted in Docker volumes.

### How to Extend

- Add new API endpoints in backend `/src/controllers/` and `/src/services/`.
- Add new Angular components in `/frontend/src/app/components/`.
- Add new environment variables in Compose and reference via `getenv()` or Angular environment files.

---

## Summary

This architecture balances developer experience, security, and production-readiness.  
Docker Compose enables easy local development and deployment, while clear separation of services and configuration makes the system robust and maintainable.
