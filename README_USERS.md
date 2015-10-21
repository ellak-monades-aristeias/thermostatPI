# Hardware

What is needed:

* 1 * Raspberry pi
* 2 * [DHT22 sensors](https://www.adafruit.com/products/385)
* 1 * [relay](https://www.sparkfun.com/products/11042)
* 2 * 4.7K resistance
* 1 * usb wifi
* cables

# Connections

Connect the temperature sensors and the relay as show in the [image](doc/schematic.png).
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

Inside the mysql shell, copy and paste the code form the file [create_tables.sql](doc/create_tables.sql).
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

In order to start the script on boot time, add the following linei to `/etc/rc.local`:

    python /opt/thermostatPi/thermostatPi.py 2>&1 > /var/log/traficLightServer.log &

Copy the files that are responsible for the web interface:

    cp src/var/www/* /var/www/

# Setup an adhoc wifi access point

Follow the instructions [CreateAccessPoint.md](doc/CreateAccessPoint.md)

# Configuration through web interface.

Assuming that you have connect to the wifi of the raspberry, then the ip of the raspberry will be `10.0.0.254`.

Open a browser and go to the address: `10.0.0.254/thermostatConfiguration.php`. The default username is `user` and the password is `pass`.

The web page offers the following options:

*  Change the password.
*  Change the maximum consumption (per day, per week or per month).
*  Change the thresholds that the thermostat will be activated.
*  View total consumption for the last day, week and month or a graph with the temperatures and the consumption through time.

# More on thresholds

The thermostat has two threshold for the inner sensor, which will called lower and upper threshold.
* When the temperature is below the lower threshold, it is activated.
* When the temperature is above the upper threshold, it is deactivated.
* When the temperature is between the lower and upper thresholds:
*   If the temperature before entering the interval [lower, upper] was bellow the lower threshold, then the temperature is activated. This is the cas, when the temperature was below the lower threshold, the thermostat was activated, the temperature increases but it has not yet reach the upper threshold.
*   Otherwise, it is deactivated. This is the case when the temperature was above the upper threshold, it started decriasing, but it has not yet reached the lower threshold.

Each threshold can be se different for different times of week. The week is considert to start on day 0 (Monday) and end on day 6 (sunday). The hours have values between 0 and 23. The minutes have values between 0 and 59.

Some other check that affect the thermostat activation are:
* If the sensor that is placed in the outer environment gives temperature measurements that are above the inhouse sensor, then it is deactivated.
* If the total consumtion for the last day, week or month exeeds the maximum comsumption for that period, it is deactivated.

