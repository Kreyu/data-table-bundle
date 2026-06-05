# Dockerised test environment — mirrors the GitHub Actions CI.
# Usage: `make install` once, then `make test` / `make phpstan` / `make cs-check`.
# Override the PHP version: `make test PHP_VERSION=8.5`.

PHP_VERSION ?= 8.1
HOST_UID := $(shell id -u)
HOST_GID := $(shell id -g)
export PHP_VERSION HOST_UID HOST_GID

DC := docker compose
RUN := $(DC) run --rm php

.DEFAULT_GOAL := help

.PHONY: help build install test phpstan cs-check cs-fix quality ci shell matrix

help: ## List available targets
	@grep -hE '^[a-zA-Z_-]+:.*?## ' $(MAKEFILE_LIST) | awk 'BEGIN{FS=":.*?## "}{printf "  \033[36m%-12s\033[0m %s\n", $$1, $$2}'

build: ## Build the PHP image
	$(DC) build

install: build ## Resolve & install dependencies for the container PHP (composer.lock is gitignored)
	$(RUN) composer update --no-interaction

test: ## Run PHPUnit
	$(RUN) vendor/bin/phpunit

phpstan: ## Run PHPStan (CI uses PHP 8.1)
	$(RUN) vendor/bin/phpstan analyse -c docker/phpstan.neon --memory-limit=1G

cs-check: ## Check code style (dry-run)
	$(RUN) vendor/bin/php-cs-fixer fix --dry-run --diff

cs-fix: ## Apply code style fixes
	$(RUN) vendor/bin/php-cs-fixer fix --diff

quality: cs-check phpstan ## Run all code-quality checks

ci: install test phpstan cs-check ## Reproduce the full CI run on PHP $(PHP_VERSION)

shell: ## Open a shell in the container
	$(RUN) bash

matrix: ## Run PHPUnit across PHP 8.1-8.5 x {lowest,highest} (host files untouched)
	@for v in 8.1 8.2 8.3 8.4 8.5; do \
		for d in lowest highest; do \
			echo "==> PHP $$v / $$d dependencies"; \
			PHP_VERSION=$$v $(DC) build >/dev/null || exit 1; \
			PHP_VERSION=$$v $(RUN) bash -c '\
				cp -a /app /tmp/build && cd /tmp/build && rm -rf vendor composer.lock && \
				composer update '"$$([ $$d = lowest ] && echo --prefer-lowest --prefer-stable || echo --prefer-stable)"' --no-interaction -q && \
				vendor/bin/phpunit' || exit 1; \
		done; \
	done
	@echo "==> matrix complete"
