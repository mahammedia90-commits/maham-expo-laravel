.PHONY: help up down build restart logs status clean fresh migrate seed shell-auth shell-expo

# ==========================================
#  Maham Expo - Docker Commands
# ==========================================

help: ## Show this help
	@echo ""
	@echo "  Maham Expo - Docker Commands"
	@echo "  ============================"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2}'
	@echo ""

# ==========================================
#  Docker Commands
# ==========================================

up: ## Start all services
	docker compose up -d
	@echo ""
	@echo "========================================="
	@echo "  Services Started!"
	@echo "========================================="
	@echo "  Auth Service:  http://localhost:8001"
	@echo "  Expo API:      http://localhost:8002"
	@echo "  phpMyAdmin:    http://localhost:8080"
	@echo "========================================="

down: ## Stop all services
	docker compose down

build: ## Build all images
	docker compose build --no-cache

restart: ## Restart all services
	docker compose restart

logs: ## Show logs for all services
	docker compose logs -f

logs-auth: ## Show auth service logs
	docker compose logs -f auth-service

logs-expo: ## Show expo api logs
	docker compose logs -f expo-api

status: ## Show status of all containers
	docker compose ps

# ==========================================
#  Database Commands
# ==========================================

migrate: ## Run migrations for all services
	docker compose exec auth-service php artisan migrate --force
	docker compose exec expo-api php artisan migrate --force

migrate-auth: ## Run auth service migrations
	docker compose exec auth-service php artisan migrate --force

migrate-expo: ## Run expo api migrations
	docker compose exec expo-api php artisan migrate --force

seed: ## Run seeders for all services
	docker compose exec auth-service php artisan db:seed --force
	docker compose exec expo-api php artisan db:seed --force

seed-auth: ## Run auth service seeders
	docker compose exec auth-service php artisan db:seed --force

seed-expo: ## Run expo api seeders
	docker compose exec expo-api php artisan db:seed --force

fresh: ## Fresh migrate + seed all services
	docker compose exec auth-service php artisan migrate:fresh --seed --force
	docker compose exec expo-api php artisan migrate:fresh --seed --force

fresh-auth: ## Fresh migrate + seed auth service
	docker compose exec auth-service php artisan migrate:fresh --seed --force

fresh-expo: ## Fresh migrate + seed expo api
	docker compose exec expo-api php artisan migrate:fresh --seed --force

# ==========================================
#  Shell Access
# ==========================================

shell-auth: ## Open shell in auth service
	docker compose exec auth-service bash

shell-expo: ## Open shell in expo api
	docker compose exec expo-api bash

tinker-auth: ## Open tinker in auth service
	docker compose exec auth-service php artisan tinker

tinker-expo: ## Open tinker in expo api
	docker compose exec expo-api php artisan tinker

# ==========================================
#  Artisan Commands
# ==========================================

artisan-auth: ## Run artisan command in auth (usage: make artisan-auth cmd="route:list")
	docker compose exec auth-service php artisan $(cmd)

artisan-expo: ## Run artisan command in expo (usage: make artisan-expo cmd="route:list")
	docker compose exec expo-api php artisan $(cmd)

routes-auth: ## Show auth service routes
	docker compose exec auth-service php artisan route:list

routes-expo: ## Show expo api routes
	docker compose exec expo-api php artisan route:list

# ==========================================
#  Cleanup
# ==========================================

clean: ## Stop and remove all containers, volumes, and images
	docker compose down -v --rmi local
	@echo "Cleaned!"

prune: ## Remove unused Docker resources
	docker system prune -f
