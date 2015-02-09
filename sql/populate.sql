-- POPULATE DATABASE W/TEST DATA

SELECT "Populating Tables ..." AS FEEDBACK;

INSERT INTO lms_systems (`LMS`)VALUES ("Blackboard");
INSERT INTO lms_systems (`LMS`)VALUES ("OntarioLearn");

INSERT INTO schools (`School`,`Short`)VALUES ("Business School","BUS");
INSERT INTO schools (`School`,`Short`)VALUES ("Liberal Arts & Sciences","LAS");

INSERT INTO `courses` (`CName`,`CNum`,`CSec`,`Title`,`school_fk`,`lms_fk`) 
VALUES ("BMAT","220","90","Mathematics of Finance",2,1);

INSERT INTO `courses` (`CName`,`CNum`,`CSec`,`Title`,`school_fk`,`lms_fk`) 
VALUES ("COMM.","016","99","Business Writing",1,1);

INSERT INTO requisites(`prereq`,`availability`,`textbook`,`sysreq`,`credentials`,`login`,`lms_system_fk`) 
VALUES ("Prerequisites are listed in the course description found in the Course Availability link below. 
It is the students' responsibility to meet the prerequisite requirements prior to registering in an online course. 
Students who register without meeting the prerequisites will NOT be given access to their online courses.",
"This course has a set start and end date. Please click on the link below to view a course description and a list of availability dates: <a href=\"http://parttimestudents.humber.ca\" target=\"_blank\">http://parttimestudents.humber.ca</a>",
"To order textbook(s) online, please visit Humber's <a href=\"http://www.humber.ca/content/bookstore-computer-shop\" target=\"_blank\">Online Bookstore</a> or call: 416-675-5066",
"Visit the SYSTEM REQUIREMENTS page to prepare your computer for online learning.",
"Your User Name will be your HCNet ID.<br />
If you do NOT know the password for your HCNet ID then Click Here for more information.",
"<a href=\"http://mycourses.humber.ca\" target=\"_blank\">Login to BlackBoard</a> once you've determined your HCNet ID (user name) and password.",1);

-- No content beyond this point
SELECT "All done ..." as FEEDBACK;
