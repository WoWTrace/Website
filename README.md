# WIP: WoWData.tools

## Setup
1. Copy .env.example to .env
2. Configuration your settings in .env file
3. Run update.sh
4. Run php artisan key:generate
5. Create a cronjob for every minute with the command ```php artisan schedule:run```
6. Configure your Webserver to serve the public folder
7. Create a User for your TACT.Builder instances with ```php artisan orchid:create user_name user_email```