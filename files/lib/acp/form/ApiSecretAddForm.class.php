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
use wcf\system\api\ApiSecretPermissionHandler;
use wcf\data\ApiSecretEditor;

/**
 * Shows the board add form.
 * 
 * @author	Alexander Ebert
 * @copyright	2001-2018 WoltLab GmbH
 * @license	WoltLab License <http://www.woltlab.com/license-agreement.html>
 * @package	WoltLabSuite\Forum\Acp\Form
 */
class ApiSecretAddForm extends AbstractForm {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.wscApi.secrets.add';

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
	 * object id
	 * @var	integer
	 */
	public $secretID = 0;
	
	/**
	 * secretKey
	 * @var	string
	 */
	public $secretKey = '';
	
	/**
	 * secretDescription
	 * @var	string
	 */
	public $secretDescription = '';
	
	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		parent::readParameters();
		$this->objectTypeID = ACLHandler::getInstance()->getObjectTypeID('at.megathorx.wsc_api.apiSecret');
	}
	
	/**
	 * @inheritDoc
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if (!empty($_POST['secretKey'])) {
			$this->secretKey = StringUtil::trim($_POST['secretKey']);
		} else {
			$this->secretKey = bin2hex(CryptoUtil::randomBytes(16));
		}

		if (!empty($_POST['secretDescription'])) {
			$this->secretDescription = StringUtil::trim($_POST['secretDescription']);
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function validate() {
		parent::validate();

		/*
		check if secret is available
		*/
		if (empty($this->secretDescription)) {
			throw new UserInputException('secretDescription', 'empty');
		}

		$sql = 'SELECT COUNT(*) AS C FROM wcf1_api_secret WHERE secretKey = ? AND secretID <> ?';

		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([
			$this->secretKey,
			$this->secretID
		]);
		
		$row = $statement->fetchArray();
		
		if (isset($row) && $row['C'] > 0) {
			throw new UserInputException('secretKey', 'alreadyUsed');
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function save() {
		parent::save();

		$apiSecret = ApiSecretEditor::create([
			'secretKey' => $this->secretKey,
			'secretDescription' => $this->secretDescription
		]);
		
		ApiSecretPermissionHandler::getInstance()->save($apiSecret->secretID, $this->objectTypeID);
		// ACLHandler::getInstance()->disableAssignVariables();
		
		$this->secretKey = '';
		$this->secretDescription = '';
		// show success message
		WCF::getTPL()->assign('success', true);
	}
	
	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();
	}
	
	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
        parent::assignVariables();
		$permissions = ApiSecretPermissionHandler::getInstance()->getPermissions($this->objectTypeID, [1], 'api.*', true);
		
		WCF::getTPL()->assign([
            'action' => 'add',
			'permissions' => $permissions,
			'secretKey' => $this->secretKey,
			'secretDescription' => $this->secretDescription
		]);
	}
}
