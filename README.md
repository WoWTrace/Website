# WIP: WoWTrace

## Philosophy 
The website is only responsible for displaying the data collected in the backend project and does not process any Blizzard data itself.

## Setup
1. Copy .env.example to .env
2. Configure your settings in .env file
3. Run update.sh
4. Run php artisan key:generate
5. Configure your Webserver to serve the public folder
6. Create a Admin Account for you ```php artisan orchid:create user_name user_email```
