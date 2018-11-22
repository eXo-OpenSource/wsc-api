<?php
namespace wcf\system\user\notification\object;
use wcf\data\ApiNotification;
use wcf\data\DatabaseObjectDecorator;
use wcf\system\user\notification\object\IUserNotificationObject;

class ApiNotificationUserNotificationObject extends DatabaseObjectDecorator implements IUserNotificationObject {
	/**
	 * @inheritDoc
	 */
	protected static $baseClass = ApiNotification::class;
	
	/**
	 * @inheritDoc
	 */
	public function getTitle() {
		return $this->getDecoratedObject()->title;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getURL() {
		return $this->getDecoratedObject()->url;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getAuthorID() {
		return null;
	}
}
