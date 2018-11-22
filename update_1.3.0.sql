DROP TABLE IF EXISTS wcf1_api_notification;
CREATE TABLE wcf1_api_notification (
	notificationID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	title varchar(255) NOT NULL,
	message MEDIUMTEXT NOT NULL,
	url varchar(255) NOT NULL,
	time INT(10) NOT NULL DEFAULT 0,
)