# TrackJob Backend Installation Guide

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/wdotgonzales/trackjob-backend.git
```

### 2. Go to project directory

```bash
cd trackjob-backend/
```

### 3. Create and activate a virtual environment inside the app/ directory

```bash
cd app/
python3 -m venv venv
```

#### Activate virtual environment:

**For Linux/macOS:**
```bash
source venv/bin/activate
```

**For Windows (Command Prompt):**
```cmd
venv\Scripts\activate.bat
```

**For Windows (PowerShell):**
```powershell
venv\Scripts\Activate.ps1
```

**For Windows (Git Bash):**
```bash
venv\Scripts\Activate
```

> **Note:** If you encounter execution policy issues on Windows PowerShell, you may need to run:
> ```powershell
> Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
> ```

### 4. Go back to root directory

```bash
cd ..
```

### 5. Copy .env.dev from the example

```bash
cp .env.dev.example .env.dev
```

### 6. Setup .env.dev credentials

Edit the `.env.dev` file with your credentials:

```bash
# Django settings
DEBUG=1
SECRET_KEY=your-secret-key-here
DJANGO_ALLOWED_HOSTS=localhost 127.0.0.1 [::1]

# Database settings
SQL_ENGINE=django.db.backends.mysql
SQL_DATABASE=your-db-name
SQL_USER=your-db-user
SQL_PASSWORD=your-db-password
SQL_HOST=db
SQL_PORT=3306
DATABASE=mysql
```

### 7. Copy docker-compose.yml from the example

```bash
cp docker-compose.example.yml docker-compose.yml
```

### 8. Setup docker-compose.yml credentials

Edit the `docker-compose.yml` file with your database credentials:

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
      - MYSQL_DATABASE=your_db_name_here # Use the same database name as in .env.dev
      - MYSQL_USER=your_db_user_here # Use the same user as in .env.dev
      - MYSQL_PASSWORD=your_db_password_here # Use the same password as in .env.dev
      - MYSQL_ROOT_PASSWORD=your_root_password_here # Set a secure root password
    
volumes:
  mysql_data:
```

### 9. Build and run the containers

```bash
docker-compose -f docker-compose.yml up -d --build
```

### 10. Access the application

Open your browser and go to:
```
http://localhost:8080/
```
