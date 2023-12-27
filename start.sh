#!/bin/bash

# Set the debounce delay in seconds
debounce_delay=5

# Initialize a variable to store the last event timestamp
last_event_time=0

# Function to run the command
run_my_command() {
    # Your command to run when a file change occurs
	# docker-compose restart php
	frankenphp reload --config /etc/caddy/Caddyfile
    # Replace the echo statement with your actual command
}

frankenphp run --config /etc/caddy/Caddyfile --adapter caddyfile &

# Monitor the current directory for file changes
inotifywait -m -e create,modify,delete . |
    while read -r directory event file; do
        # Get the current timestamp
        current_time=$(date +%s)

        # Calculate the time elapsed since the last event
        time_since_last_event=$((current_time - last_event_time))

        # If the time elapsed is greater than or equal to the debounce delay, run the command
        if [ $time_since_last_event -ge $debounce_delay ]; then
            run_my_command
        fi

        # Update the last event timestamp
        last_event_time=$current_time
    done
