# thermostatPI

ThermostatPi is a project that contains the code and the instructions in order to make a smart thermostat for the house.
It uses two DHT22 sensors.
The first measures the inner temperature of the house.
The other measures the outer temperature of the house.
If the outer is above the inner, then the thermostat is not activated.
The temperature thresholds that the thermostat is activated are configured through a web interface
 Moreover, the user can se the consumption in hours and set an upper limit per day.

# Hardware

What is needed:

* 1 * Raspberry pi
* 2 * [DHT22 sensors](https://www.adafruit.com/products/385)
* 1 * [relay](https://www.sparkfun.com/products/11042)
* 2 * 4.7K resistance
* 1 * usb wifi
* cables

# Installation

The installation instructions can be found in the doc folder: [doc/README.md](doc/README.md)

# License

See: [LICENSE](LICENSE)
