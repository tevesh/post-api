# Run project in docker

### Run docker for mac
```bash
open -a docker
```

### Build
```bash
USER_ID=$(id -u) USER_GROUP=$(id -g) docker-compose up --build --remove-orphans -d
```

### Run
```bash
USER_ID=$(id -u) USER_GROUP=$(id -g) docker-compose up -d
```

### Stop
```bash
USER_ID=$(id -u) USER_GROUP=$(id -g) docker-compose stop
```

### Down
```bash
USER_ID=$(id -u) USER_GROUP=$(id -g) docker-compose down
```

# Doctrine Migration

### Create or update entity
```bash
php bin/console make:entity
```
### Create a migration file
```bash
php bin/console make:migration
```
### Migrate actual database to new one with changes
```bash
php bin/console doctrine:migrations:migrate
```

## If something fail

### Drop current database
```bash
php bin/console doctrine:database:drop --force
```
### Create new database
```bash
php bin/console doctrine:database:create
```
### Apply migration
```bash
php bin/console doctrine:migrations:migrate
```
### Generate new migration
```bash
php bin/console make:migration
```
### Apply fixture
```bash
php bin/console doctrine:fixtures:load
```