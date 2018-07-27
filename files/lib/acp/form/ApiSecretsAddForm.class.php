<?php
namespace wcf\acp\form;

use wcf\data\package\PackageCache;
use wcf\data\style\Style;
use wcf\form\AbstractForm;
use wcf\system\acl\ACLHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;
use wcf\system\style\StyleHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;
use wcf\util\JSON;
use wcf\util\StringUtil;
use wcf\util\CryptoUtil;

/**
 * Shows the board add form.
 * 
 * @author	Alexander Ebert
 * @copyright	2001-2018 WoltLab GmbH
 * @license	WoltLab License <http://www.woltlab.com/license-agreement.html>
 * @package	WoltLabSuite\Forum\Acp\Form
 */
class ApiSecretsAddForm extends AbstractForm {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wsc.acp.menu.link.wscApi.secrets.add';
	
	/**
	 * @inheritDoc
	 */
	public $neededPermissions = [];
	
	/**
	 * object type id
	 * @var	integer
	 */
	public $objectTypeID = 0;
	
	/**
	 * board type
	 * @var	integer
	 */
	public $boardType = 0;
	
	/**
	 * count user posts in this board
	 * @var	boolean
	 */
	public $countUserPosts = true;
	
	/**
	 * prune after x days
	 * @var	integer
	 */
	public $daysPrune = 0;
	
	/**
	 * board description
	 * @var	string
	 */
	public $description = '';
	
	/**
	 * use HTML in description
	 * @var	boolean
	 */
	public $descriptionUseHtml = false;
	
	/**
	 * enable marking as done
	 * @var	boolean
	 */
	public $enableMarkingAsDone = false;
	
	/**
	 * external url
	 * @var	string
	 */
	public $externalURL = '';
	
	/**
	 * board can be ignored
	 * @var	boolean
	 */
	public $ignorable = true;
	
	/**
	 * board is closed
	 * @var	boolean
	 */
	public $isClosed = false;
	
	/**
	 * board is invisible
	 * @var	boolean
	 */
	public $isInvisible = false;
	
	/**
	 * board is private
	 * @var boolean
	 */
	public $isPrivate = false;
	
	/**
	 * parent board id
	 * @var	integer
	 */
	public $parentID = 0;
	
	/**
	 * board tree position
	 * @var	integer
	 */
	public $position = 0;
	
	/**
	 * post sort order in threads
	 * @var	string
	 */
	public $postSortOrder = '';
	
	/**
	 * number of posts per thread page
	 * @var	integer
	 */
	public $postsPerPage = 0;
	
	/**
	 * board is searchable
	 * @var	boolean
	 */
	public $searchable = true;
	
	/**
	 * board is searchable for similar threads
	 * @var	boolean
	 */
	public $searchableForSimilarThreads = true;
	
	/**
	 * display sub boards in tree
	 * @var	boolean
	 */
	public $showSubBoards = true;
	
	/**
	 * order threads by column
	 * @var	string
	 */
	public $sortField = '';
	
	/**
	 * threads order
	 * @var	string
	 */
	public $sortOrder = '';
	
	/**
	 * threads per page
	 * @var	integer
	 */
	public $threadsPerPage = 0;
	
	/**
	 * board title
	 * @var	string
	 */
	public $title = '';
	
	/**
	 * list of valid prune values in days
	 * @var	integer[]
	 */
	public $availableDaysPrune = [1, 3, 7, 14, 30, 60, 100, 365, 1000];
	
	/**
	 * list of valid sort fields
	 * @var	string[]
	 */
	public $availableSortField = ['topic', 'username', 'time', 'views', 'replies', 'lastPostTime', 'cumulativeLikes'];
	
	/**
	 * list of available styles
	 * @var	Style[]
	 */
	public $availableStyles = [];
	
	/**
	 * board node list
	 * @var	RealtimeBoardNodeList
	 */
	public $boardNodeList;
	
	/**
	 * style id
	 * @var	integer
	 */
	public $styleID = 0;
	
	/**
	 * icon data
	 * @var string[][]
	 */
	public $iconData = [];
	
	/**
	 * default values for icons
	 * @var string[][]
	 */
	public $iconDataDefaultValues = [];
	
	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		parent::readParameters();
		
		/*I18nHandler::getInstance()->register('description');
		I18nHandler::getInstance()->register('title');
		
		$this->objectTypeID = ACLHandler::getInstance()->getObjectTypeID('com.woltlab.wbb.board');
		
		if (isset($_REQUEST['parentID'])) $this->parentID = intval($_REQUEST['parentID']);
		if ($this->parentID) {
			$board = new Board($this->parentID);
			if (!$board->boardID || $board->boardType == Board::TYPE_LINK) {
				throw new IllegalLinkException();
			}
		}
		
		// get available styles
		$this->availableStyles = StyleHandler::getInstance()->getStyles();
		
		// set icon data defaults
		foreach (Board::getDefaultIconTypes() as $type) {
			$this->iconDataDefaultValues[$type] = $this->iconData[$type] = [
				'icon' => Board::getDefaultIcon($type),
				'useColor' => false,
				'color' => 'rgba(44, 62, 80, 1)' // wcfContentText
			];
        }*/
	}
	
	/**
	 * @inheritDoc
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// set all boolean fields to false prior to input reading
		/*$this->countUserPosts = $this->descriptionUseHtml = $this->ignorable = $this->isClosed = false;
		$this->isInvisible = $this->searchable = $this->searchableForSimilarThreads = $this->showSubBoards = false;
		
		// read i18n values
		I18nHandler::getInstance()->readValues();
		
		// handle i18n plain input
		if (I18nHandler::getInstance()->isPlainValue('description')) $this->description = I18nHandler::getInstance()->getValue('description');
		if (I18nHandler::getInstance()->isPlainValue('title')) $this->title = I18nHandler::getInstance()->getValue('title');
		
		// handle default input
		if (isset($_POST['boardType'])) {
			$this->boardType = intval($_POST['boardType']);
			
			if ($this->boardType != Board::TYPE_CATEGORY && $this->boardType != Board::TYPE_LINK) {
				$this->boardType = Board::TYPE_BOARD;
			}
		}
		if (isset($_POST['countUserPosts'])) $this->countUserPosts = true;
		if (isset($_POST['daysPrune'])) $this->daysPrune = intval($_POST['daysPrune']);
		if (isset($_POST['descriptionUseHtml'])) $this->descriptionUseHtml = true;
		if (isset($_POST['enableMarkingAsDone']) && WBB_MODULE_THREAD_MARKING_AS_DONE) $this->enableMarkingAsDone = true;
		if (isset($_POST['externalURL'])) $this->externalURL = StringUtil::trim($_POST['externalURL']);
		if (isset($_POST['ignorable'])) $this->ignorable = true;
		if (isset($_POST['isClosed'])) $this->isClosed = true;
		if (isset($_POST['isInvisible'])) $this->isInvisible = true;
		if (isset($_POST['isPrivate'])) $this->isPrivate = true;
		if (!empty($_POST['position'])) $this->position = intval($_POST['position']);
		if (!empty($_POST['postSortOrder'])) {
			$this->postSortOrder = StringUtil::trim($_POST['postSortOrder']);
			if ($this->postSortOrder != 'DESC') {
				$this->postSortOrder = 'ASC';
			}
		}
		if (isset($_POST['postsPerPage'])) $this->postsPerPage = intval($_POST['postsPerPage']);
		if (isset($_POST['searchable'])) $this->searchable = true;
		if (isset($_POST['searchableForSimilarThreads'])) $this->searchableForSimilarThreads = true;
		if (isset($_POST['showSubBoards'])) $this->showSubBoards = true;
		if (isset($_POST['sortField'])) $this->sortField = StringUtil::trim($_POST['sortField']);
		if (!empty($_POST['sortOrder'])) {
			$this->sortOrder = StringUtil::trim($_POST['sortOrder']);
			if ($this->sortOrder != 'ASC') {
				$this->sortOrder = 'DESC';
			}
		}
		if (isset($_POST['threadsPerPage'])) $this->threadsPerPage = intval($_POST['threadsPerPage']);
		if (isset($_POST['styleID'])) $this->styleID = intval($_POST['styleID']);
		if (isset($_POST['iconData']) && is_array($_POST['iconData'])) {
			$icons = StyleHandler::getInstance()->getIcons();
			foreach ($_POST['iconData'] as $type => $data) {
				if (!in_array($type, Board::getDefaultIconTypes())) {
					continue;
				}
				
				if (isset($data['icon']) && in_array($data['icon'], $icons)) {
					$this->iconData[$type]['icon'] = $data['icon'];
				}
				if (isset($data['useColor'])) {
					$this->iconData[$type]['useColor'] = ($data['useColor'] == 1) ? 1 : 0;
				}
				if (isset($data['color']) && preg_match('~^rgba\(\d+, \d+, \d+, [01](?:\.\d+)?\)$~', $data['color'])) {
					$this->iconData[$type]['color'] = $data['color'];
				}
			}
        }*/
	}
	
	/**
	 * @inheritDoc
	 */
	public function validate() {
		parent::validate();
		
		// validate parent board id
		/*$this->validateParentID();
		
		// validate title
		if (!I18nHandler::getInstance()->validateValue('title')) {
			if (I18nHandler::getInstance()->isPlainValue('title')) {
				throw new UserInputException('title');
			}
			else {
				throw new UserInputException('title', 'multilingual');
			}
		}
		
		// validate description
		if (!I18nHandler::getInstance()->validateValue('description', false, true)) {
			throw new UserInputException('description');
		}
		
		// validate days prune
		if ($this->daysPrune && !in_array($this->daysPrune, $this->availableDaysPrune)) {
			throw new UserInputException('daysPrune', 'invalid');
		}
		
		// validate sort field
		if (!empty($this->sortField) && !in_array($this->sortField, $this->availableSortField)) {
			throw new UserInputException('sortField', 'invalid');
		}
		
		// validate external url
		if ($this->boardType == Board::TYPE_LINK && empty($this->externalURL)) {
			throw new UserInputException('externalURL');
		}
		
		// validate style id
		if ($this->styleID && !isset($this->availableStyles[$this->styleID])) {
			throw new UserInputException('styleID', 'invalid');
        }*/
	}
	
	/**
	 * Validates the given parent id.
	 */
	protected function validateParentID() {
		/*if ($this->parentID) {
			$board = new Board($this->parentID);
			if (!$board->boardID || $board->boardType == Board::TYPE_LINK) {
				throw new IllegalLinkException();
			}
        }*/
	}
	
	/**
	 * Returns the icon data in the format saved in the database.
	 * 
	 * @return	string[][]
	 */
	protected function getIconData() {
		/*$iconData = [];
		
		foreach ($this->iconData as $type => $data) {
			$typeData = [];
			if ($data['icon'] !== $this->iconDataDefaultValues[$type]['icon']) {
				$typeData['icon'] = $data['icon'];
			}
			
			if ($data['color'] !== $this->iconDataDefaultValues[$type]['color']) {
				$typeData['color'] = $data['color'];
			}
			
			if (!empty($typeData)) {
				if (!empty($typeData['color'])) {
					$typeData['useColor'] = (isset($data['useColor'])) ? $data['useColor'] : $this->iconDataDefaultValues[$type]['useColor'];
				}
				
				$iconData[$type] = $typeData;
			}
		}
		
		return $iconData;*/
	}
	
	/**
	 * @inheritDoc
	 */
	public function save() {
		parent::save();
		
		// create board
		/*$this->objectAction = new BoardAction([], 'create', ['data' => array_merge($this->additionalFields, [
			'boardType' => $this->boardType,
			'countUserPosts' => $this->countUserPosts ? 1 : 0,
			'daysPrune' => $this->daysPrune,
			'description' => $this->description,
			'descriptionUseHtml' => $this->descriptionUseHtml ? 1 : 0,
			'enableMarkingAsDone' => $this->enableMarkingAsDone ? 1 : 0,
			'externalURL' => $this->externalURL,
			'ignorable' => $this->ignorable ? 1 : 0,
			'isClosed' => $this->isClosed ? 1 : 0,
			'isInvisible' => $this->isInvisible ? 1 : 0,
			'isPrivate' => $this->isPrivate ? 1 : 0,
			'parentID' => $this->parentID ?: null,
			'position' => $this->position,
			'postSortOrder' => $this->postSortOrder,
			'postsPerPage' => $this->postsPerPage,
			'searchable' => $this->searchable ? 1 : 0,
			'searchableForSimilarThreads' => $this->searchableForSimilarThreads ? 1 : 0,
			'showSubBoards' => $this->showSubBoards ? 1 : 0,
			'sortField' => $this->sortField,
			'sortOrder' => $this->sortOrder,
			'threadsPerPage' => $this->threadsPerPage,
			'title' => $this->title,
			'styleID' => $this->styleID ?: null,
			'iconData' => JSON::encode($this->getIconData())
		])]);*/
		/** @var Board $board */
		/*$board = $this->objectAction->executeAction()['returnValues'];
		
		// save i18n values
		$this->saveI18nValue($board, 'description');
		$this->saveI18nValue($board, 'title');
		
		// save ACL
		ACLHandler::getInstance()->save($board->boardID, $this->objectTypeID);
		UserStorageHandler::getInstance()->resetAll('wbbBoardPermissions');
		$this->saved();
		
		// reset values
		$this->countUserPosts = $this->ignorable = $this->searchable = $this->searchableForSimilarThreads = $this->showSubBoards = true;
		$this->descriptionUseHtml = $this->isClosed = $this->isInvisible = $this->isPrivate = false;
		$this->boardType = $this->daysPrune = $this->parentID = $this->postsPerPage = $this->threadsPerPage = $this->styleID = 0;
		$this->description = $this->postSortOrder = $this->sortField = $this->sortOrder = $this->title = $this->position = '';
		
		// reset icon data
		$this->iconData = $this->iconDataDefaultValues;
		
		I18nHandler::getInstance()->reset();
		ACLHandler::getInstance()->disableAssignVariables();
		if (!empty($board->getIconData())) {
			StyleHandler::resetStylesheets(false);
		}
		
		// show success message
		WCF::getTPL()->assign('success', true);*/
	}
	
	/**
	 * Saves i18n values.
	 * 
	 * @param	Board		$board
	 * @param	string		$columnName
	 */
	public function saveI18nValue(Board $board, $columnName) {
		/*if (!I18nHandler::getInstance()->isPlainValue($columnName)) {
			I18nHandler::getInstance()->save($columnName, 'wbb.board.board'.$board->boardID.($columnName == 'description' ? '.description' : ''), 'wbb.board', PackageCache::getInstance()->getPackageID('com.woltlab.wbb'));
			
			// update description
			$boardEditor = new BoardEditor($board);
			$boardEditor->update([
				$columnName => 'wbb.board.board'.$board->boardID.($columnName == 'description' ? '.description' : '')
			]);
		}*/
	}
	
	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();
		/*
		$this->boardNodeList = new RealtimeBoardNodeList();
		$this->boardNodeList->readNodeTree();*/
	}
	
	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
        parent::assignVariables();
        

        WCF::getTPL()->assign([
            'action' => 'add',
            'success' => true,
            'secret' => bin2hex(CryptoUtil::randomBytes(16))
		]);
		/*
		ACLHandler::getInstance()->assignVariables($this->objectTypeID);
		I18nHandler::getInstance()->assignVariables();
		
		WCF::getTPL()->assign([
			'action' => 'add',
			'availableDaysPrune' => $this->availableDaysPrune,
			'availableSortField' => $this->availableSortField,
			'availableStyles' => $this->availableStyles,
			'boardNodeList' => $this->boardNodeList->getNodeList(),
			'boardType' => $this->boardType,
			'countUserPosts' => $this->countUserPosts,
			'daysPrune' => $this->daysPrune,
			'descriptionUseHtml' => $this->descriptionUseHtml,
			'enableMarkingAsDone' => $this->enableMarkingAsDone,
			'externalURL' => $this->externalURL,
			'ignorable' => $this->ignorable,
			'isClosed' => $this->isClosed,
			'isInvisible' => $this->isInvisible,
			'isPrivate' => $this->isPrivate,
			'objectTypeID' => $this->objectTypeID,
			'parentID' => $this->parentID,
			'position' => $this->position,
			'postSortOrder' => $this->postSortOrder,
			'postsPerPage' => $this->postsPerPage,
			'searchable' => $this->searchable,
			'searchableForSimilarThreads' => $this->searchableForSimilarThreads,
			'showSubBoards' => $this->showSubBoards,
			'sortField' => $this->sortField,
			'sortOrder' => $this->sortOrder,
			'threadsPerPage' => $this->threadsPerPage,
			'styleID' => $this->styleID,
			'iconData' => $this->iconData
		]);*/
	}
}
