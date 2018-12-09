<?php
namespace wcf\api;

use wcf\data\user\UserAction;
use wcf\data\user\group\UserGroup;
use wcf\system\WCF;
use wcf\system\exception\UserInputException;
use wcf\system\user\authentication\UserAuthenticationFactory;
use wcf\system\user\notification\object\ApiNotificationUserNotificationObject;
use wcf\util\UserUtil;
use wcf\util\StringUtil;
use wcf\system\exception\ApiException;
use wcf\data\user\User;
use wcf\system\user\notification\UserNotificationHandler;
use wcf\data\ApiNotificationEditor;
/*
https://github.com/WoltLab/WCF/blob/master/wcfsetup/install/files/lib/form/SettingsForm.class.php
cleaner update of options with error validation
*/

/**
 * @author 	Robert Bitschnau
 * @package	at.megathorx.wsc-api
 */
class UserApi extends BaseApi {

	/**
	 * Allowed methods
	 * @var string[]
	 */
    public $allowedMethods = ['create', 'login', 'get', 'update', 'notification'];

    public function create() {
        $this->checkPermission('user.canCreateUser');
		$username = (isset($_REQUEST['username'])) ? StringUtil::trim($_REQUEST['username']) : null;
		$password = (isset($_REQUEST['password'])) ? StringUtil::trim($_REQUEST['password']) : null;
		$email = (isset($_REQUEST['email'])) ? StringUtil::trim($_REQUEST['email']) : null;

        if (empty($username)) {
			throw new ApiException('username is missing', 400);
		} else if (!UserUtil::isValidUsername($username)) { // check for forbidden chars (e.g. the ",")
			throw new ApiException('username is invalid', 412);
		} else if (!UserUtil::isAvailableUsername($username)) { // Check if username exists already.
			throw new ApiException('username is not notUnique', 412);
        }
        
        if (empty($password)) {
			throw new ApiException('password is missing', 400);
        }
        
        if (empty($email)) {
			throw new ApiException('email is missing', 400);
        } else if (!UserUtil::isValidEmail($email)) {
			throw new ApiException('email is invalid', 412);
		} else if (!UserUtil::isAvailableEmail($email)) {
			throw new ApiException('email is not notUnique', 412);
        }

        $languageID = WCF::getLanguage()->languageID;
        
        $data = [
			'data' => [
				'username' => $username,
				'email' => $email,
                'password' => $password,
                'languageID' => $languageID
			],
			'addDefaultGroups' => true
        ];
        
        $user = new UserAction([], 'create', $data);
        $result = $user->executeAction();

        return $this->get($result['returnValues']->userID);
    }
    
    public function login() {
        $this->checkPermission('user.canLoginUser');
        $username = (isset($_REQUEST['username'])) ? StringUtil::trim($_REQUEST['username']) : null;
        $password = (isset($_REQUEST['password'])) ? StringUtil::trim($_REQUEST['password']) : null;

        if (empty($username)) {
			throw new ApiException('username is missing', 400);
        }

        if (empty($password)) {
			throw new ApiException('password is missing', 400);
        }

        if (strpos($username, "@")) {
            $username = User::getUserByEmail($username) -> username;
        }
        
		try {
			$user = UserAuthenticationFactory::getInstance()->getUserAuthentication()->loginManually($username, $password);
			return $this->get($user->userID);
		} catch(UserInputException $e) {
            throw new ApiException('Invalid credentials', 412);
		}
    }
    
    public function get($userID = null) {
        $this->checkPermission('user.canFetchUserData');

        $userIDs = [];
        $requestMultiple = false;

        if (empty($userID)) {
            $userID = $_REQUEST['userID'];

            if (is_array($userID)) {
                $requestMultiple = true;
                foreach ($userID as $user) {
                    $user = StringUtil::trim($user);
                    if (empty($user)) {
                        throw new ApiException('userID is missing', 400);
                    } else if (!is_numeric($user)) {
                        throw new ApiException('userID is invalid', 412);
                    }
                    array_push($userIDs, $user);
                }
            } else {
                $userID = StringUtil::trim($userID);
                if (empty($userID)) {
                    throw new ApiException('userID is missing', 400);
                } else if (!is_numeric($userID)) {
                    throw new ApiException('userID is invalid', 412);
                }
                $userIDs = [$userID];
            }
        } else {
            if (is_array($userID)) {
                $requestMultiple = true;
                $userIDs = $userID;
            } else {
                $userIDs = [$userID];
            }
        }    
        
        $users = User::getUsers($userIDs);

        if (sizeof($users) === 0) {
            throw new ApiException('userID is invalid', 412);
        }

        $lang = WCF::getLanguage();

        $response = [];

        foreach ($users as $index => $user) {
            $groupIDs = $user->getGroupIDs();
            $groups = UserGroup::getGroupsByIDs($groupIDs);
            
            $resultGroups = array();
            
            foreach ($groups as $id => $group) {
                array_push($resultGroups, array(
                    'groupID' => $id,
                    'groupName' => $lang->getDynamicVariable($group->groupName),
                    'groupType' => $group->groupType
                ));
            }
            
            $sql = "SELECT        user_option_value.*
                    FROM        wcf".WCF_N."_user_option_value user_option_value
                    WHERE        user_option_value.userID = ?";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute([$index]);

            $options = $statement->fetchArray();

            $response[$index] = [
                'userID' => $user->userID,
                'wscApiId' => $user->wscApiId,
                'username' => $user->username,
                'email' => $user->email,
                'options' => $options,
                'groups' => $resultGroups
            ];
        }


        if ($requestMultiple) {
            return $response;
        } else {
            return $response[$userIDs[0]];
        }
        
    }

	public function update() {
        $userID = (isset($_REQUEST['userID'])) ? StringUtil::trim($_REQUEST['userID']) : null;
        $username = (isset($_REQUEST['username'])) ? StringUtil::trim($_REQUEST['username']) : null;
        $wscApiId = (isset($_REQUEST['wscApiId'])) ? StringUtil::trim($_REQUEST['wscApiId']) : null;
        $data = [];

        if (empty($userID)) {
            throw new ApiException('userID is missing', 400);
        } else if (!is_numeric($userID)) {
            throw new ApiException('userID is invalid', 412);
        }
        

        $users = User::getUsers([$userID]);

        if (sizeof($users) !== 1) {
            throw new ApiException('userID is invalid', 412);
        }
        $user = $users[$userID];

        $sql = "SELECT        user_option_value.*
                FROM        wcf".WCF_N."_user_option_value user_option_value
                WHERE        user_option_value.userID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$userID]);

        $options = $statement->fetchArray();
        $newOptions = [];
        $modifiedOptions = false;

        foreach ($options as $key => $value) {
            if ($key === 'userID') { continue; }

            $numKey = str_replace('userOption', '', $key);
            $newOptions[$numKey] = $value;

            if (isset($_REQUEST[$key])) {
                $modifiedOptions = true;
                $newOptions[$numKey] = $_REQUEST[$key];
            }
        }

        if($modifiedOptions) {
            $this->checkPermission('user.canUpdateUserOptions');
        }
        
        if (empty($username) && empty($wscApiId) && !$modifiedOptions) {
			throw new ApiException('no value to change provided (username or wscApiId allowed or userOptionXX)', 400);
        }
        
        if (!empty($username)) {
            if (!UserUtil::isValidUsername($username)) { // check for forbidden chars (e.g. the ",")
                throw new ApiException('username is invalid', 412);
            } else if (!UserUtil::isAvailableUsername($username)) { // Check if username exists already.
                throw new ApiException('username is not notUnique', 412);
            }
            $this->checkPermission('user.canUpdateUserName');
            $data['username'] = $username;
        }
        
        if (!empty($wscApiId)) {
            if (!is_numeric($wscApiId)) {
                throw new ApiException('wscApiId is invalid', 412);
            }
            $this->checkPermission('user.canUpdateUserWscApiId');
            $data['wscApiId'] = $wscApiId;
        }

        $action = new UserAction([$users[$userID]], 'update', [
			'data' => $data,
            'options' => $newOptions
        ]);

        $action->executeAction();
        return $this->get($userID);
    }

    public function notification() {
        $this->checkPermission('user.canCreateNotification');
        
        $userID = (isset($_REQUEST['userID'])) ? StringUtil::trim($_REQUEST['userID']) : null;
        $title = (isset($_REQUEST['title'])) ? StringUtil::trim($_REQUEST['title']) : null;
        $message = (isset($_REQUEST['message'])) ? StringUtil::trim($_REQUEST['message']) : null;
        $url = (isset($_REQUEST['url'])) ? StringUtil::trim($_REQUEST['url']) : null;

        if (empty($userID)) {
            throw new ApiException('userID is missing', 400);
        } else if (!is_numeric($userID)) {
            throw new ApiException('userID is invalid', 412);
        } else if (empty($title)) {
            throw new ApiException('title is missing', 400);
        } else if (empty($message)) {
            throw new ApiException('message is missing', 400);
        } else if (empty($url)) {
            throw new ApiException('url is missing', 400);
        }
        
        $users = User::getUsers([$userID]);

        if (sizeof($users) !== 1) {
            throw new ApiException('userID is invalid', 412);
        }

		$notification = ApiNotificationEditor::create([
			'title' => $title,
            'message' => $message,
            'url' => $url,
            'time' => time()
        ]);
        
        UserNotificationHandler::getInstance()->fireEvent(
            'notification',
            'at.megathorx.wsc_api.api_notification',
            new ApiNotificationUserNotificationObject($notification),
            [$userID]
        );

        return 'success';
    }
}
