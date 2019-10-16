<?php
namespace wcf\data;

use wcf\system\cache\CacheHandler;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\language\LanguageFactory;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\ApiNotificationEditor;

class ApiNotificationAction extends AbstractDatabaseObjectAction  {
	/**
	 * @inheritDoc
	 */
	public $className = ApiNotificationEditor::class;

	/**
	 * @inheritDoc
	 */
	protected $permissionsDelete = [];

	/**
	 * @inheritDoc
	 */
	protected $requireACP = [];
}
