# php_mvc
# php-login-management with mvc

# NOTE
# SORRY FOR MY ENGLISH
# RUN THIS ON WEB SERVER (LIKE XAMPP/MAMPP/WAMPP/NGINX OR WHATEVER IT IS) OTHERWISE IT WILL NOT WORK
# YOU MUST HAVE COMPOSER
# YOU MUST HAVE MySQL DATABASE SERVER
# I USING TERM DIRECTORY INSTEAD FOLDER

@author Ahmad Faisal
@city Depok
@country Indonesia
@email ahmadfaisal718970@gmail.com

Follow this step

1. Create Database (php_login_management and php_login_management_test)
2. Create Directory (the name is up to you but i recommend "php-login-management")
3. Put all of this Directory (in this repo) inside Directory that you've created
4. Run composer command "composer init" under Root Directory
5. Then run composer command again "composer install"
6. Run Web Server and MySQL Server
7. Open <directory You've Created>/public/index.php in your browser
8. And it should run

The Project Directory will look like this

<Directory that you created>
|
|--- app
|    |
|    --- App
|    |
|    --- Config
|    |
|    --- Controller
|    |
|    --- Domain
|    |
|    --- Exception
|    |
|    --- Middleware
|    |
|    --- Model
|    |
|    --- Repository
|    |
|    --- Service
|    |
|    --- View
|
|--- config
|
|--- public
|
|--- test
|
|--- composer.json
|
|--- php_login_management.sql
|
|--- php_login_management_test.sql
