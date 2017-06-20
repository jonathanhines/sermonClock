Sermon Clock
============

Displays a simple, configurable countdown timer.

Requirements
------------

1.  a web server capable of running php
2.  bower

Installation
------------

Clone the repository into your web root and issue `bower install`

Then copy the sample files in the data directory to remove the 'sample' from their
file names and make sure they are writable by the web server.

There is a sample .htaccess file in that folder that will give you some ideas on
how you can make the admin page password protected if you are using an apache2
web server with override enabled.

Operation
---------

The main display will appear at any domain connected to your web root.

This control interface for the application can be found through a web browser at
[your web root domain]/set.
