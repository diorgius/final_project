#!/usr/bin/env bash

set -e

echo "========================================"
echo "   AdTechTracker installation"
echo "========================================"

echo $UID
echo $(id -g)


export UID
export GID

echo ""
echo "[1] Copy .env"

    [ -f .env ] || cp .env.example .env
    echo "docker/.env created."
    [ -f ../adtechtracker.local/.env ] || cp ../adtechtracker.local/.env.example ../adtechtracker.local/.env
    echo "adtechtracker.local/.env created."

echo ""
echo "[2] Building Docker images..."

docker compose build \
    --build-arg UID=$UID \
    --build-arg GID=$(id -g)

echo ""
echo "[3] Starting infrastructure..."

docker compose up -d \
    adtechtracker-mysql \
    adtechtracker-php \
    adtechtracker-redis \
    adtechtracker-memcached \
    adtechtracker-mailpit \
    adtechtracker-nginx \
    adtechtracker-phpmyadmin

echo ""
echo "[4] Installing Composer dependencies..."

docker compose exec -T adtechtracker-php composer install

echo ""
echo "[5] Installing NPM dependencies..."

docker compose run --rm --user "$UID:$(id -g)" adtechtracker-node npm install

echo ""
echo "[6] Configuring Laravel..."

docker compose exec -T adtechtracker-php php artisan key:generate

echo ""
echo "[7] Running migrations and seeders..."

docker compose exec -T adtechtracker-php php artisan migrate --seed

echo ""
echo "[8] Starting Node, Reverb, Worker and Scheduler..."

docker compose up -d \
    adtechtracker-node \
    adtechtracker-worker \
    adtechtracker-scheduler \
    adtechtracker-reverb

echo ""
echo "========================================"
echo " Installation completed successfully!"
echo "========================================"
echo ""
echo Please add the following line to your hosts file:
echo 127.0.0.1 adtechtracker.local
echo ""
echo If you are using a virtual machine,
echo replace 127.0.0.1 with the VM IP address.
echo ""
echo "Application : http://adtechtracker.local"
echo "PhpMyAdmin : http://adtechtracker.local:81"
echo "Mailpit    : http://adtechtracker.local:8025"