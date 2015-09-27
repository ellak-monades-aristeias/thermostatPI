CREATE DATABASE `thermostatpidb`;
USE `thermostatpidb`;

CREATE TABLE IF NOT EXISTS `measurements` (
  -- The time in which the measurment was taken.
  `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  -- The temperature measurement. 4 digits before the decimal point and 2 digit after.
  -- The inner sensor.
  `temperatureIn` DECIMAL(6,2) NOT NULL,
  -- The outer sensor.
  `temperatureOut` DECIMAL(6,2) NOT NULL,
  -- The status of the thermostat true=on, false=off
  `status` BOOLEAN NOT NULL,
  PRIMARY KEY(`time`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `thresholds` (
  -- The starting day (0-6), time (0-23), minute (0-60)
  `day` int NOT NULL,
  `hour` int NOT NULL,
  `minute` int NOT NULL,
  -- The lower and upper thresholds for the inner and outer sensor.
  `lowerThresholdIn` DECIMAL(6,2) NOT NULL,
  `upperThresholdIn` DECIMAL(6,2) NOT NULL,
  `lowerThresholdOut` DECIMAL(6,2) NOT NULL,
  `upperThresholdOut` DECIMAL(6,2) NOT NULL,
  PRIMARY KEY(`day`, `hour`, `minute`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `config` (
  -- The key of the property.
  `key` VARCHAR(255) NOT NULL,
  -- Tha value.
  `value` VARCHAR(255) NOT NULL,
  PRIMARY KEY(`key`)
) ENGINE=InnoDB;

INSERT INTO `config` (`key`, `value`) VALUES ('username', 'user');
INSERT INTO `config` (`key`, `value`) VALUES ('password', 'pass');
INSERT INTO `config` (`key`, `value`) VALUES ('maxHoursPerDay', '25');

