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
  -- The starting and ending day (0-6), time (0-23), minute (0-60)
  `startDay`    INT NOT NULL,
  `startHour`   INT NOT NULL,
  `startMinute` INT NOT NULL,
  `endDay`    INT NOT NULL,
  `endHour`   INT NOT NULL,
  `endMinute` INT NOT NULL,
  -- The lower and upper thresholds for the inner and outer sensor.
  `lowerThresholdIn` DECIMAL(6,2) NOT NULL,
  `upperThresholdIn` DECIMAL(6,2) NOT NULL,
  `lowerThresholdOut` DECIMAL(6,2) NOT NULL,
  `upperThresholdOut` DECIMAL(6,2) NOT NULL
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

DELIMITER $$
DROP PROCEDURE IF EXISTS `check_thresholds_before_insert`$$
CREATE PROCEDURE `check_thresholds_before_insert`(
IN `startDayIn` INT, IN `startHourIn` INT, IN `startMinuteIn` INT,
IN `endDayIn` INT,  IN `endHourIn` INT,  IN `endMinuteIn` INT
)
READS SQL DATA
BEGIN
  DECLARE `num_rows` INT;
  
  if `endDayIn`*24*60+`endHourIn`*60+`endMinuteIn` <= `startDayIn`*24*60+`startHourIn`*60+`startMinuteIn` THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = "ERROR: Start time must be after end time.";
  END IF;
  
  if `startDayIn`<0 || `startDayIn`>6 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = "ERROR: Start day must be an integer in the interval [0,6].";
  END IF;
  
  if `endDayIn`<0 || `endDayIn`>6 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = "ERROR: End day must be an integer in the interval [0,6].";
  END IF;
  
  if `startHourIn`<0 || `startHourIn`>23 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = "ERROR: Start hour must be an integer in the interval [0,23].";
  END IF;
  
  if `endHourIn`<0 || `endHourIn`>23 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = "ERROR: End hour must be an integer in the interval [0,23].";
  END IF;
  
  if `startMinuteIn`<0 || `startMinuteIn`>59 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = "ERROR: Start minute must be an integer in the interval [0,59].";
  END IF;
  
  if `endMinuteIn`<0 || `endMinuteIn`>59 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = "ERROR: End minute must be an integer in the interval [0,59].";
  END IF;
  
  SELECT COUNT(*) INTO `num_rows` FROM `thresholds` WHERE 
    -- Case startTime <= startInTime < endTime
    (`startDay`*24*60+`startHour`*60+`startMinute` <= `startDayIn`*24*60+`startHourIn`*60+`startMinuteIn`
    AND `startDayIn`*24*60+`startHourIn`*60+`startMinuteIn` <= `endDay`*24*60+`endHour`*60+`endMinute`)
    -- Case startTime <= endInTime < endTime
    OR (`startDay`*24*60+`startHour`*60+`startMinute` <= `endDayIn`*24*60+`endHourIn`*60+`endMinuteIn`
    AND `endDayIn`*24*60+`endHourIn`*60+`endMinuteIn` <= `endDay`*24*60+`endHour`*60+`endMinute`)
    -- Case startInTime <= startTime < endInTime
    OR (`startDayIn`*24*60+`startHourIn`*60+`startMinuteIn` <= `startDay`*24*60+`startHour`*60+`startMinute`
    AND `startDay`*24*60+`startHour`*60+`startMinute` <= `endDayIn`*24*60+`endHourIn`*60+`endMinuteIn`)
    -- Case startInTime <= endTime < endInTime
    OR (`startDayIn`*24*60+`startHourIn`*60+`startMinuteIn` <= `endDay`*24*60+`endHour`*60+`endMinute`
    AND `endDay`*24*60+`endHour`*60+`endMinute` <= `endDayIn`*24*60+`endHourIn`*60+`endMinuteIn`);

  IF `num_rows` > 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = "ERROR: Date intervals can not overlap.";
  END IF;
END$$
DELIMITER ;

DELIMITER $$
DROP TRIGGER IF EXISTS `before_thresholds_insert`$$
CREATE TRIGGER `before_thresholds_insert`
  BEFORE INSERT ON `thresholds`
  FOR EACH ROW
BEGIN
  CALL `check_thresholds_before_insert`(
NEW.`startDay`, NEW.`startHour`, NEW.`startMinute`,
NEW.`endDay`, NEW.`endHour`, NEW.`endMinute`);
END$$
DELIMITER ;

DELIMITER $$
DROP TRIGGER IF EXISTS `before_thresholds_update`$$
CREATE TRIGGER `before_thresholds_update`
  BEFORE UPDATE ON `thresholds`
  FOR EACH ROW
BEGIN
  CALL `check_thresholds_before_insert`(
NEW.`startDay`, NEW.`startHour`, NEW.`startMinute`,
NEW.`endDay`, NEW.`endHour`, NEW.`endMinute`);
END$$
DELIMITER ;

