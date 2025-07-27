
# Trackjob Backend API

A  backend service for the TrackJob Mobile Application created using Django REST Framework and MySQL.


## Installation

Clone the repository

```bash
git clone https://github.com/wdotgonzales/trackjob-backend.git
```
Go to project directory
```bash
cd trackjob-backend/
```
Create and activate a virtual environment inside the app/ directory

```bash
cd app/
python3 -m venv venv
source venv/bin/activate
```
Copy .env.dev from the example
```bash
cp .env.dev.example .env.dev
```

Go back to root directory
```bash
cd ..
```

Setup .env.dev credentials

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
Copy docker-compose.yml from the example

```bash
cp docker-compose.example.yml docker-compose.yml
```

Setup docker-compose.yml credentials

```bash
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
      - MYSQL_DATABASE=your_db_name_here <!-- Use the same database name as in .env.dev -->
      - MYSQL_USER=your_db_user_here <!-- Use the same user as in .env.dev -->
      - MYSQL_PASSWORD=your_db_password_here <!-- Use the same password as in .env.dev -->
      - MYSQL_ROOT_PASSWORD=your_root_password_here <!-- Use the same root password as in .env.dev -->
    
      
volumes:
  mysql_data:

```
Build and run the containers
```bash
docker-compose -f docker-compose.yml up -d --build
```

Access the app in your browser at:
```bash
http://localhost:8080/
```
