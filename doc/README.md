# Software

The project uses the following software:

* Python
* Apache
* Mysql
* Php

# Architecture

The main program of the thermostat is a python script that reads the temperature measurements (every one minute).
After reading these measurments, it decides if it must be activated or deactivated.
The threshold that are used are read from the database.
Moreover, the temparature along with the status (on-off) is stored in the database.

The configuration of the thermostat is done through a web interface that is writen in php.
The web interfase provides also the history of the temperature and the operation hours.

# Connections

Connect the temperature sensors and the relay as show in the [image](schematic.png).
The sensor that is connected to the GPIO4 is the internal sensor,
 so it must be plased inside the room.
The sensor that is connected to the GPIO22 is the external sensor,
 so it must be plased outside of the room, for example in the balcony.
The inputs of the relay must be connectected to the raspberry pi.
The outputs, must be connected to the cables of the heating system. 
As this involves high voltage electric power, please contact an electrician.

# Update

First update the software of the raspberry pi:

    sudo apt-get update
    sudo apt-get -y upgrade
    sudo reboot

# Install DHT22 software

In order to be able to read the measurements from the sensor, 
the Adafruit_Python_DHT package must be installed, as described below:

    sudo apt-get -y install build-essential python-dev
    git clone https://github.com/adafruit/Adafruit_Python_DHT.git
    cd Adafruit_Python_DHT
    sudo python setup.py install
    cd ..
    rm -rf Adafruit_Python_DHT
    sudo reboot

# Install mysql

The measurements and the configuration is installed in a mysql database.
First, install the mysql package:

    sudo apt-get -y install mysql-server mysql-client python-mysqldb


After the installation run:

    mysql -u root -p

Inside the mysql shell, copy and paste the code form the file [create_tables.sql](create_tables.sql).
This will create the database.

# Install php and apache

The software is configure through a web interface that is based on php and apache.
So we must install these packages:

    sudo apt-get -y install apache2 libapache2-mod-auth-mysql php5-cli php5-mysql php5 libapache2-mod-php5
    sudo a2enmod auth_mysql
    sudo service apache2 restart

# Files configuration

Copy the file `src/opt/thermostatPi/thermostatPi.py` to the file: `/opt/thermostatPi/thermostatPi.py`:

    mkdir -p /opt/thermostatPi/
    cp src/opt/thermostatPi/thermostatPi.py /opt/thermostatPi/thermostatPi.py

In order to start the script on boot time, add the following linei to `/etc/rc.local:

    python /opt/thermostatPi/thermostatPi.py 2>&1 > /var/log/traficLightServer.log &

Copy the files that are responsible for the web interface:

    cp src/var/www/* /var/www/

# Setup an adhoc wifi access point

Follow the instructions [CreateAccessPoint.md](CreateAccessPoint.md)

# Configuration through web interface.

Assuming that you have connect to the wifi of the raspberry, then the ip of the raspberry will be `10.0.0.254`.

Open a browser and go to the addres: `10.0.0.254/thermostatConfiguration.php`. The default username is `user` and the password is `pass`.


