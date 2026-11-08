#!/bin/bash

# Start the cron service
service cron start

# Run the original Apache foreground command provided by the PHP image
apache2-foreground