# Graph Report - /var/www/dbteknisi  (2026-07-16)

## Corpus Check
- Corpus is ~31,148 words - fits in a single context window. You may not need a graph.

## Summary
- 716 nodes · 971 edges · 144 communities (135 shown, 9 thin omitted)
- Extraction: 95% EXTRACTED · 5% INFERRED · 0% AMBIGUOUS · INFERRED: 48 edges (avg confidence: 0.81)
- Token cost: 0 input · 0 output

## Community Hubs (Navigation)
- Password Confirmation Auth
- Project Controller
- Customer Contact Controller
- Composer Dependencies
- Login Session Auth
- Document Category Controller
- Project Status Controller
- NPM Dependencies
- Customer Controller
- Composer Scripts & Config
- Work Type Controller
- Test Infrastructure
- User Controller
- Database Schema V1
- Account Manager Settings
- User Model & Auth Routes
- Profile Tests
- Middleware - Can Edit
- Middleware - Super Admin
- Livewire Status List
- User Factory
- Auth Tests - Login
- Auth Tests - Password Reset
- Project Status Enum
- User Role Enum
- Opencode Configuration
- Profile Views
- App Layout Views
- Graphify Plugin
- System Roles
- Laravel Framework

## God Nodes (most connected - your core abstractions)
1. `User` - 57 edges
2. `Project` - 45 edges
3. `Controller` - 34 edges
4. `Customer` - 31 edges
5. `TestCase` - 20 edges
6. `ProjectDocument` - 18 edges
7. `ProjectStatus` - 18 edges
8. `DocumentCategory` - 16 edges
9. `WorkType` - 16 edges
10. `AccountManager` - 14 edges

## Surprising Connections (you probably didn't know these)
- `Scope of Work: Survey, Demo, POC, Instalasi, Maintenance, Troubleshooting` --semantically_similar_to--> `Activity Types`  [INFERRED] [semantically similar]
  docs/system-design-v1.md → docs/database-v1.md
- `AccountManagerController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/AccountManagerController.php → app/Http/Controllers/Controller.php
- `AuthenticatedSessionController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Auth/AuthenticatedSessionController.php → app/Http/Controllers/Controller.php
- `CustomerContactController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/CustomerContactController.php → app/Http/Controllers/Controller.php
- `CustomerController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/CustomerController.php → app/Http/Controllers/Controller.php

## Import Cycles
- None detected.

## Communities (144 total, 9 thin omitted)

### Community 0 - "Password Confirmation Auth"
Cohesion: 0.06
Nodes (32): ConfirmablePasswordController, RedirectResponse, Request, View, EmailVerificationNotificationController, RedirectResponse, Request, EmailVerificationPromptController (+24 more)

### Community 1 - "Project Controller"
Cohesion: 0.06
Nodes (10): Request, ProjectController, Request, ProjectTaskController, TrashController, Project, ProjectActivity, ProjectObserver (+2 more)

### Community 2 - "Customer Contact Controller"
Cohesion: 0.08
Nodes (11): CustomerContactController, Request, Request, ProjectSupportController, CustomerContact, ProjectSupport, ProjectTask, AppServiceProvider (+3 more)

### Community 3 - "Composer Dependencies"
Cohesion: 0.05
Nodes (43): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+35 more)

### Community 4 - "Login Session Auth"
Cohesion: 0.09
Nodes (13): AuthenticatedSessionController, RedirectResponse, Request, View, RedirectResponse, Request, View, ProfileController (+5 more)

### Community 5 - "Document Category Controller"
Cohesion: 0.09
Nodes (7): DocumentCategoryController, Request, Request, ProjectDocumentController, DocumentCategory, ProjectDocument, DocumentCategorySeeder

### Community 6 - "Project Status Controller"
Cohesion: 0.10
Nodes (10): Request, ProjectStatusController, ProjectStatusList, ProjectStatus, AppLayout, View, GuestLayout, View (+2 more)

### Community 7 - "NPM Dependencies"
Cohesion: 0.07
Nodes (28): alpinejs, @anthropic-ai/claude-code, autoprefixer, concurrently, laravel-vite-plugin, dependencies, @anthropic-ai/claude-code, devDependencies (+20 more)

### Community 8 - "Customer Controller"
Cohesion: 0.12
Nodes (5): CustomerController, Request, Customer, CustomerObserver, CustomerPolicy

### Community 9 - "Composer Scripts & Config"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 10 - "Work Type Controller"
Cohesion: 0.13
Nodes (8): Request, WorkTypeController, WorkType, DatabaseSeeder, RoleAndPermissionSeeder, WorkTypeSeeder, DatabaseSeeder, Seeder

### Community 11 - "Test Infrastructure"
Cohesion: 0.13
Nodes (8): BaseTestCase, RefreshDatabase, EmailVerificationTest, PasswordUpdateTest, RegistrationTest, ExampleTest, TestCase, ExampleTest

### Community 12 - "User Controller"
Cohesion: 0.19
Nodes (4): Request, UserController, User, Authenticatable

### Community 13 - "Database Schema V1"
Cohesion: 0.14
Nodes (16): Account Managers, Activity Types, Companies (NTI, MGK, TPS, WANI), Customer Contacts Table, Customers Table, Document Categories, Engineers, Project Documents Table (+8 more)

### Community 14 - "Account Manager Settings"
Cohesion: 0.27
Nodes (3): AccountManagerController, Request, AccountManager

### Community 15 - "User Model & Auth Routes"
Cohesion: 0.22
Nodes (4): HasFactory, HasRoles, Notifiable, PasswordConfirmationTest

### Community 17 - "Middleware - Can Edit"
Cohesion: 0.53
Nodes (4): EnsureCanEdit, Closure, Request, Response

### Community 18 - "Middleware - Super Admin"
Cohesion: 0.53
Nodes (4): EnsureIsSuperAdmin, Closure, Request, Response

### Community 19 - "Livewire Status List"
Cohesion: 0.33
Nodes (5): cancelEdit, delete({{ $status->id }}), edit({{ $status->id }}), save, update

### Community 20 - "User Factory"
Cohesion: 0.47
Nodes (3): UserFactory, Factory, static

### Community 25 - "Opencode Configuration"
Cohesion: 0.50
Nodes (3): plugin, $schema, .opencode/plugins/graphify.js

### Community 26 - "Profile Views"
Cohesion: 0.50
Nodes (3): profile.partials.delete-user-form, profile.partials.update-password-form, profile.partials.update-profile-information-form

## Knowledge Gaps
- **86 isolated node(s):** `$schema`, `.opencode/plugins/graphify.js`, `$schema`, `name`, `type` (+81 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **9 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Controller` connect `Password Confirmation Auth` to `Project Controller`, `Customer Contact Controller`, `Login Session Auth`, `Document Category Controller`, `Project Status Controller`, `Customer Controller`, `Work Type Controller`, `User Controller`, `Account Manager Settings`?**
  _High betweenness centrality (0.087) - this node is a cross-community bridge._
- **Why does `User` connect `User Controller` to `Password Confirmation Auth`, `Project Controller`, `Customer Contact Controller`, `Customer Controller`, `Work Type Controller`, `Test Infrastructure`, `User Model & Auth Routes`, `Profile Tests`, `Auth Tests - Login`, `Auth Tests - Password Reset`?**
  _High betweenness centrality (0.064) - this node is a cross-community bridge._
- **Why does `Project` connect `Project Controller` to `Customer Contact Controller`, `User Controller`, `Document Category Controller`?**
  _High betweenness centrality (0.041) - this node is a cross-community bridge._
- **Are the 27 inferred relationships involving `User` (e.g. with `.store()` and `.index()`) actually correct?**
  _`User` has 27 INFERRED edges - model-reasoned connections that need verification._
- **Are the 4 inferred relationships involving `Project` (e.g. with `.index()` and `.index()`) actually correct?**
  _`Project` has 4 INFERRED edges - model-reasoned connections that need verification._
- **Are the 7 inferred relationships involving `Customer` (e.g. with `.index()` and `.create()`) actually correct?**
  _`Customer` has 7 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `.opencode/plugins/graphify.js`, `$schema` to the rest of the system?**
  _86 weakly-connected nodes found - possible documentation gaps or missing edges._