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
    #Keep the time.
    a = datetime.now()
    
    # Get the threshold from the database.
    temperatureLowerThreshold = None
    temperatureUpperThreshold = None
    cursor = db.cursor()
    try:
      now = datetime.today()
      day = now.weekday()
      hour = now.hour
      minute = now.minute
      print("Day=%d, hour=%d, minute=%d" % (day, hour, minute))
      myTimestamp = day*24*60 + hour*60 + minute;
      query = """SELECT `lowerThresholdIn`, `upperThresholdIn` FROM `thresholds` WHERE `startDay`*24*60+`startHour`*60+`startMinute` <= %s AND %s <= `endDay`*24*60+`endHour`*60+`endMinute`""";
      cursor.execute(query, (myTimestamp, myTimestamp))
      for (temperatureLowerThreshold, temperatureUpperThreshold) in cursor:
        print("Found thresholds: lower=%.1f, upper=%.1f" % (temperatureLowerThreshold, temperatureUpperThreshold))
    except:
      db.rollback()
      ex_type, ex, tb = sys.exc_info()
      traceback.print_tb(tb)
      print(ex)
    cursor.close()
    
    # Get the limit
    period         = None
    maxConsumption = None
    cursor = db.cursor()
    try:
      query = """SELECT `c1`.`value` AS `period`, `c2`.`value` AS `maxConsumption` FROM `config` AS `c1`, `config` AS `c2` WHERE `c1`.`key`='period' AND `c2`.`key`='maxConsumption'""";
      cursor.execute(query)
      for (period, maxConsumption) in cursor:
        maxConsumption = float(maxConsumption)
        print("Period: %s, max consumption=%.1f" % (period, maxConsumption))
    except:
      db.rollback()
      ex_type, ex, tb = sys.exc_info()
      traceback.print_tb(tb)
      print(ex)
    cursor.close()
    
    # Get the limit
    comsumptionInPeriod = None
    cursor = db.cursor()
    try:
      query = "";
      if period == "day":
        query = """SELECT count(*) as `comsumptionInPeriod`  FROM `measurements` WHERE `time` >= DATE_ADD(CURDATE(), INTERVAL -1 DAY) AND `status`=1""";
      elif period == "week":
        query = """SELECT count(*) as `comsumptionInPeriod`  FROM `measurements` WHERE `time` >= DATE_ADD(CURDATE(), INTERVAL -1 WEEK) AND `status`=1""";
      elif period == "month":
        query = """SELECT count(*) as `comsumptionInPeriod`  FROM `measurements` WHERE `time` >= DATE_ADD(CURDATE(), INTERVAL -1 MONTH) AND `status`=1""";
      else:
        print("Period: %s is not supported" % period)
      cursor.execute(query)
      for (comsumptionInPeriod) in cursor:
        #Convert it in hours.
        comsumptionInPeriod = comsumptionInPeriod[0] / 60.0
        print("Consumption in period=%.1f" % (comsumptionInPeriod))
    except:
      db.rollback()
      ex_type, ex, tb = sys.exc_info()
      traceback.print_tb(tb)
      print(ex)  
    cursor.close()
    
    # Read the temperature and the humidity.
    h1,t1 = DHT.read_retry(DHT.DHT22, sensor1Pin)
    h2,t2 = DHT.read_retry(DHT.DHT22, sensor1Pin)
    print("In:  Temp=%.1f*C Humidity=%.1f%%" % (t1,h1))
    print("Out: Temp=%.1f*C Humidity=%.1f%%" % (t2,h2))
   
    #If the current temperature is higher than the temperatureUpperThreshold, then power off.
    status = False
    if temperatureLowerThreshold is not None and temperatureUpperThreshold is not None:
      if temperatureUpperThreshold < t1:
        status = False
        hasBeenAboveTemperatureUpperThreshold = True
        print("Temperature above upper limit. Status=Off.")
      #If the current temperature is lower than the temperatureLowerThreshold, then power on.
      elif t1<temperatureLowerThreshold:
        status = True
        hasBeenAboveTemperatureUpperThreshold = False
        print("Temperature below lower limit. Status=On.")
      #The temperature is between the temperatureLowerThreshold and temperatureUpperThreshold.
      else:
        #It was hot, and now the temperature is falling. Wait until it is bellow temperatureLowerThreshold.
        if hasBeenAboveTemperatureUpperThreshold:
          status = False
          print("Temperature between limits, but has been above upper limit. Status=Off.")
        #The temperature was bellow temperatureLowerThreshold, now it is above temperatureLowerThreshold, but it has not yet reach temperatureUpperThreshold.
        else:
          status = True
          print("Temperature between limits, but has been below lower limit. Status = On.")
    
      #t2 represents the temperature in the outer space, so if it is above t1, do not open the thermostat.
      #Dht22 have 2-5% accuracy error, so for measurements around 20oC the maximum error is 0.05*20=1oC. We have two sensors, so the combined error can be up to 2oC.
      if t1 < t2 - 2 :
        status = False
        print("Inner temperature is below outer temperature. Status=Off")
      if comsumptionInPeriod is not None and maxConsumption is not None and maxConsumption<=comsumptionInPeriod:
        status = False
        print("Max consumption has been reached. Status=Off")
    else:
      print("No limits has been set. Status=Off.")
    
    #Open or close the thermostat.
    GPIO.output(outputPin, status)
    
    #Insert the measurments to the database.
    cursor = db.cursor()
    try:
      query = """INSERT INTO `measurements` (`time`, `temperatureIn`, `temperatureOut`, `status`) VALUES (%s,%s,%s,%s)"""
      cursor.execute(query, (time.strftime('%Y-%m-%d %H:%M:%S'), t1, t2, status))
      db.commit()
    except:
      db.rollback()
      ex_type, ex, tb = sys.exc_info()
      traceback.print_tb(tb)
      print(ex)
    
    # Do not wait for the time that the above command consumed.
    b = datetime.now()
    c = b - a
    if timeBetweenMeasurments - c.total_seconds() > 0:
      time.sleep(timeBetweenMeasurments - c.total_seconds())
except KeyboardInterrupt:
  print("Exiting...")

#Close mysql connection
db.close()

