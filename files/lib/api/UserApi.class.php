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
use wcf\system\api\ApiSecretPermissionHandler;

/**
 * @author 	Robert Bitschnau
 * @package	at.megathorx.wsc-api
 */
class UserApi extends BaseApi {

    /**
     * @api
     * @permission('user.canCreateUser')
     */
    public function create($username, $password, $email) {
        if (empty($username)) {
			throw new ApiException('username is required', 400);
		} else if (!UserUtil::isValidUsername($username)) { // check for forbidden chars (e.g. the ",")
			throw new ApiException('username is invalid', 412);
		} else if (!UserUtil::isAvailableUsername($username)) { // Check if username exists already.
			throw new ApiException('username is not notUnique', 412);
        }

        if (empty($password)) {
			throw new ApiException('password is required', 400);
        }

        if (empty($email)) {
			throw new ApiException('email is required', 400);
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

    /**
     * @api
     * @permission('user.canLoginUser')
     */
    public function login($username, $password) {
        if (empty($username)) {
			throw new ApiException('username is required', 400);
        }

        if (empty($password)) {
			throw new ApiException('password is required', 400);
        }

        if (strpos($username, "@")) {
            $username = User::getUserByEmail($username)->username;
        }

		try {
			$user = UserAuthenticationFactory::getInstance()->getUserAuthentication()->loginManually($username, $password);
			return $this->get($user->userID);
		} catch(UserInputException $e) {
            throw new ApiException('Invalid credentials', 412);
		}
    }

    /**
     * @api
     * @permission('user.canFetchUserData')
     */
    public function get($userID = null) {
        $userIDs = [];
        $requestMultiple = false;

        if (is_array($userID)) {
            $requestMultiple = true;
            foreach ($userID as $user) {
                $user = StringUtil::trim($user);
                if (empty($user)) {
                    throw new ApiException('userID is required', 400);
                } else if (!is_numeric($user)) {
                    throw new ApiException('userID is invalid', 412);
                }
                array_push($userIDs, $user);
            }
        } else {
            $userID = StringUtil::trim($userID);
            if (empty($userID)) {
                throw new ApiException('userID is required', 400);
            } else if (!is_numeric($userID)) {
                throw new ApiException('userID is invalid', 412);
            }
            $userIDs = [$userID];
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

    /**
     * @api
     * @permission('user.canFetchUserData')
     */
    public function getByName($username) {
        if (empty($username)) {
            throw new ApiException('username is required', 400);
        } else if (!is_string($username)) {
            throw new ApiException('username is invalid', 412);
        }

        $user = User::getUserByUsername($username);

        if (!$user->userID) {
            throw new ApiException('username is invalid', 412);
        }

        $lang = WCF::getLanguage();

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
        $statement->execute([$user->userID]);

        $options = $statement->fetchArray();

        $response = [
            'userID' => $user->userID,
            'wscApiId' => $user->wscApiId,
            'username' => $user->username,
            'email' => $user->email,
            'options' => $options,
            'groups' => $resultGroups
        ];

        return $response;

    }

    /**
     * @api
     */
	public function update($userID, $username = null, $wscApiId = null) {
        $data = [];

        if (empty($userID)) {
            throw new ApiException('userID is required', 400);
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

            if (isset($_POST[$key])) {
                $modifiedOptions = true;
                $newOptions[$numKey] = $_POST[$key];
            }
        }

        if($modifiedOptions) {
            if (!ApiSecretPermissionHandler::getInstance()->getPermission($this->secretID, 'user.canUpdateUserOptions')) {
                throw new ApiException('Permission denied', 403);
            }
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
            if (!ApiSecretPermissionHandler::getInstance()->getPermission($this->secretID, 'user.canUpdateUserName')) {
                throw new ApiException('Permission denied', 403);
            }
            $data['username'] = $username;
            $data['lastUsernameChange'] = TIME_NOW;
            $data['oldUsername'] = $user->username;
        }

        if (!empty($wscApiId)) {
            if (!is_numeric($wscApiId)) {
                throw new ApiException('wscApiId is invalid', 412);
            }
            if (!ApiSecretPermissionHandler::getInstance()->getPermission($this->secretID, 'user.canUpdateUserWscApiId')) {
                throw new ApiException('Permission denied', 403);
            }
            $data['wscApiId'] = $wscApiId;
        }

        $action = new UserAction([$users[$userID]], 'update', [
			'data' => $data,
            'options' => $newOptions
        ]);

        $action->executeAction();
        return $this->get($userID);
    }

    /**
     * @api
     * @permission('user.canCreateNotification')
     */
    public function notification($userID, $title, $message, $url, $email = false) {
        if (empty($userID)) {
            throw new ApiException('userID is required', 400);
        } else if (!is_numeric($userID)) {
            throw new ApiException('userID is invalid', 412);
        } else if (empty($title)) {
            throw new ApiException('title is required', 400);
        } else if (empty($message)) {
            throw new ApiException('message is required', 400);
        } else if (empty($url)) {
            throw new ApiException('url is required', 400);
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

        $notificationClass = 'at.megathorx.wsc_api.api_notification';

        if ($email) {
            $notificationClass = 'at.megathorx.wsc_api.api_notification_email';
        }

        UserNotificationHandler::getInstance()->fireEvent(
            'notification',
            $notificationClass,
            new ApiNotificationUserNotificationObject($notification),
            [$userID]
        );

        return 'success';
    }
}
