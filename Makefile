all: dev-setup build-production 
serve: dev-setup watch 

dev-setup: clean npm-init

## auto-generated help commands based on https://gist.github.com/prwhite/8168133
help: ## Show this help.
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//'

npm-init: ## download all frontend dependencies based on package-lock.json
	docker-compose run --rm node npm ci

npm-update: ## update package-lock.json file
	docker-compose run --rm node npm update

watch: ## uses docker-compose to run a webpack server with hot-reload on background.
watch: npm-update
	docker-compose up -d

build-production: ## build production files inside `js` folder
build-production: lint
	docker-compose run --rm node npm run build

lint-fix: ## try to fix lint problems
	docker-compose run --rm node npm run lint:fix
	docker-compose run --rm node npm run stylelint:fix

lint: ## check all files for lint problems
	docker-compose run --rm node npm run lint
	docker-compose run --rm node npm run stylelint

clean: ## clear builded files and node_modules
	rm -rf js/*
	rm -rf node_modules
