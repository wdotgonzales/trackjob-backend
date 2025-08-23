# TrackJob REST API
A RESTful API backend service for the [TrackJob Mobile Application](https://github.com/wdotgonzales/trackjob-frontend) that helps users monitor and manage their job applications effectively.

## Overview
TrackJob Backend provides a comprehensive API for tracking job applications, managing application statuses, and organizing job search activities. The service enables users to maintain detailed records of their job applications with real-time status updates and filtering capabilities.

## Why I made this app?

### 🎯 Problem Solved
Job seekers struggle to track multiple applications across different companies, 
leading to missed follow-ups and poor application management.

### 💡 Solution
TrackJob provides a centralized system to track job applications with 
automated reminders and intelligent filtering.

## Technical Stack
- **Framework**: Django / Django REST Framework
- **Database**: MySQL
- **Authentication**: JWT (JSON Web Token), OAuth 2.0 (Google)
- **Email Service**: Google (Gmail) SMTP server
- **Caching**: Redis
- **Containerization**: Docker
- **Reverse Proxy**: NGINX

## Features

### Job Application Management
- **Create, Read, Update, Delete (CRUD)** operations for job applications
- **Company Information Tracking** - Store and manage company details
- **Position Details** - Track job titles, descriptions, and requirements
- **Application Timeline** - Monitor application submission dates and updates

### Status Tracking
- **Application Status Monitoring** with multiple states:
  - Default (newly added)
  - Applied (application submitted)
  - Rejected (application declined)
  - Ghosted (no response received)
  - Under Process (U.P.)
  - Offer Received (O.R.)
  - Interview Scheduled (I.S.)
  - Withdrawn/Cancelled (W/C)
  - Accepted Offer (A.O.)

### Employment Type Classification
- **Full Time** positions
- **Part Time** positions  
- **Contract** roles

### Work Arrangement Options
- **On-site** positions
- **Remote** work opportunities
- **Hybrid** arrangements

### Advanced Filtering & Search
- **Date Range Filtering** - Filter applications by submission date
- **Status-based Filtering** - View applications by current status
- **Employment Type Filtering** - Filter by job type preferences
- **Work Arrangement Filtering** - Filter by work location preferences
- **Company Search** - Search applications by company name
- **Application History** - Track changes and updates over time

## Database Structure
The TrackJob backend uses Django ORM with a MySQL database to manage job application tracking. The database is designed with normalized tables to handle user management, job applications, and related metadata efficiently.

### Database Schema

#### 1. User Management

**tbl_user**
```python
class User(AbstractBaseUser, PermissionsMixin):
    id = models.AutoField(primary_key=True)
    email = models.EmailField(unique=True)
    full_name = models.CharField(max_length=255, null=True, blank=True)
    password = models.CharField(max_length=128)
    profile_url = models.URLField(null=True, blank=True)
    is_active = models.BooleanField(default=True)
    is_staff = models.BooleanField(default=False)
    created_at = models.DateTimeField(default=timezone.now)
    updated_at = models.DateTimeField(auto_now=True)
```

**tbl_verification_code**
```python
class VerificationCode(models.Model):
    id = models.AutoField(primary_key=True)
    email = models.EmailField(max_length=255)
    code = models.CharField(max_length=6)
    created_at = models.DateTimeField()
    expires_at = models.DateTimeField()
```

#### 2. Job Application System

**tbl_employment_type** (Lookup Table)
```python
class EmploymentType(models.Model):
    id = models.AutoField(primary_key=True)
    label = models.CharField(max_length=128)
    description = models.CharField(max_length=128)
```
*Examples: Full-time, Part-time, Contract, Internship*

**tbl_work_arrangement** (Lookup Table)
```python
class WorkArrangement(models.Model):
    id = models.AutoField(primary_key=True)
    label = models.CharField(max_length=128)
    description = models.CharField(max_length=128)
```
*Examples: Remote, On-site, Hybrid*

**tbl_job_application_status** (Lookup Table)
```python
class JobApplicationStatus(models.Model):
    id = models.AutoField(primary_key=True)
    label = models.CharField(max_length=128)
    description = models.CharField(max_length=128)
```
*Examples: Applied, Interview Scheduled, Rejected, Offered*

**tbl_job_application** (Main Entity)
```python
class JobApplication(models.Model):
    id = models.AutoField(primary_key=True)
    user = models.ForeignKey(User, on_delete=models.CASCADE)
    position_title = models.CharField(max_length=128)
    company_name = models.CharField(max_length=128)
    employment_type = models.ForeignKey(EmploymentType, on_delete=models.SET_NULL, null=True)
    work_arrangement = models.ForeignKey(WorkArrangement, on_delete=models.SET_NULL, null=True)
    job_application_status = models.ForeignKey(JobApplicationStatus, on_delete=models.SET_NULL, null=True)
    job_posting_link = models.CharField(max_length=255)
    date_applied = models.DateField()
    job_location = models.CharField(max_length=255)
    job_description = models.TextField(null=True, blank=True)
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)
```

**tbl_reminder**
```python
class Reminder(models.Model):
    id = models.AutoField(primary_key=True)
    job_application = models.ForeignKey(JobApplication, on_delete=models.CASCADE)
    title = models.CharField(max_length=255)
    description = models.TextField(blank=True, null=True)
    is_enabled = models.BooleanField(default=True)
    reminder_datetime = models.DateTimeField(default=timezone.now)
    created_at = models.DateTimeField(auto_now_add=True)
    modified_at = models.DateTimeField(auto_now=True)
```

### Entity Relationships

```
User (1) ──────── (N) JobApplication
                       │
                       ├── (N) Reminder
                       │
                       ├── (N) EmploymentType (Lookup)
                       │
                       ├── (N) WorkArrangement (Lookup)
                       │
                       └── (N) JobApplicationStatus (Lookup)

User (1) ──────── (N) VerificationCode
```

### Relationship Details

1. **User → JobApplication**: One-to-Many
   - One user can have multiple job applications
   - When user is deleted, all related job applications are deleted (`CASCADE`)

2. **JobApplication → Reminder**: One-to-Many
   - Each job application can have multiple reminders
   - When job application is deleted, all related reminders are deleted (`CASCADE`)

3. **JobApplication → Lookup Tables**: Many-to-One
   - Multiple job applications can reference the same employment type, work arrangement, or status
   - When lookup record is deleted, job application reference is set to NULL (`SET_NULL`)

4. **User → VerificationCode**: One-to-Many
   - Users can have multiple verification codes (for different verification processes)

## Prerequisites for Intallation
- **Git** (with Git Bash recommended for Windows)
- **Docker Desktop**
- **Python 3.12+** (if running locally without Docker)

## Installation

### 1. Clone the repository
```bash
git clone https://github.com/wdotgonzales/trackjob-backend.git
```

### 2. Go to project directory
```bash
cd trackjob-backend/
```

### 3. **Windows Only: Fix Line Endings for Docker**
> **Skip this step if you're on Mac or Linux**

To prevent Docker entrypoint issues on Windows, run this command:
```bash
git config core.autocrlf false
git config core.eol lf
git checkout -- app/entrypoint.sh
```

Alternatively, if you have `dos2unix` installed:
```bash
dos2unix app/entrypoint.sh
```

### 4. Create and activate a virtual environment inside the app/ directory
```bash
cd app/
python -m venv venv
```

#### Activate virtual environment:
**For Windows (Git Bash) - Recommended:**
```bash
source venv/Scripts/activate
```

**For Windows (Command Prompt):**
```cmd
venv\Scripts\activate.bat
```

**For Windows (PowerShell):**
```powershell
venv\Scripts\Activate.ps1
```

**For Mac/Linux:**
```bash
source venv/bin/activate
```

> **Note:** If you encounter execution policy issues on Windows PowerShell, run:
> ```powershell
> Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
> ```

### 5. Go back to root directory
```bash
cd ..
```

### 6. Copy .env.dev from the example
**For Windows (Git Bash) or Mac/Linux:**
```bash
cp .env.dev.example .env.dev
```

**For Windows (Command Prompt/PowerShell):**
```cmd
copy .env.dev.example .env.dev
```

### 7. Setup .env.dev credentials
Edit the `.env.dev` file with your credentials:
```bash
# Django settings
DEBUG=1
SECRET_KEY=your-secret-key-here
DJANGO_ALLOWED_HOSTS=localhost 127.0.0.1 [::1]

# Database settings
SQL_ENGINE=django.db.backends.mysql
SQL_DATABASE=trackjob_db
SQL_USER=trackjob_user
SQL_PASSWORD=your_secure_password
SQL_HOST=db
SQL_PORT=3306
DATABASE=mysql

# Email SMTP Configurations
EMAIL_BACKEND=django.core.mail.backends.smtp.EmailBackend
EMAIL_HOST=smtp.gmail.com
EMAIL_PORT=587
EMAIL_USE_TLS=True
EMAIL_HOST_USER=your_account@gmail.com
EMAIL_HOST_PASSWORD=your_app_password_here
DEFAULT_FROM_EMAIL=your_account@gmail.com
```

### 8. Copy docker-compose.yml from the example
**For Windows (Git Bash) or Mac/Linux:**
```bash
cp docker-compose.example.yml docker-compose.yml
```

**For Windows (Command Prompt/PowerShell):**
```cmd
copy docker-compose.example.yml docker-compose.yml
```

### 9. Setup docker-compose.yml credentials
Edit the `docker-compose.yml` file with your database credentials (must match .env.dev):
```yaml
services:
  web:
    build: ./app
    command: gunicorn trackjob.wsgi:application --bind 0.0.0.0:8000
    volumes:
      - static_volume:/home/app/web/staticfiles
    expose:
      - "8000"
    env_file:
      - ./.env.dev
    depends_on:
      - db

  db:
    image: mysql:8.0 
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql/
    environment:
      - MYSQL_DATABASE=your_database_name
      - MYSQL_USER=your_mysql_user
      - MYSQL_PASSWORD=your_mysql_password
      - MYSQL_ROOT_PASSWORD=your_root_password

  redis:
    image: redis:7-alpine
    restart: always
    ports:
      - "6379:6379"

  nginx:
    build: ./nginx
    volumes:
      - static_volume:/home/app/web/staticfiles
    ports:
      - 1337:80
    depends_on:
      - web

volumes:
  mysql_data:
  static_volume:
```

### 10. Build and run the containers
```bash
docker-compose -f docker-compose.yml up -d --build
```

### 11. Access the application
Load data for **tbl_employment_type**, **tbl_work_arrangement** and **tbl_job_application_status** 
```
docker-compose -f docker-compose.yml exec web python3 manage.py loaddata job_applications/fixtures/employment_type_fixture.json job_applications/fixtures/job_application_status_fixture.json job_applications/fixtures/work_arrangement_fixture.json
```

### 12. Access the application
Open your browser and go to:
```
http://localhost:1337/
```

## Windows-Specific Troubleshooting

### Issue: Docker entrypoint permission denied or command not found
**Solution:** This is caused by Windows line endings. Run:
```bash
git checkout -- app/entrypoint.sh
dos2unix app/entrypoint.sh
```
Or manually fix with:
```bash
sed -i 's/\r$//' app/entrypoint.sh
```

### Issue: Virtual environment activation not working
**Solution:** Use the appropriate activation command for your terminal:
- **Git Bash:** `source venv/Scripts/activate`
- **CMD:** `venv\Scripts\activate.bat`
- **PowerShell:** `venv\Scripts\Activate.ps1`

### Issue: Docker Desktop not starting
**Solution:** 
1. Ensure Hyper-V is enabled in Windows Features
2. Enable WSL 2 integration in Docker Desktop settings
3. Restart Docker Desktop

## Alternative: Using WSL 2 (Recommended for Better Performance)
For the best Docker experience on Windows, consider using WSL 2:

1. Install WSL 2 and a Linux distribution (Ubuntu recommended)
2. Clone and run the project inside WSL 2
3. Enable WSL 2 integration in Docker Desktop
4. Follow the standard Linux installation steps

This approach eliminates most Windows-specific issues and provides better performance.

## API Documentation

### Base URL
```
Development: http://localhost:1337/api/v1/
Production: https://your-domain.com/api/v1/
```

### User Registration & Authentication
- `GET /users/` - Get all users
- `GET /users/<id>/` - Get user by ID
- `POST /register/` - Register a new user
- `POST /login/` - User login (email + password)
- `POST /login-email-only/` - User login (email only)
- `POST /decode-token/` - Decode and validate JWT token

### Email Verification
- `POST /send-verification-code/` - Send verification code (checks if email exists)
- `POST /send-verification-code-no-check/` - Send verification code (no email existence check)
- `POST /verify-code/` - Verify email verification code

### Password Management
- `POST /reset-password/` - Reset user password

### Profile Management
- `POST /change-profile-url/` - Update user profile URL
- `POST /update-profile/` - Update user profile (full_name, profile_url)
- `POST /check-email-existence/` - Check if email is available

### Job Applications
- `GET /job_application/` - List user's job applications (with filtering & pagination)
- `POST /job_application/` - Create a new job application
- `GET /job_application/<id>/` - Get specific job application by ID
- `PUT /job_application/<id>/` - Update all fields of job application
- `PATCH /job_application/<id>/` - Partial update of job application
- `DELETE /job_application/<id>/` - Delete specific job application

### Bulk Operations
- `DELETE /job_application/delete-all/` - Delete all job applications for current user

### Job Application Filtering (Query Parameters)
The list endpoint supports the following query parameters:
- `employment_type` - Filter by employment type
- `job_application_status` - Filter by application status
- `work_arrangement` - Filter by work arrangement
- `company_name` - Filter by company name (partial match)
- `position_title` - Filter by position title (partial match)
- `date_from` - Filter applications from this date
- `date_to` - Filter applications up to this date
- `date_exact` - Filter applications for exact date
- **Example**: `GET job_applications/?page=1&employment_type=Full-time&job_application_status=Interview&work_arrangement=Remote&company_name=Tech&position_title=Engineer&date_from=2025-01-01&date_to=2025-03-31`

### Reminders (Nested under Job Applications)
- `GET /job_application/<job_app_id>/reminder/` - List all reminders for job application
- `POST /job_application/<job_app_id>/reminder/` - Create a new reminder
- `GET /job_application/<job_app_id>/reminder/<id>/` - Get specific reminder by ID
- `PUT /job_application/<job_app_id>/reminder/<id>/` - Update all fields of reminder
- `PATCH /job_application/<job_app_id>/reminder/<id>/` - Partial update of reminder
- `DELETE /job_application/<job_app_id>/reminder/<id>/` - Delete specific reminder

### Bulk Operations
- `POST /job_application/<job_app_id>/reminder/bulk-create/` - Create multiple reminders at once

### Response Format

All endpoints return responses in the following format:

```json
{
    "message": "Response message",
    "data": { ... },
    "status_code": 200
}
```

## Sample API Example/Usage
### POST /register/ -> Register a new user
```bash
curl -X POST http://localhost:1337/api/v1/register/ \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "securepassword", 
    "repeat_password": "securepassword",
    "full_name": "Full Name",
    "profile_uri": "https://example.com/profile.png"
  }'
```

**Sample Response:**
```json
{
    "data": {
        "id": 1,
        "email": "user@example.com",
        "full_name": "Full Name",
        "profile_url": "https://example.com/profile.png"
    },
    "message": "User registered successfully",
    "status_code": 201
}
```

### POST /login/ -> Login 
```bash
curl -X POST http://localhost:1337/api/v1/login/ \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "securepassword"
  }'

```

**Sample Response:**
```json
{
    "data": {
        "refresh": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ0b2tlbl90eXBlIjoicmVmcmVzaCIsImV4cCI6MTc1NjUyMzIyNywiaWF0IjoxNzU1OTE4NDI3LCJqdGkiOiJmNzg2M2NlYTAzZDI0M2YwOTYyYTlmZDA4MDAxZDUzZiIsInVzZXJfaWQiOiIyIiwiZW1haWwiOiJ1c2VyQGV4YW1wbGUuY29tIn0.9sGOuZ4ubOkiVljlpgX72-kDvWPRCElgxe_nBIGfRp0",
        "access": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ0b2tlbl90eXBlIjoiYWNjZXNzIiwiZXhwIjoxNzU2MDA0ODI3LCJpYXQiOjE3NTU5MTg0MjcsImp0aSI6Ijk5ODhlYzdhYzA2ODRhYzNiN2U1ZmU4ZDBmMDgwNTJkIiwidXNlcl9pZCI6IjIiLCJlbWFpbCI6InVzZXJAZXhhbXBsZS5jb20ifQ.k1oq_SpCsmvId0TzrspaccaOSmjHF_wmyb0ng6ZYiuI"
    },
    "message": "Login successful",
    "status_code": 200
}
```

### POST /decode-token/ -> Decode Token
```bash
curl -X POST http://localhost:1337/api/v1/decode-token/ \
  -H "Content-Type: application/json" \
  -d '{
    "token": "<access-token>"
  }'
```

**Sample Response:**
```json
{
    "data": {
        "token_type": "access",
        "exp": 1756004827,
        "iat": 1755918427,
        "jti": "9988ec7ac0684ac3b7e5fe8d0f08052d",
        "user_id": "2",
        "email": "user@example.com"
    },
    "message": "Token decoded successfully",
    "status_code": 200
}
```

### POST /send-verification-code/ -> Send verification code to email
```json
curl -X POST http://localhost:1337/api/v1/send-verification-code/   
  -H "Content-Type: application/json"   
  -d '{ 
        "email": "randomemail@gmail.com" 
      }'
```

**Sample Response:**
```bash
{
    "data": {
        "email": "randomemail@gmail.com"
    },
    "message": "Verification code sent to email successfully.",
    "status_code": 200
}
```

### POST /verify-code/ -> Verify code authenticity
```bash
curl -X POST http://localhost:1337/api/v1/verify-code/   
  -H "Content-Type: application/json"   
  -d '{ 
        "email": "randomemail@gmail.com", 
        "code": "123456" 
      }'
```

**Sample Response:**
```bash
{
    "data": {
        "email": "randomemail@gmail.com"        
    },
    "message":"Verification code is valid.",
    "status_code": 200
}
```

### POST /reset-password/ -> Reset Password
```bash
curl -X POST http://localhost:1337/api/v1/reset-password/   
-H "Content-Type: application/json"   
-d '{ 
      "email": "trackjob72101@gmail.com",  
      "new_password":"helloworld", 
      "confirm_password": "helloworld" 
    }'
```


**Sample Response:**
```json
{
    "data": {},
    "message": "Password has been reset successfully.",
    "status_code": 200
}
```


### POST /change-profile-url/ -> Change Profile URL
```json
curl -X POST http://localhost:1337/api/v1/change-profile-url/ \
  -H "Content-Type: application/json" \
  -H "Authorization: YOUR_ACCESS_TOKEN" \
  -d '{
    "profile_url": "YOUR_PROFILE_URL"
  }'

```

**Sample Response:**
```json
{
    "data": {
        "profile_url": "https://i.ibb.co/MxhFT7Xj/image.png"
    },
    "message": "Profile URL updated successfully.",
    "status_code": 200
}
```


### Authentication
All job application and reminder endpoints require JWT authentication. Include the access token in the Authorization header:
```bash
-H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```

### 📋 Job Application APIs

### 1. Create Job Application
**POST** `/job_application/`

```bash
curl -X POST http://localhost:1337/api/v1/job_application/ \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -d '{
    "position_title": "Senior Software Engineer",
    "company_name": "TechCorp Inc.",
    "employment_type": 1,
    "work_arrangement": 2,
    "job_application_status": 1,
    "job_posting_link": "https://techcorp.com/careers/senior-engineer",
    "date_applied": "2025-08-23",
    "job_location": "San Francisco, CA",
    "job_description": "We are looking for a senior software engineer to join our team..."
  }'
```

**Sample Response:**
```json
{
    "data": {
        "id": 15,
        "position_title": "Senior Software Engineer",
        "company_name": "TechCorp Inc.",
        "employment_type": {
            "id": 1,
            "label": "Full-time"
        },
        "work_arrangement": {
            "id": 2,
            "label": "Remote"
        },
        "job_application_status": {
            "id": 1,
            "label": "Applied"
        },
        "job_posting_link": "https://techcorp.com/careers/senior-engineer",
        "date_applied": "2025-08-23",
        "job_location": "San Francisco, CA",
        "job_description": "We are looking for a senior software engineer to join our team...",
        "created_at": "2025-08-23T10:30:00Z",
        "updated_at": "2025-08-23T10:30:00Z"
    },
    "message": "Job Application created successfully",
    "status_code": 201
}
```

### 2. List Job Applications (with Filtering & Pagination)
**GET** `/job_application/`

```bash
# Basic list (all applications)
curl -X GET http://localhost:1337/api/v1/job_application/ \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"

# With pagination
curl -X GET "http://localhost:1337/api/v1/job_application/?page=1" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"

# With filters
curl -X GET "http://localhost:1337/api/v1/job_application/?employment_type=Full-time&job_application_status=Applied&work_arrangement=Remote&company_name=Tech&position_title=Engineer&date_from=2025-01-01&date_to=2025-12-31" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```

**Sample Response:**
```json
{
    "data": {
        "results": [
            {
                "id": 15,
                "position_title": "Senior Software Engineer",
                "company_name": "TechCorp Inc.",
                "employment_type": {
                    "id": 1,
                    "label": "Full-time"
                },
                "work_arrangement": {
                    "id": 2,
                    "label": "Remote"
                },
                "job_application_status": {
                    "id": 1,
                    "label": "Applied"
                },
                "job_posting_link": "https://techcorp.com/careers/senior-engineer",
                "date_applied": "2025-08-23",
                "job_location": "San Francisco, CA",
                "job_description": "We are looking for a senior software engineer...",
                "created_at": "2025-08-23T10:30:00Z",
                "updated_at": "2025-08-23T10:30:00Z"
            }
        ],
        "current_page": 1,
        "total_pages": 3,
        "count": 25,
        "filters_applied": {
            "employment_type": "Full-time",
            "job_application_status": "Applied",
            "work_arrangement": "Remote",
            "company_name": "Tech",
            "position_title": "Engineer"
        }
    },
    "message": "Paginated job applications",
    "status_code": 200
}
```

### 3. Get Single Job Application
**GET** `/job_application/{id}/`

```bash
curl -X GET http://localhost:1337/api/v1/job_application/15/ \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```

**Sample Response:**
```json
{
    "data": {
        "id": 15,
        "position_title": "Senior Software Engineer",
        "company_name": "TechCorp Inc.",
        "employment_type": {
            "id": 1,
            "label": "Full-time"
        },
        "work_arrangement": {
            "id": 2,
            "label": "Remote"
        },
        "job_application_status": {
            "id": 1,
            "label": "Applied"
        },
        "job_posting_link": "https://techcorp.com/careers/senior-engineer",
        "date_applied": "2025-08-23",
        "job_location": "San Francisco, CA",
        "job_description": "We are looking for a senior software engineer...",
        "created_at": "2025-08-23T10:30:00Z",
        "updated_at": "2025-08-23T10:30:00Z"
    },
    "message": "Job Application fetched successfully",
    "status_code": 200
}
```

### 4. Update Job Application (Full Update)
**PUT** `/job_application/{id}/`

```bash
curl -X PUT http://localhost:1337/api/v1/job_application/15/ \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -d '{
    "position_title": "Principal Software Engineer",
    "company_name": "TechCorp Inc.",
    "employment_type": 1,
    "work_arrangement": 2,
    "job_application_status": 7,
    "job_posting_link": "https://techcorp.com/careers/principal-engineer",
    "date_applied": "2025-08-23",
    "job_location": "San Francisco, CA",
    "job_description": "Updated description for principal role..."
  }'
```

### 5. Partial Update Job Application
**PATCH** `/job_application/{id}/`

```bash
# Update only the status
curl -X PATCH http://localhost:1337/api/v1/job_application/15/ \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -d '{
    "job_application_status": 7
  }'

# Update multiple fields
curl -X PATCH http://localhost:1337/api/v1/job_application/15/ \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -d '{
    "job_application_status": 7,
    "position_title": "Lead Software Engineer"
  }'
```

**Sample Response:**
```json
{
    "data": {
        "id": 15,
        "position_title": "Lead Software Engineer",
        "company_name": "TechCorp Inc.",
        "employment_type": {
            "id": 1,
            "label": "Full-time"
        },
        "work_arrangement": {
            "id": 2,
            "label": "Remote"
        },
        "job_application_status": {
            "id": 7,
            "label": "Interview Scheduled"
        },
        "job_posting_link": "https://techcorp.com/careers/senior-engineer",
        "date_applied": "2025-08-23",
        "job_location": "San Francisco, CA",
        "job_description": "We are looking for a senior software engineer...",
        "created_at": "2025-08-23T10:30:00Z",
        "updated_at": "2025-08-23T11:15:00Z"
    },
    "message": "Job Application partially updated successfully",
    "status_code": 200
}
```

### 6. Delete Single Job Application
**DELETE** `/job_application/{id}/`

```bash
curl -X DELETE http://localhost:1337/api/v1/job_application/15/ \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```

**Sample Response:**
```json
{
    "data": null,
    "message": "Job Application deleted successfully",
    "status_code": 204
}
```

### 7. Delete All Job Applications
**DELETE** `/job_application/delete-all/`

```bash
curl -X DELETE http://localhost:1337/api/v1/job_application/delete-all/ \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```

**Sample Response:**
```json
{
    "data": {
        "deleted_count": 25
    },
    "message": "Successfully deleted all 25 job application(s) and related data",
    "status_code": 200
}
```

---

## 🔔 Reminder APIs

### 1. Create Reminder
**POST** `/job_application/{job_app_id}/reminder/`

```bash
curl -X POST http://localhost:1337/api/v1/job_application/15/reminder/ \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -d '{
    "title": "Follow up with recruiter",
    "description": "Send a follow-up email after 1 week of application",
    "is_enabled": true,
    "reminder_datetime": "2025-08-30T09:00:00Z"
  }'
```

**Sample Response:**
```json
{
    "data": {
        "id": 8,
        "title": "Follow up with recruiter",
        "description": "Send a follow-up email after 1 week of application",
        "is_enabled": true,
        "reminder_datetime": "2025-08-30T09:00:00Z",
        "created_at": "2025-08-23T10:45:00Z",
        "modified_at": "2025-08-23T10:45:00Z"
    },
    "message": "Reminder created successfully",
    "status_code": 201
}
```

### 2. Bulk Create Reminders
**POST** `/job_application/{job_app_id}/reminder/bulk-create/`

```bash
curl -X POST http://localhost:1337/api/v1/job_application/15/reminder/bulk-create/ \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -d '[
    {
        "title": "Follow up with recruiter",
        "description": "Send a follow-up email after 1 week",
        "is_enabled": true,
        "reminder_datetime": "2025-08-30T09:00:00Z"
    },
    {
        "title": "Prepare for interview",
        "description": "Review company info and prepare answers",
        "is_enabled": true,
        "reminder_datetime": "2025-09-05T14:00:00Z"
    },
    {
        "title": "Thank you email",
        "description": "Send thank you email after interview",
        "is_enabled": false,
        "reminder_datetime": "2025-09-06T10:00:00Z"
    }
  ]'
```

**Sample Response:**
```json
{
    "data": {
        "created_reminders": [
            {
                "id": 9,
                "title": "Follow up with recruiter",
                "description": "Send a follow-up email after 1 week",
                "is_enabled": true,
                "reminder_datetime": "2025-08-30T09:00:00Z",
                "created_at": "2025-08-23T10:50:00Z",
                "modified_at": "2025-08-23T10:50:00Z"
            },
            {
                "id": 10,
                "title": "Prepare for interview",
                "description": "Review company info and prepare answers",
                "is_enabled": true,
                "reminder_datetime": "2025-09-05T14:00:00Z",
                "created_at": "2025-08-23T10:50:00Z",
                "modified_at": "2025-08-23T10:50:00Z"
            },
            {
                "id": 11,
                "title": "Thank you email",
                "description": "Send thank you email after interview",
                "is_enabled": false,
                "reminder_datetime": "2025-09-06T10:00:00Z",
                "created_at": "2025-08-23T10:50:00Z",
                "modified_at": "2025-08-23T10:50:00Z"
            }
        ],
        "created_count": 3
    },
    "message": "Successfully created 3 reminders.",
    "status_code": 201
}
```

### 3. List All Reminders for Job Application
**GET** `/job_application/{job_app_id}/reminder/`

```bash
curl -X GET http://localhost:1337/api/v1/job_application/15/reminder/ \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```

**Sample Response:**
```json
{
    "data": [
        {
            "id": 8,
            "title": "Follow up with recruiter",
            "description": "Send a follow-up email after 1 week of application",
            "is_enabled": true,
            "reminder_datetime": "2025-08-30T09:00:00Z",
            "created_at": "2025-08-23T10:45:00Z",
            "modified_at": "2025-08-23T10:45:00Z"
        },
        {
            "id": 9,
            "title": "Prepare for interview",
            "description": "Review company info and prepare answers",
            "is_enabled": true,
            "reminder_datetime": "2025-09-05T14:00:00Z",
            "created_at": "2025-08-23T10:50:00Z",
            "modified_at": "2025-08-23T10:50:00Z"
        }
    ],
    "message": "Reminders fetched successfully",
    "status_code": 200
}
```

### 4. Get Single Reminder
**GET** `/job_application/{job_app_id}/reminder/{id}/`

```bash
curl -X GET http://localhost:1337/api/v1/job_application/15/reminder/8/ \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```

**Sample Response:**
```json
{
    "data": {
        "id": 8,
        "title": "Follow up with recruiter",
        "description": "Send a follow-up email after 1 week of application",
        "is_enabled": true,
        "reminder_datetime": "2025-08-30T09:00:00Z",
        "created_at": "2025-08-23T10:45:00Z",
        "modified_at": "2025-08-23T10:45:00Z"
    },
    "message": "Reminder retrieved successfully",
    "status_code": 200
}
```

### 5. Update Reminder (Full Update)
**PUT** `/job_application/{job_app_id}/reminder/{id}/`

```bash
curl -X PUT http://localhost:1337/api/v1/job_application/15/reminder/8/ \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -d '{
    "title": "Updated: Follow up with recruiter",
    "description": "Send a follow-up email after 1 week of application - updated",
    "is_enabled": true,
    "reminder_datetime": "2025-08-31T10:00:00Z"
  }'
```

### 6. Partial Update Reminder
**PATCH** `/job_application/{job_app_id}/reminder/{id}/`

```bash
# Update only the enabled status
curl -X PATCH http://localhost:1337/api/v1/job_application/15/reminder/8/ \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -d '{
    "is_enabled": false
  }'

# Update multiple fields
curl -X PATCH http://localhost:1337/api/v1/job_application/15/reminder/8/ \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -d '{
    "title": "URGENT: Follow up with recruiter",
    "reminder_datetime": "2025-08-25T09:00:00Z"
  }'
```

**Sample Response:**
```json
{
    "data": {
        "id": 8,
        "title": "URGENT: Follow up with recruiter",
        "description": "Send a follow-up email after 1 week of application",
        "is_enabled": true,
        "reminder_datetime": "2025-08-25T09:00:00Z",
        "created_at": "2025-08-23T10:45:00Z",
        "modified_at": "2025-08-23T11:30:00Z"
    },
    "message": "Reminder partially updated successfully",
    "status_code": 200
}
```

### 7. Delete Reminder
**DELETE** `/job_application/{job_app_id}/reminder/{id}/`

```bash
curl -X DELETE http://localhost:1337/api/v1/job_application/15/reminder/8/ \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```

**Sample Response:**
```json
{
    "data": null,
    "message": "Reminder deleted successfully",
    "status_code": 204
}
```

---

## 📊 Lookup Values

### Employment Types
- `1` - Full-time
- `2` - Part-time
- `3` - Contract

### Work Arrangements
- `1` - On-site
- `2` - Remote
- `3` - Hybrid

### Job Application Status
- `1` - Default (newly added)
- `2` - Applied
- `3` - Rejected
- `4` - Ghosted
- `5` - Under Process (U.P.)
- `6` - Offer Received (O.R.)
- `7` - Interview Scheduled (I.S.)
- `8` - Withdrawn/Cancelled (W/C)
- `9` - Accepted Offer (A.O.)

---

## 🚨 Error Responses

### 400 Bad Request (Validation Error)
```json
{
    "data": null,
    "message": "Missing fields: position_title, company_name",
    "status_code": 400
}
```

### 403 Forbidden (Unauthorized Access)
```json
{
    "data": {},
    "message": "You are not authorized to access this job application.",
    "status_code": 403
}
```

### 404 Not Found
```json
{
    "data": {},
    "message": "Job Application not found.",
    "status_code": 404
}
```

---

## 💡 Tips

1. **Always use JWT tokens** for authentication
2. **Use appropriate HTTP methods**: GET for reading, POST for creating, PUT for full updates, PATCH for partial updates, DELETE for deletion
3. **Take advantage of filtering** in the list endpoint to narrow down results
4. **Use bulk create** for reminders when you need to create multiple reminders at once
5. **Pagination** is available for the job applications list - use `?page=1`, `?page=2`, etc.
6. **Date formats** should be in ISO format: `YYYY-MM-DD` for dates, `YYYY-MM-DDTHH:MM:SSZ` for datetimes
