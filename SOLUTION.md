# SOLUTION.md

## Design Overview

This proof-of-concept is a lightweight, scalable document search system built for Company X’s internal research needs. It demonstrates efficient use of core PHP (8.3, no frameworks), MySQL, and Angular 16+, with a focus on RESTful design, performance, and modern UI/UX.

### Backend (PHP, MySQL)

- **RESTful API (Vanilla PHP):**
  - Implements endpoints for uploading, listing (with pagination), retrieving, and deleting documents, plus full-text search with highlighting and relevance scoring.
  - Follows clean separation: `/api/documents` for CRUD, `/api/search` for search.
- **Database Schema:**
  - MySQL tables for documents and a search index.
  - Full-text indexes on content fields for fast search and relevance ranking.
- **Performance & Caching:**
  - File-based caching for frequent searches.
  - Query optimization and proper indexing for scalable performance.
  - Large document uploads handled via streaming to avoid memory spikes.
- **Security & Robustness:**
  - Input validation, file type checks, and error handling throughout.
  - All configuration via environment variables

### Frontend (Angular 19)

- **Modern UI/UX:**
  - Responsive, clean interface using Angular Material.
  - Drag-and-drop document upload, paginated document list, and detail views.
  - Real-time search with debounced input, result highlighting, and sorting by relevance/date.
  - Performance metrics (search time, result count) and robust error/loading states.
- **API Integration:**
  - All API URLs are injected at runtime for flexible deployment.
  - Uses Angular services for clean separation of concerns.

### DevOps & Testing

- **Dockerized Workflow:**
  - Separate containers for backend, frontend, and database.
  - Dev and prod Compose files for local and production parity.
  - Persistent volumes for uploads, cache, and DB data.
- **Testing:**
  - PHPUnit for backend logic and services.
  - Jasmine/Karma for Angular components and services.

## Key Design Decisions

- **No Frameworks (PHP):**
  - Chose vanilla PHP for maximum transparency and control, as required.
- **Full-text Search:**
  - Leveraged MySQL’s native full-text indexing for speed and simplicity.
- **Caching:**
  - File-based cache is easy to implement and maintain for a POC; can be swapped for Redis/Memcached if scaling up.
- **Runtime Config:**
  - Frontend reads API URL from a JS file generated at container start, supporting flexible deployments.
- **Separation of Concerns:**
  - Clear split between backend (API, DB) and frontend (UI/UX), communicating via REST.

## Trade-offs

- **Multi-container vs. Monolith:**
  - Multi-container (frontend, backend, db) is more complex but enables scalability and easier debugging.
- **PHP Image Choice:**
  - Used Ubuntu + ondrej/php for extension flexibility, at the cost of longer build times.
- **Caching Simplicity:**
  - File-based cache is simple but not distributed; sufficient for POC, but would need to scale for production.
  - Indexing cache isn't implemented, but could be added for large datasets.
- **No .env in VCS:**
  - All secrets/config are passed via Compose for security and portability.

## Notable Implementations

- **Streaming Uploads:**
  - Handles large files efficiently, avoiding memory overload.
- **Search Highlighting:**
  - Returns search results with highlighted query terms for better UX.
- **Debounced Search:**
  - Angular search input is debounced for performance and reduced API load.
- **Healthchecks & Resilience:**
  - MySQL healthchecks and backend retry logic ensure robust startup in Docker.

---

## Summary

This architecture demonstrates efficient, scalable document search using modern PHP and Angular best practices. It balances developer experience, security, and performance, and is ready for further extension or production hardening.
