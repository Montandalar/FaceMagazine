FaceMagazine
============
FaceMagazine is a  facebook/reddit-like social network written in PHP, with
branches supporting Oracle database and MongoDB.

![View when logged in](screenshot.png)

Features
========
* Create user accounts with visibility settings
    * Private: only you can see your account's posts.
    * Friends-only: only you can your friends can see your account's posts.
    * Public: Anyone can see your posts
* Search for and add friends
    * Search looks for full name and nickname.
    * The other person has to accept your request.
* A newsfeed that shows:
    * Your posts
    * Friends' posts
    * Public posts
* Delete your account, including all your posts!
* Update your profile
    * This is only shown on the search screen, as there are no profile pages.
    * Like other peoples' posts, with facebook-like flavour text telling you
    to 'Be the first to like this', or 'So-and-so and 2 others like this'.

Installation
============

Requirements
------------
* PHP 5 or 7.
* A webserver with the PHP module installed. Apache httpd was used for testing.
* An Oracle or MongoDB database.
* `pecl` in order to install PHP modules for the database.
* `composer` in order to install the dependencies for MongoDB.

Steps
-----
1. Use git to check out either branch `mongo` or `oracle`, depending on which
is the database system that you will use for FaceMagazine.

2. Change the connection string in `fbl_common.php` to connect to your database.

3. Install the PHP source into your web server directory. e.g. /var/www/html/
is the default for Apache.

4. If using MongoDB, install the dependencies with pecl:

    pecl install

Setup notes for Oracle
----------------------
If you will be using an Oracle database backend, it will take some setup to get
an installation of OCI8 working.

If you will be running the oracle database on another node, download Instant
Client from Oracle. The linux builds can be found at:

    https://www.oracle.com/database/technologies/instant-client/linux-x86-64-downloads.html

If you are not on Red Hat Enterprise Linux, you will need to install the Basic
Package and SDK Package by copying them to somewhere. I recommend putting them
in a directory in /opt/.

After you have your database or instant client configured, install the OCI8
extension for PHP. You will need a C compiler and possibly some other standard
build tools installed to complete the installation.

    $ sudo pecl install oci8

If you're using Instant Client, when prompted for ORACLE_HOME, give the
directory you unzipped Instant Client to as the directory like e.g.

    instantclient,/opt/intsantclient_19_5

Add

    extension=oci8.so

to php.ini to enable to newly built OCI8 extension. Then you will probably need
to restart your webserver.

In case the extension is installed with wrong permissions, go to your php
extensions directory. The following command might help you find that by
searching recursively:

    $ find -name oci8.so

Now change the permisisons on the file to match other known working extensions.
I had to change the mode on mine from 644 to 755.

You will need to add the instant client libraries to the LD\_LIBRARY\_PATH. Under
debian and apache2, this is configured in the `envvars` script. Again, `find`
will probably help you find  where this file is located. Bitnami users should
be aware that this configuration is global and they should modify
`/opt/bitnami/scripts/setenv.sh`.


Add these lines to the file to add Instant Client to the library path:

    LD_LIBRARY_PATH="/opt/instantclient_19_5${LD_LIBRARY_PATH:+:$LD_LIBRARY_PATH}"
    export LD_LIBRARY_PATH

To test your installation of OCI, open your browser and go to this application's
index.php. If you attempt to log in and you see the oci\_* functions are
undefined, you have no been successful. If you attempt to log in and you get a
large number of warnings about 'parameter 1 to be a resource, object/null
given',
you should go and configure the server settings in fbl\_common.php, inside the
function db_connect.

