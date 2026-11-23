HTAdmin
=======

Stack: nginx 1.30 + php-fpm 8.5 (Debian), PHPMailer 7.2.0, jQuery 3.8.0, Bootstrap 3.4.1 - all vendored, no CDN dependencies.

HTAdmin is a simple htpasswd editor implemented in PHP with a nice frontend (based on bootstrap). It's intended to update and maintain users and password hashes in a .htpasswd file. The admin has to create a user, but every user can change his password by himself using a self service area. It is also possible to send a password reset mail. You can use the .htpasswd with nginx and Apache, in the example we use nginx.

It comes with a preconfigured docker compose file, so you don't have to install a LAMP stack locally for testing but can use docker instead.

You find the application in `app/htadmin` and the area to secure for testing in `app/test`.

![Screenshot](screenshot.png "Screenshot")

Since both the nginx and the php-fpm run as the www-data user, change the group for the app folder and everything within:

`chgrp -R www-data app`

PHP needs write permission for the user www-data.

Create a config.ini within `app/htadmin/config` from the included example file `config.ini.example`.

Install a recent docker and the docker compose plugin and run:

`docker compose up -d`
 
After the build of the PHP image and startup of the containers point your browser to:

<http://localhost/htadmin/>

Standard access: admin / admin, make sure to change that in your `...config/config.ini` for other purposes than testing. You have to enter a hashed password, there is a tool for its generation included in the webapp (one of the included web pages):

<http://localhost/htadmin/adminpwd.php>

the .htaccess and .htpasswd files are configured for this folder:

<http://localhost/test/>

Uses the following libraries (vendored in `app/htadmin`):

- PHPMailer (vendored in `app/htadmin/phpmailer`)
- jQuery and Bootstrap (vendored in `app/htadmin/script` and `app/htadmin/styles`)

Based on nginx and php-fpm images (see `nginx.dockerfile` and `php.dockerfile`). Xdebug is installed in the PHP image but stays disabled unless `PHP_XDEBUG_ENABLED=1` is set in the environment; set `PHP_XDEBUG_ENABLED=0` to keep the container free of debug overhead.


Enjoy!
