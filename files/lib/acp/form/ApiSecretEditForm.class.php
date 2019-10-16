<?php
namespace wcf\acp\form;
use wcf\data\user\avatar\Gravatar;
use wcf\data\user\avatar\UserAvatar;
use wcf\data\user\avatar\UserAvatarAction;
use wcf\data\user\cover\photo\UserCoverPhoto;
use wcf\data\user\group\UserGroup;
use wcf\data\user\User;
use wcf\data\user\UserAction;
use wcf\data\user\UserEditor;
use wcf\data\user\UserProfileAction;
use wcf\form\AbstractForm;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\moderation\queue\ModerationQueueManager;
use wcf\system\WCF;
use wcf\util\StringUtil;
use wcf\data\ApiSecret;
use wcf\data\ApiSecretEditor;
use wcf\system\api\ApiSecretPermissionHandler;

/**
 * Shows the user edit form.
 *
 * @author	Marcel Werk
 * @copyright	2001-2018 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	WoltLabSuite\Core\Acp\Form
 */
class ApiSecretEditForm extends ApiSecretAddForm {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.wscApi.secrets';

	/**
	 * user editor object
	 * @var	ApiSecretEditor
	 */
	public $apiSecret;

	/**
	 * @inheritDoc
	 */
	public $neededPermissions = [];

	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		if (isset($_REQUEST['id'])) $this->secretID = intval($_REQUEST['id']);
		$apiSecret = new ApiSecret($this->secretID);
		if (!$apiSecret->secretID) {
			throw new IllegalLinkException();
		}

		$this->apiSecret = new ApiSecretEditor($apiSecret);
		/*if (!UserGroup::isAccessibleGroup($this->user->getGroupIDs())) {
			throw new PermissionDeniedException();
		}*/

		parent::readParameters();
    }

	/**
	 * @inheritDoc
	 */
	public function readFormParameters() {
		parent::readFormParameters();
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

		WCF::getTPL()->assign([
			'secretID' => $this->apiSecret->secretID,
			'action' => 'edit',
			'apiSecret' => $this->apiSecret
		]);
	}

	/**
	 * @inheritDoc
	 */
	public function save() {
		AbstractForm::save();

		$this->apiSecret->update([
			'secretKey' => $this->secretKey,
			'secretDescription' => $this->secretDescription
		]);

		$this->apiSecret = new ApiSecretEditor(new ApiSecret($this->apiSecret->secretID));

		ApiSecretPermissionHandler::getInstance()->save($this->apiSecret->secretID, $this->objectTypeID);

		WCF::getTPL()->assign('success', true);
	}

	/**
	 * @inheritDoc
	 */
	public function validate() {
		parent::validate();
	}
}
