#!/usr/bin/env bash

#Exit immediately if a pipeline returns a non-zero status.
set -e

# Create a .env.php file
composer dump-env ${ENVIRONMENT}

if  [[ "dev" = "${ENVIRONMENT}" ]]; then
    printf "\n==> Change groupid to ${USER_GROUP}\n";
    groupmod --non-unique --gid ${USER_GROUP} www-data
    printf "\n==> Change userid to ${USER_ID}\n";
    usermod --non-unique --uid ${USER_ID} www-data
    printf "\n==> Change ownership of ${PROJECT_PATH} folder\n";
    chown -R www-data:www-data ${PROJECT_PATH}
fi

# Replaces the shell without creating a new process
source /usr/local/bin/docker-php-entrypoint "$@"
