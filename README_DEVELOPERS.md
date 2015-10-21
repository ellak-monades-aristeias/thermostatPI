# Software

The project uses the following software:

* Python
* Apache
* Mysql
* Php

# Architecture

The main program of the thermostat is a python script that reads the temperature measurements (every one minute).
After reading these measurements, it decides if it must be activated or deactivated.
The threshold that are used are read from the database.
Moreover, the temperature along with the status (on-off) is stored in the database.

The configuration of the thermostat is done through a web interface that is writen in php.
The web interface provides also the history of the temperature and the operation hours.

# Instructions

In order to download the source code, run:

    git clone https://github.com/ellak-monades-aristeias/thermostatPI.git

Instructions on the code parts are given in the [wiki page](https://github.com/ellak-monades-aristeias/thermostatPI/wiki).
