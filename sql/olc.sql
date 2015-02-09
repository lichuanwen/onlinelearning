-- ====================[ Initializing ]

SELECT "Dropping Tables ..." AS FEEDBACK;

DROP TABLE IF EXISTS `courses`;
DROP TABLE IF EXISTS `schools`;
DROP TABLE IF EXISTS `requisites`;
DROP TABLE IF EXISTS `lms_systems`;


SELECT "Creating Tables ..." AS FEEDBACK;

-- ==================== [ Table Definitions ]

CREATE TABLE schools (
`school_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
`School` VARCHAR(30) NOT NULL,
`Short` VARCHAR(4),
PRIMARY KEY(`school_id`)) ENGINE=INNODB;

CREATE TABLE lms_systems(
`lms_system_id` INT NOT NULL AUTO_INCREMENT,
`LMS` VARCHAR(25) NOT NULL DEFAULT 'Undefined',
PRIMARY KEY(`lms_system_id`)) ENGINE=InnoDB;

CREATE TABLE requisites (
`requisite_id` INT NOT NULL AUTO_INCREMENT,
`prereq` VARCHAR(2020) NOT NULL DEFAULT 'Undefined',
`availability` VARCHAR(1024) NOT NULL DEFAULT 'Undefined',
`textbook`  VARCHAR(1024) NOT NULL DEFAULT 'Undefined',
`sysreq`  VARCHAR(256) NOT NULL DEFAULT 'Undefined',
`credentials`  VARCHAR(256) NOT NULL DEFAULT 'Undefined',
`login`  VARCHAR(256) NOT NULL DEFAULT 'Undefined',
`lms_system_fk` INT NOT NULL,
  INDEX(`lms_system_fk`),
  FOREIGN KEY(`lms_system_fk`) REFERENCES `lms_systems`(`lms_system_id`)
  ON UPDATE CASCADE ON DELETE CASCADE,
  PRIMARY KEY(`requisite_id`)) ENGINE=InnoDB;

CREATE TABLE courses (
`course_id` INT NOT NULL AUTO_INCREMENT,
`CName` VARCHAR(4) NOT NULL,
`CNum` INT NOT NULL,
`CSec` VARCHAR(3) NOT NULL,
`Title` VARCHAR(225) DEFAULT "Unspecified",
`school_fk` SMALLINT UNSIGNED NOT NULL,
`lms_fk` INT NOT NULL,
INDEX (`school_fk`,`lms_fk`),
FOREIGN KEY(`school_fk`) REFERENCES `schools`(`school_id`)
ON UPDATE CASCADE ON DELETE CASCADE,
FOREIGN KEY(`lms_fk`) REFERENCES `lms_systems`(`lms_system_id`)
ON UPDATE CASCADE ON DELETE CASCADE,
PRIMARY KEY(`course_id`)) ENGINE = INNODB;


-- { PROCEDURES }

DELIMITER $$ 

DROP PROCEDURE IF EXISTS `spRequisites` $$
CREATE PROCEDURE `spRequisites` (intCourseID INT)
BEGIN
    SELECT `CName`,`CNum`,`Title`,`prereq`,`availability`,`textbook`,`sysreq`,`credentials`,`login` 
    FROM courses C, requisites R
    WHERE C.`lms_fk` = R.`lms_system_fk`
    AND C.`course_id` = intCourseID;
END $$

DELIMITER ;

SELECT "Pocessed Procedures ... " AS FEEDBACK;

-- { PROCEDURES ends }
--
-- { VIEWS }

CREATE OR REPLACE VIEW `vuCoursesInfo` AS
SELECT `course_id`,`CName`,`CNum`,`CSec`,`Title`,`School`,`Short`,`LMS`
FROM courses CO, schools SC, lms_systems LMS
WHERE CO.`school_fk` = SC.`school_id` 
AND CO.`lms_fk` = LMS.`lms_system_id`;

-- { VIEWS ends }
--



