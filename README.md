# TrackJob Backend

A Python backend service for the TrackJob application using Docker and environment-based configuration.

## 🔧 Quick Setup (Single Bash Block)

```bash
# 1. Clone the repository
git clone https://github.com/your-username/trackjob-backend.git
cd trackjob-backend

# 2. Create and activate a virtual environment inside the app/ directory
cd app/
python3 -m venv venv
source venv/bin/activate

# 3. Go back to the project root
cd ..

# 4. Copy .env.dev from the example
cp .env.dev.example .env.dev
# (Edit .env.dev and fill in the necessary credentials)

# 5. Copy docker-compose.yml from the example
cp docker-compose.example.yml docker-compose.yml
# (Edit docker-compose.yml and configure your services)

# 6. Build and run the containers
docker-compose -f docker-compose.yml up -d --build

# 7. Access the app in your browser at:
# http://localhost:8080/
