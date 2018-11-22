ALTER TABLE wcf1_user ADD wscApiId INT(10) NOT NULL DEFAULT 0;

DROP TABLE IF EXISTS wcf1_api_secret;
CREATE TABLE wcf1_api_secret (
  secretID int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  secretKey varchar(255) NULL,
  secretDescription varchar(255) NULL
);

DROP TABLE IF EXISTS wcf1_acl_option_to_secret;
CREATE TABLE wcf1_acl_option_to_secret (
	optionID INT(10) NOT NULL,
	objectID INT(10) NOT NULL,
	optionValue TINYINT(1) NOT NULL DEFAULT 0,
	UNIQUE KEY secretKey (objectID, optionID)
);

DROP TABLE IF EXISTS wcf1_api_notification;
CREATE TABLE wcf1_api_notification (
	notificationID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	title varchar(255) NOT NULL,
	message MEDIUMTEXT NOT NULL,
	url varchar(255) NOT NULL,
	time INT(10) NOT NULL DEFAULT 0,
)