#!/bin/bash

# Install ODBC Driver 17 for SQL Server (Ubuntu-based example)
curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add -
curl https://packages.microsoft.com/config/ubuntu/20.04/prod.list > /etc/apt/sources.list.d/mssql-release.list
apt-get update
ACCEPT_EULA=Y apt-get install -y msodbcsql17 unixodbc-dev || { echo 'Failed to install ODBC Driver 17'; exit 1; }

# Install Python dependencies
pip install -r /home/site/wwwroot/requirements.txt || { echo 'Failed to install Python dependencies'; exit 1; }

# Start the Python Flask app in the background
nohup python3.9 /home/site/wwwroot/app.py &

# Start the PHP application using Apache
apache2-foreground


