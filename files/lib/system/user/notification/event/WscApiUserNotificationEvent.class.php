<?php
namespace wcf\system\user\notification\event;
/*
use wbb\system\cache\runtime\ThreadRuntimeCache;
use wbb\system\user\notification\object\PostUserNotificationObject;
*/
use wcf\system\email\Email;
use wcf\system\request\LinkHandler;
use wcf\system\user\notification\event\AbstractSharedUserNotificationEvent;
use wcf\system\user\notification\event\ITestableUserNotificationEvent;
use wcf\system\user\object\watch\UserObjectWatchHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;

/**
 */
class WscApiUserNotificationEvent extends AbstractSharedUserNotificationEvent {

	/**
	 * @inheritDoc
	 */
	protected $stackable = true;

	/**
	 * @inheritDoc
	 */
	protected function prepare() {
		// ThreadRuntimeCache::getInstance()->cacheObjectID($this->getUserNotificationObject()->notificationID);
	}

	/**
	 * @inheritDoc
	 */
	public function getTitle() {
		return $this->getUserNotificationObject()->title;
	}

	/**
	 * @inheritDoc
	 */
	public function getMessage() {
		return $this->getUserNotificationObject()->message;
	}

	/** @noinspection PhpMissingParentCallCommonInspection */
	/**
	 * @inheritDoc
	 */
	public function getEmailMessage($notificationType = 'instant') {
		return $this->getUserNotificationObject()->message;
	}

	/**
	 * @inheritDoc
	 * @since	5.0
	 */
	public function getEmailTitle() {
		return $this->getUserNotificationObject()->title;
	}

	/** @noinspection PhpMissingParentCallCommonInspection */
	/**
	 * @inheritDoc
	 */
	public function getEventHash() {
		return sha1($this->eventID . '-' . $this->getUserNotificationObject()->notificationID);
	}

	/**
	 * @inheritDoc
	 */
	public function getLink() {
		return $this->getUserNotificationObject()->url;
	}

	/** @noinspection PhpMissingParentCallCommonInspection */
	/**
	 * @inheritDoc
	 */
	public function checkAccess() {
		return true;
	}
}
