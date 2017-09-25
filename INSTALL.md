Installation instructions on Pi Zero W
===

Install the latest Raspian os.

Open settings and ->

*  Change the user password.
*  Change the hostname and enable ssh (optional)

```
sudo apt-get update
sudo apt-get upgrade
```

Reboot the system.

Install apache and php:
```
sudo apt-get install apache2
sudo apt-get install php5 libapache2-mod-php5
```

Clone this repository into your web root (/var/www/html)

install node, for example using the tool at https://github.com/sdesalas/node-pi-zero:

```
wget -O - https://raw.githubusercontent.com/sdesalas/node-pi-zero/master/install-node-v6.11.3.sh | bash
```

Install bower globally:
```
npm install bower -g
```

Get dependencies with bower:
```
cd /var/www/html
bower install
```

Copy all files in `/var/www/html/data` that have `sample` in their filename to the same file name without the sample and then make sure the new files are writable by the web server.  ie:

```
cd /var/www/html/data
cp data.sample.txt data.txt
cp blank.sample.txt blank.txt
chmod 666 data.txt
chmod 666 blank.txt
```

For development
---

```
cd /var/www/html
npm install
```
