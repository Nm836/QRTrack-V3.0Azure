#!/bin/bash

# Install Python dependencies
pip install -r /home/site/wwwroot/requirements.txt || { echo 'Failed to install Python dependencies'; exit 1; }

# Start the Python Flask app using gunicorn (binding to port 8000)
nohup gunicorn --bind 0.0.0.0:8000 app:app &

# Start the PHP application using Apache
apache2-foreground
