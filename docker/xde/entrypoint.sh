#!/bin/bash

printenv | grep -v '^_=' | sed 's/^\([^=]*\)=\(.*\)$/export \1="\2"/' > /etc/container.env
chmod 600 /etc/container.env

# Start the cron service
service cron start

# Run the original Apache foreground command provided by the PHP image
apache2-foreground
