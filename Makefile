# Variables
COMPOSE=docker compose
EXEC=$(COMPOSE) exec app

.PHONY: setup up down build dev fresh-env tinker migrate fresh test logs cache-clear

# 🚀 Full First-Time Setup
# Use this to get a brand new environment running from zero
setup: build-no-cache
<<<<<<< HEAD
=======
	@if [ ! -f .env ]; then cp .env.example .env; fi
>>>>>>> f3c01c7 (NHMP-HMS STARTED)
	$(COMPOSE) up -d
	sleep 5
	$(EXEC) php artisan key:generate
	$(EXEC) php artisan migrate:fresh --seed
	@echo "✅ Setup complete! Visit http://localhost:8080"

# Start the environment in the background
up:
	$(COMPOSE) up -d

# Start with Watch mode (Best for active coding)
dev:
	$(COMPOSE) up --watch

# Stop all containers
down:
	$(COMPOSE) down

# Rebuild images without cache (Use when Dockerfile changes)
build-no-cache:
	$(COMPOSE) build --no-cache

# Wipe volumes and rebuild (The 'Nuclear' option)
fresh-env:
	$(COMPOSE) down -v
	$(COMPOSE) up -d --build
	$(EXEC) php artisan key:generate || true
	$(EXEC) php artisan migrate:fresh --seed
	$(COMPOSE) logs -f

# 🛠 Laravel Commands
tinker:
	$(EXEC) php artisan tinker

migrate:
	$(EXEC) php artisan migrate

fresh:
	$(EXEC) php artisan migrate:fresh --seed

test:
	$(EXEC) php artisan test

logs:
	$(COMPOSE) logs -f

# 🧹 Maintenance
cache-clear:
	$(EXEC) php artisan optimize:clear
	@echo "✅ All caches cleared!"

# Optional: Clear everything and restart
restart-clear: down up cache-clear