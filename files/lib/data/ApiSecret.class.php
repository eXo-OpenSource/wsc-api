<?php

namespace wcf\data;

use wcf\data\user\object\watch\UserObjectWatch;
use wcf\data\user\UserProfile;
use wcf\data\DatabaseObject;

/**
 * 
 * @author
 * @copyright
 * @license
 * @package
 * 
 * @property-read	integer		$secretID                   unique id of the secret
 * @property-read	string		$secretKey				    api key
 * @property-read	string		$secretDescription			description of the secret
 */
class ApiSecret extends DatabaseObject {
}
