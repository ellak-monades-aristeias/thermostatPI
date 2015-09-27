#!/usr/bin/python
import time
import sys
import traceback
import warnings
import MySQLdb
from datetime import datetime
import Adafruit_DHT as DHT
import RPi.GPIO as GPIO

#Some settings.
mysqlPass = "raspberry"
temperatureLowerThreshold = 20.00
temperatureUpperThreshold = 22.00

#The GPIO name of the pins.
outputPin = 17
sensor1Pin = 4
sensor2Pin = 22

#If this change, the code of the php scripts must change.
timeBetweenMeasurments = 60

#Enable the output pin.
GPIO.setwarnings(False)
GPIO.setmode(GPIO.BCM)
GPIO.setup(outputPin, GPIO.OUT)

#Do not display warning of mysql.
warnings.filterwarnings("ignore", category = MySQLdb.Warning)

#Connect to mysql
db = MySQLdb.connect(host="localhost", user="root", passwd=mysqlPass, db="thermostatpidb")

#Run until ctrl^c is pressed
try:
  hasBeenAboveTemperatureUpperThreshold = False
  while True:
    # Read the temperature and the humidity.
    a = datetime.now()
    h1,t1 = DHT.read_retry(DHT.DHT22, sensor1Pin)
    h2,t2 = DHT.read_retry(DHT.DHT22, sensor1Pin)
    print 'Temp={0:1.1f}*C Humidity={1:0.1f}%'.format(t1,h1)
    print 'Temp={0:1.1f}*C Humidity={1:0.1f}%'.format(t2,h2)
    
    #If the current temperature is higher than the temperatureUpperThreshold, then power off.
    status = False
    if temperatureUpperThreshold < t1:
      status = False
      hasBeenAboveTemperatureUpperThreshold = True
    #If the current temperature is lower than the temperatureLowerThreshold, then power on.
    elif t1<temperatureLowerThreshold:
      status = True
      hasBeenAboveTemperatureUpperThreshold = False
    #The temperature is between the temperatureLowerThreshold and temperatureUpperThreshold.
    else:
      #It was hot, and now the temperature is falling. Wait until it is bellow temperatureLowerThreshold.
      if hasBeenAboveTemperatureUpperThreshold:
        status = False
      #The temperature was bellow temperatureLowerThreshold, now it is above temperatureLowerThreshold, but it has not yet reach temperatureUpperThreshold.
      else:
        status = True
    
    #t2 represents the temperature in the outer space, so if it is above t1, do not open the thermostat.
    if t1 < t2 :
      status = False
    
    #Open or close the thermostat.
    GPIO.output(outputPin, status)
    GPIO.output(outputPin,False)
    
    #Insert the measurments to the database.
    x = db.cursor()
    try:
      x.execute("""INSERT INTO `measurements` (`time`, `temperatureIn`, `temperatureOut`, `status`) VALUES (%s,%s,%s,%s)""",  
        (time.strftime('%Y-%m-%d %H:%M:%S'), t1, t2, status))
      db.commit()
    except:
      db.rollback()
      ex_type, ex, tb = sys.exc_info()
      traceback.print_tb(tb)
      print ex
    
    # Do not wait for the time that the above command consumed.
    b = datetime.now()
    c = b - a
    if timeBetweenMeasurments - c.total_seconds() > 0:
      time.sleep(timeBetweenMeasurments - c.total_seconds())
except KeyboardInterrupt:
  print "Exiting..."

#Close mysql connection
db.close()

