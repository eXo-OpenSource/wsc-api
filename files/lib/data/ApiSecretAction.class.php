<?php
namespace wcf\data;

use wcf\system\cache\CacheHandler;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\language\LanguageFactory;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\ApiSecretEditor;

class ApiSecretAction extends AbstractDatabaseObjectAction  {
	/**
	 * @inheritDoc
	 */
	public $className = ApiSecretEditor::class;

	/**
	 * @inheritDoc
	 */
	protected $permissionsDelete = ['admin.wscApi.canManageSecret'];

	/**
	 * @inheritDoc
	 */
	protected $requireACP = ['delete'];
}
