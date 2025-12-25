#!/bin/sh

# Create and setup storage directories
mkdir -p /application/storage/logs
mkdir -p /application/storage/framework/cache
mkdir -p /application/storage/framework/sessions
mkdir -p /application/storage/framework/views
mkdir -p /application/bootstrap/cache

# Set proper permissions
chown -R www-data:www-data /application/storage /application/bootstrap/cache
chmod -R 775 /application/storage /application/bootstrap/cache

# Execute the command
exec "$@"
