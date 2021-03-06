DragonPHP

Phalcon is a web framework delivered as a C extension providing high performance and lower resource consumption.

This is a mutli-module application for the Phalcon Framework. We expect to implement as many features as possible to showcase the framework and its potential.

Please write us if you have any feedback.

Thanks.
NOTE

Demo

![项目演示](https://github.com/kideny/dragonphp/blob/master/samples/backend.png)

The master branch will always contain the latest stable version. If you wish to check older versions or newer ones currently under development, please switch to the relevant branch.
Get Started
Requirements

To run this application on your machine, you need at least:

    PHP >= 5.6

    Phalcon >= 3.0

    Apache Web Server with mod_rewrite enabled, and AllowOverride Options (or All) in your httpd.conf or Nginx Web Server
    Latest Phalcon Framework extension installed/enabled

    MySQL >= 5.6

Then you'll need to create the database and initialize schema:

echo 'CREATE DATABASE dragonphp' | mysql -u root
cat schemas/dragonphp.sql | mysql -u root dragonphp

Also you can override application config by creating app/config/config.php (already gitignored).
Installing Dependencies via Composer

DragonPHP's dependencies must be installed using Composer. Install composer in a common location or in your project:

curl -s http://getcomposer.org/installer | php

Run the composer installer:

cd dragonphp
php composer.phar install

Improving this Sample

Phalcon is an open source project and a volunteer effort. DragonPHP does not have human resources fully dedicated to the mainteniance of this software. If you want something to be improved or you want a new feature please submit a Pull Request.
License

DragonPHP is open-sourced software licensed under the New MIT License.