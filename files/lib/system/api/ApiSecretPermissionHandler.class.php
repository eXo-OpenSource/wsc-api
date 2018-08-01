<?php

namespace wcf\system\api;

use wcf\system\SingletonFactory;
use wcf\system\WCF;
use wcf\data\ApiSecret;
use wcf\system\acl\ACLHandler;
use wcf\util\StringUtil;
use wcf\data\acl\option\ACLOption;
use wcf\data\acl\option\ACLOptionList;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\data\acl\option\category\ACLOptionCategory;
use wcf\data\acl\option\category\ACLOptionCategoryList;

/**
 * @author
 * @copyright
 * @license
 * @package
 */
class ApiSecretPermissionHandler extends SingletonFactory {
	/**
	 * list of permissions
	 * @var	array
	 */
	protected $secretPermissions = [];
	
	/**
	 * @inheritDoc
	 */
	protected function init() {
		// $this->secretPermissions = BoardPermissionCache::getInstance()->getPermissions(WCF::getUser());
	}
	
	/**
	 * Returns the board permission with the given name for the board with the
	 * given id.
	 * 
	 * @param	integer		$secretID
	 * @param	string		$permission
	 * @return	boolean
	 */
	public function getPermission($secretID, $permission) {
		$objectTypeID = ACLHandler::getInstance()->getObjectTypeID('at.megathorx.wsc_api.apiSecret');
		$categoryName = 'api.' . explode('.', $permission)[0];
		$optionName = explode('.', $permission)[1];

		$sql = "SELECT  *
				FROM    wcf".WCF_N."_acl_option
				WHERE   objectTypeID = ? AND
				        categoryName = ? AND
				        optionName = ? ";

		$statement = \wcf\system\WCF::getDB()->prepareStatement($sql, 1);
		$statement->execute([$objectTypeID, $categoryName, $optionName]);
		$option = $statement->fetchSingleRow();

		if (!$option) { return false; }

		$sql = "SELECT  *
				FROM    wcf".WCF_N."_acl_option_to_secret
				WHERE   optionID = ? AND
				        objectID = ?";

		$statement = \wcf\system\WCF::getDB()->prepareStatement($sql, 1);
		$statement->execute([$option['optionID'], $secretID]);
		$permission = $statement->fetchSingleRow();

		if (!$permission || $permission['optionValue'] === 0) { return false; }
		return true;
	}

		/**
	 * Returns the permissions for given user.
	 * 
	 * @param	ApiSecret	$apiSecret
	 */
	/*public function getPermissions() {
		$sql = "SELECT	acl_option.optionName AS permission, acl_option.categoryName
				FROM	wcf".WCF_N."_acl_option acl_option
				WHERE	acl_option.objectTypeID = ?";

		$statement = WCF::getDB()->prepareStatement($sql);

		$statement->execute([
			ACLHandler::getInstance()->getObjectTypeID('at.megathorx.wsc_api.apiSecret')
		]);
		
		
		while ($row = $statement->fetchArray()) {
			var_dump($row);
			echo "<br>";
		}
	}*/
	
	/**
	 * Returns a list of permissions by object type id.
	 * 
	 * @param	integer		$objectTypeID
	 * @param	array		$objectIDs
	 * @param	string		$categoryName
	 * @param	boolean		$settingsView
	 * @return	array
	 */
	public function getPermissions($objectTypeID, array $objectIDs, $categoryName = '', $settingsView = false) {
		$optionList = $this->getOptions($objectTypeID, $categoryName);
		
		$data = [
			'options' => $optionList,
			'secret' => []
		];
		
		if (!empty($objectIDs)) {
			$this->getValues($optionList, $objectIDs, $data, $settingsView);
		}
		
		// use alternative data structure for settings
		if ($settingsView) {
			$objectType = ObjectTypeCache::getInstance()->getObjectType($objectTypeID);
			
			$data['options'] = [];
			$data['categories'] = [];
			
			if (count($optionList)) {
				$categoryNames = [];
				foreach ($optionList as $option) {
					$data['options'][$option->optionID] = [
						'categoryName' => $option->categoryName,
						'label' => WCF::getLanguage()->getDynamicVariable('wcf.acl.option.'.$objectType->objectType.'.'.$option->optionName),
						'optionName' => $option->optionName
					];
					
					if (!in_array($option->categoryName, $categoryNames)) {
						$categoryNames[] = $option->categoryName;
					}
				}
				
				// load categories
				$categoryList = new ACLOptionCategoryList();
				$categoryList->getConditionBuilder()->add("acl_option_category.categoryName IN (?)", [$categoryNames]);
				$categoryList->getConditionBuilder()->add("acl_option_category.objectTypeID = ?", [$objectTypeID]);
				$categoryList->readObjects();
				
				foreach ($categoryList as $category) {
					$data['categories'][$category->categoryName] = WCF::getLanguage()->get('wcf.acl.option.category.'.$objectType->objectType.'.'.$category->categoryName);
				}
			}
		}
		
		return $data;
	}
	
	/**
	 * Fetches ACL option values by type.
	 * 
	 * @param	ACLOptionList	$optionList
	 * @param	string		$type
	 * @param	array		$objectIDs
	 * @param	array		$data
	 * @param	boolean		$settingsView
	 */
	protected function getValues(ACLOptionList $optionList, array $objectIDs, array &$data, $settingsView) {
		$data['option'] = [];
		$optionsIDs = [];
		foreach ($optionList as $option) {
			$optionsIDs[] = $option->optionID;
		}
		
		// category matched no options
		if (empty($optionsIDs)) {
			return;
		}

		$conditions = new PreparedStatementConditionBuilder();
		$conditions->add("optionID IN (?)", [$optionsIDs]);
		$conditions->add("objectID IN (?)", [$objectIDs]);
		$sql = "SELECT	*
				FROM	wcf".WCF_N."_acl_option_to_secret
				".$conditions;

		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($conditions->getParameters());

		while ($row = $statement->fetchArray()) {
			$data['option'][$row['optionID']] = $row['optionValue'];
		}

		$data['grantAll'] = true;

		foreach ($optionsIDs as $optionID) {
			if (!isset($data['option'][$optionID]) || $data['option'][$optionID] !== 1) {
				$data['grantAll'] = false;
				break;
			}
		}

		$data['denyAll'] = true;

		foreach ($optionsIDs as $optionID) {
			if (!isset($data['option'][$optionID]) || $data['option'][$optionID] !== 0) {
				$data['denyAll'] = false;
				break;
			}
		}

	}

	/**
	 * Returns a list of options by object type id.
	 * 
	 * @param	integer		$objectTypeID
	 * @param	string		$categoryName
	 * @return	ACLOptionList
	 */
	public function getOptions($objectTypeID, $categoryName = '') {
		$optionList = new ACLOptionList();
		if (!empty($categoryName)) {
			if (StringUtil::endsWith($categoryName, '.*')) {
				$categoryName = mb_substr($categoryName, 0, -1) . '%';
				$optionList->getConditionBuilder()->add("acl_option.categoryName LIKE ?", [$categoryName]);
			}
			else {
				$optionList->getConditionBuilder()->add("acl_option.categoryName = ?", [$categoryName]);
			}
		}
		$optionList->getConditionBuilder()->add("acl_option.objectTypeID = ?", [$objectTypeID]);
		$optionList->readObjects();
		
		return $optionList;
	}


	/**
	 * Replaces values for given type and object.
	 * 
	 * @param	ACLOptionList	$optionList
	 * @param	integer		$objectID
	 */
	protected function replaceValues(ACLOptionList $optionList, $objectID) {
		$options = $optionList->getObjects();
		
		// remove previous values
		$conditions = new PreparedStatementConditionBuilder();
		$conditions->add("optionID IN (?)", [array_keys($options)]);
		$conditions->add("objectID = ?", [$objectID]);
		
		$sql = "DELETE FROM	wcf".WCF_N."_acl_option_to_secret
			   ".$conditions;
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($conditions->getParameters());
		
		// add new values if given
		if (!isset($_POST['aclValues'])) {
			return;
		}
		
		$sql = "INSERT INTO	wcf".WCF_N."_acl_option_to_secret
					(optionID, objectID, optionValue)
					VALUES		(?, ?, ?)";
		$statement = WCF::getDB()->prepareStatement($sql);
		$values =& $_POST['aclValues'];
		
		WCF::getDB()->beginTransaction();

		foreach ($values as $optionID => $optionValue) {
			if (!isset($options[$optionID])) {
				continue;
			}
			
			$statement->execute([
				$optionID,
				$objectID,
				$optionValue
			]);
		}
		WCF::getDB()->commitTransaction();
	}
	
	/**
	 * Saves acl for a given object.
	 * 
	 * @param	integer		$objectID
	 * @param	integer		$objectTypeID
	 */
	public function save($objectID, $objectTypeID) {
		// get options
		$optionList = ACLOption::getOptions($objectTypeID);
		
		$this->replaceValues($optionList, $objectID);
	}

	/**
	 * Returns the permissions for given user.
	 * 
	 * @param	ApiSecret	$apiSecret
	 */
	public function loadPermissions(ApiSecret $apiSecret) {
		/*$sql = "SELECT	option_to_api_secret.objectID AS boardID, option_to_api_secret.optionValue,
						acl_option.optionName AS permission, acl_option.categoryName
				FROM	wcf".WCF_N."_acl_option acl_option,
						wcf".WCF_N."_acl_simple_to_api_secret option_to_api_secret
				WHERE	acl_option.objectTypeID = ?
						AND n.optionID = acl_option.optionID";

		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([
			ACLHandler::getInstance()->getObjectTypeID('at.megathorx.wsc_api.apiSecret')
		]);*/
		

		$sql = "SELECT	acl_option.optionName AS permission, acl_option.categoryName
				FROM	wcf".WCF_N."_acl_option acl_option
				WHERE	acl_option.objectTypeID = ?";

		$statement = WCF::getDB()->prepareStatement($sql);

		$statement->execute([
			ACLHandler::getInstance()->getObjectTypeID('at.megathorx.wsc_api.apiSecret')
		]);
		

		while ($row = $statement->fetchArray()) {
			/*
			if (StringUtil::startsWith($row['categoryName'], 'user.')) {
				$userPermissions[$row['boardID']][$row['permission']] = $row['optionValue'];
			}
			else {
				$moderatorPermissions[$row['boardID']][$row['permission']] = $row['optionValue'];
			}*/
			var_dump($row);
			echo "<br>";
		}
		die();
		/*
		return;
		// get user permissions
		if ($user->userID) {
			$data = UserStorageHandler::getInstance()->getField('wbbBoardPermissions', $user->userID);
			
			// cache does not exist or is outdated
			if ($data === null) {
				$moderatorPermissions = $userPermissions = [];
				
				$sql = "SELECT	option_to_user.objectID AS boardID, option_to_user.optionValue,
						acl_option.optionName AS permission, acl_option.categoryName
					FROM	wcf".WCF_N."_acl_option acl_option,
						wcf".WCF_N."_acl_option_to_user option_to_user
					WHERE	acl_option.objectTypeID = ?
						AND option_to_user.optionID = acl_option.optionID
						AND option_to_user.userID = ?";
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute([
					ACLHandler::getInstance()->getObjectTypeID('com.woltlab.wbb.board'),
					$user->userID
				]);
				while ($row = $statement->fetchArray()) {
					if (StringUtil::startsWith($row['categoryName'], 'user.')) {
						$userPermissions[$row['boardID']][$row['permission']] = $row['optionValue'];
					}
					else {
						$moderatorPermissions[$row['boardID']][$row['permission']] = $row['optionValue'];
					}
				}
				
				if (!empty($userPermissions)) {
					Board::inheritPermissions(null, $userPermissions);
				}
				if (!empty($moderatorPermissions)) {
					Board::inheritPermissions(null, $moderatorPermissions);
				}
				
				// update storage data
				UserStorageHandler::getInstance()->update($user->userID, 'wbbBoardPermissions', serialize([
					'mod' => $moderatorPermissions,
					'user' => $userPermissions
				]));
			}
			else {
				$tmp = unserialize($data);
				$moderatorPermissions = $tmp['mod'];
				$userPermissions = $tmp['user'];
			}
			
			foreach ($userPermissions as $boardID => $permissions) {
				foreach ($permissions as $name => $value) {
					$this->boardPermissions[$user->userID][$boardID][$name] = $value;
				}
			}
			
			foreach ($moderatorPermissions as $boardID => $permissions) {
				foreach ($permissions as $name => $value) {
					$this->moderatorPermissions[$user->userID][$boardID][$name] = $value;
				}
			}
		}*/
	}
}
