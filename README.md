## Tech Stack & Architecture

### Backend Framework
- **Laravel 8.x** – Core PHP framework (project structure and configuration defined in `composer.json`).
- **PHP Compatibility** – Supports PHP `^7.3 | ^8.0`.

### Frontend Build & Tooling
- **Laravel Mix** – Asset bundling and build pipeline.
- **Webpack** – JavaScript and CSS compilation.
- **PostCSS** – CSS processing.
- **Axios, Lodash** – Frontend utilities and HTTP requests.

### File Management & Rich Text
- **CKFinder** (`ckfinder/ckfinder-laravel-package`) – File management and rich content integration.
- Configuration managed via `ckfinder.php`.

### Messaging & External Platform Integration
- **LINE Bot SDK** (`linecorp/line-bot-sdk`) – LINE bot and webhook integration.
- **Google API Client** (`google/apiclient`) – Integration with Google services such as Drive and YouTube.

### Data Import / Export
- **Maatwebsite Excel** (`maatwebsite/excel`) – Excel import/export functionality.
- Import logic implemented in `ProductsImport.php`.

### Data Tables & UI
- **Yajra DataTables** – Server-side DataTables processing.
- **DataTables (Frontend)** – Tabular data display and interaction.

### Data Modeling & Structure
- **Eloquent ORM** – Model-based database interaction.
- Multiple domain models (e.g., `Product`, `ProductSku`, `ProductCategory`) to support products, categories, tags, images, and orders.
- **kalnoy/nestedset** – Hierarchical / tree-structured data management (categories, taxonomies).

### Background Jobs & Asynchronous Processing
- **Laravel Jobs & Queue** – Background task processing.
- Used for email dispatching and deferred business logic.

### Email System
- **Laravel Mail** – Built-in mail system for notifications and transactional emails.

### HTTP & External APIs
- **Guzzle** – Backend HTTP client for third-party API communication.

### Middleware & Utilities
- **fideloper/proxy** – Proxy and load balancer support.
- **fruitcake/laravel-cors** – CORS handling.

### Testing & Development Tools
- **PHPUnit** – Unit and integration testing.
- **Mockery** – Mocking framework.
- **facade/ignition**, **nunomaduro/collision** – Debugging and error handling.

### Code Structure & Conventions
- **MVC Architecture**
- **PSR-4 Autoloading**
- **Laravel routing, middleware, and service provider conventions**
