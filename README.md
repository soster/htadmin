HTAdmin
=======

HTAdmin is a simple .htaccess and .htpasswd editor implemented in PHP with a nice frontend (based on bootstrap). It's intended to secure a folder of plain html files with multiple users. The admin has to create a user, but every user can change his password by himself using a self service area. It is also possible to send a password reset mail.

It comes with a preconfigured Vagrant / Puppet VM, so you don't have to install a LAMP stack locally for testing.

You find the application in `sites/html/htadmin`.

![Screenshot](screenshot.png "Screenshot")

Just install vagrant and virtual box and type

`vagrant up`
 
to start the vm. After startup point your browser to:

<http://localhost/htadmin/>

Standard access: admin / admin, make sure to change that in your `...config/config.ini`. You have to enter a hashed password, there is a tool for its generation included in the webapp:

<http://localhost/htadmin/adminpwd.php>

the .htaccess and .htpasswd files are configured for this folder:

<http://localhost/test/>

Uses the following libraries:

<https://github.com/PHPMailer/PHPMailer>


Enjoy!
