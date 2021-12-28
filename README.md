# WIP: WoWData.tools

## Setup
1. Copy .env.example to .env
2. Configuration your settings in .env file
3. Run update.sh
4. Run php artisan key:generate
5. Create a cronjob for every minute with the command ```php artisan schedule:run```
6. Configure your Webserver to serve the public folder
7. Create a Admin Account for you ```php artisan orchid:create user_name user_email```
8. Create a systemctl job with autorestart and the command ```php artisan queue:listen --timeout 3600```