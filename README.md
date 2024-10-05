# Setup Instructions for Laravel Project

This document outlines the steps to set up a Laravel project with Docker using Sail.

## Commands and Definitions

1. **Clone the Repository**
   ```bash
   git clone https://github.com/Adilshah12324/news_aggregator_api_task.git
   ```
   Clones the project repository from the specified URL to your local machine.

2. **Update Package Lists**
   ```bash
   sudo apt update
   ```
   Updates the local package index to ensure you have the latest information on available packages.

3. **Install PHP and Extensions**
   ```bash
   sudo apt install php8.2 php8.2-cli php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd
   ```
   Installs PHP 8.2 and various essential PHP extensions needed for running Laravel.

4. **Update Composer Dependencies**
   ```bash
   composer update
   ```
   Updates the project dependencies as defined in the `composer.json` file to their latest versions.

5. **Require Laravel Sail**
   ```bash
   composer require laravel/sail --dev
   ```
   Installs Laravel Sail, a lightweight command-line interface for running Laravel applications in Docker.

6. **Start Sail in Detached Mode**
   ```bash
   ./vendor/bin/sail up -d
   ```
   Starts the Sail Docker containers in the background, allowing your application to run.

7. **Migrate Database and Seed**
   ```bash
   ./vendor/bin/sail artisan migrate:fresh --seed
   ```
   Resets the database by running all migrations fresh and seeds it with users table data where user can login through and the other news sources can be stored through API's and also running task schedular..

8. **Start the Scheduler**
   ```bash
   ./vendor/bin/sail artisan schedule:work
   ```
   Starts the Laravel task scheduler, allowing scheduled tasks to run as defined in the application.

8. **API Documentation (Swagger)**
    FileName
   ```bash
   API-documentaion-swagger-editor.yaml
   ```
   use this code in the swagger editor by visiting below link:
   https://editor.swagger.io/

## Conclusion

Follow these steps to set up your Laravel project with Docker and ensure all necessary components are properly configured.
