# TrackJob Backend Installation Guide

## Prerequisites
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
    command: python manage.py runserver 0.0.0.0:8000
    volumes:
      - ./app/:/usr/src/app/
    ports:
      - "8080:8000"
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
      - MYSQL_DATABASE=trackjob_db # Must match SQL_DATABASE in .env.dev
      - MYSQL_USER=trackjob_user # Must match SQL_USER in .env.dev
      - MYSQL_PASSWORD=your_secure_password # Must match SQL_PASSWORD in .env.dev
      - MYSQL_ROOT_PASSWORD=your_root_password_here # Set a secure root password
    
volumes:
  mysql_data:
```

### 10. Build and run the containers
```bash
docker-compose -f docker-compose.yml up -d --build
```

### 11. Access the application
Open your browser and go to:
```
http://localhost:8080/
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

### Issue: Port 8080 already in use
**Solution:** Change the port in docker-compose.yml:
```yaml
ports:
  - "8081:8000"  # Change 8080 to any available port
```

## Alternative: Using WSL 2 (Recommended for Better Performance)
For the best Docker experience on Windows, consider using WSL 2:

1. Install WSL 2 and a Linux distribution (Ubuntu recommended)
2. Clone and run the project inside WSL 2
3. Enable WSL 2 integration in Docker Desktop
4. Follow the standard Linux installation steps

This approach eliminates most Windows-specific issues and provides better performance.
