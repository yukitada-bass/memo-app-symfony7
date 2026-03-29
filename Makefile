# ===== Docker =====
up:
	docker compose up -d

down:
	docker compose down

restart:
	docker compose restart

ps:
	docker compose ps

logs:
	docker compose logs -f

# ===== PHPコンテナ =====
php:
	docker compose exec php bash

# ===== Nodeコンテナ =====
node:
	docker compose exec node bash

# ===== Symfony =====
sf:
	docker compose exec php php bin/console

# コマンド付きで実行（重要）
c:
	docker compose exec php php bin/console $(filter-out $@,$(MAKECMDGOALS))

# ===== Composer =====
composer:
	docker compose exec php composer $(filter-out $@,$(MAKECMDGOALS))

# ===== DB =====
db:
	docker compose exec postgres psql -U root -d app

# ===== Migration =====
migrate:
	docker compose exec php php bin/console doctrine:migrations:migrate

diff:
	docker compose exec php php bin/console make:migration

# ===== Cache =====
cache-clear:
	docker compose exec php php bin/console cache:clear

# ===== 初期セットアップ =====
init:
	make up
	make composer install
	make migrate

# ===== 掃除 =====
clean:
	docker system prune -f

# 引数をエラーにしないため
%:
	@: