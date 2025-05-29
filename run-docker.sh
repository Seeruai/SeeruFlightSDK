#!/bin/bash

# Export the API token from example.php if not set in environment
if [ -z "$SEERU_API_TOKEN" ]; then
    export SEERU_API_TOKEN=$(grep -o "apiToken = '[^']*'" example.php | cut -d"'" -f2)
fi

# Stop and remove containers, remove volumes and images
echo "Cleaning up..."
docker-compose down -v
docker rmi devsdk_app --force 2>/dev/null

# Rebuild and run
echo "Building and running..."
docker-compose up --build 