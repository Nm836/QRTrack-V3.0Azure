#!/bin/bash

# Install Python dependencies
pip install -r /home/site/wwwroot/requirements.txt || { echo 'Failed to install Python dependencies'; exit 1; }

# Start the Python Flask app in the background
nohup python3.9 /home/site/wwwroot/app.py &

# Start the PHP application using Apache
apache2-foreground
