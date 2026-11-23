#!/bin/bash

printenv | grep -v '^_=' | while IFS='=' read -r k v; do
    printf 'export %s=%q\n' "$k" "$v"
done > /etc/container.env
chmod 600 /etc/container.env

# Start the cron service
service cron start

# Run the original Apache foreground command provided by the PHP image
apache2-foreground
