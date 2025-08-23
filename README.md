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
