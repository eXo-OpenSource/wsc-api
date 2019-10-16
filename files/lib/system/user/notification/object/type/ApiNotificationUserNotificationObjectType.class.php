<?php
namespace wcf\system\user\notification\object\type;
use wcf\data\ApiNotification;
use wcf\data\ApiNotificationList;
use wcf\system\user\notification\object\ApiNotificationUserNotificationObject;
use wcf\system\user\notification\object\type\AbstractUserNotificationObjectType;

class ApiNotificationUserNotificationObjectType extends AbstractUserNotificationObjectType {
	/**
	 * @inheritDoc
	 */
	protected static $decoratorClassName = ApiNotificationUserNotificationObject::class;

	/**
	 * @inheritDoc
	 */
	protected static $objectClassName = ApiNotification::class;

	/**
	 * @inheritDoc
	 */
	protected static $objectListClassName = ApiNotificationList::class;
}
