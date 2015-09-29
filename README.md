# thermostatPI

ThermostatPi is a project that contains the code and the instructions in order to make a smart thermostat for the house. It uses two DHT22 sensors. The first measures the inner temperature of the house. The other measures the outer temperature of the house. If the outer is above the inner, then the thermostat is not activated. The temperature thresholds that the thermostat is activated are configured through a web interface. Moreover, the user can se the consumption in hours and set an upper limit per day.

# Hardware

What is needed:

* 1 * Raspberry pi
* 2 * [DHT22 sensors](https://www.adafruit.com/products/385)
* 1 * [relay](https://www.sparkfun.com/products/11042)
* 2 * 4.7K resistance
* 1 * usb wifi
* cables

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

# Installation

The installation instructions can be found in the doc folder: [doc/README.md](doc/README.md)

# License

See: [LICENSE](LICENSE)
