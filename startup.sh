#!/bin/bash

# Install Python dependencies
pip install -r /home/site/wwwroot/requirements.txt

# Start the Python Flask app in the background
nohup python3 /home/site/wwwroot/app.py &

# Start the PHP application using Apache
apache2-foreground
