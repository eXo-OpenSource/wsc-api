<?php
namespace wcf\api;
use wcf\data\user\UserAction;
use wcf\data\user\group\UserGroup;
use wcf\system\WCF;
use wcf\system\exception\UserInputException;
use wcf\system\user\authentication\UserAuthenticationFactory;
use wcf\util\UserUtil;
use wcf\util\StringUtil;
use wcf\system\exception\ApiException;
use wcf\data\user\User;

/**
 * @author 	Robert Bitschnau
 * @package	at.megathorx.wsc-api
 */
class UserApi {

    public static function create() {
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

        return self::get($result['returnValues']->userID);
    }
    
    public static function login() {
		$username = (isset($_REQUEST['username'])) ? StringUtil::trim($_REQUEST['username']) : null;
        $password = (isset($_REQUEST['password'])) ? StringUtil::trim($_REQUEST['password']) : null;
        
        if (empty($username)) {
			throw new ApiException('username is missing', 400);
        }

        if (empty($password)) {
			throw new ApiException('password is missing', 400);
        }
        
		try {
			$user = UserAuthenticationFactory::getInstance()->getUserAuthentication()->loginManually($username, $password);
			return self::get($user->userID);
		} catch(UserInputException $e) {
            throw new ApiException('Invalid credentials', 412);
		}
    }
    
    
    public static function get($userID = null) {
        $userID = $userID ? $userID : ((isset($_REQUEST['userID'])) ? StringUtil::trim($_REQUEST['userID']) : null);
        
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
        $groupIDs = $user->getGroupIDs();

        $groups = UserGroup::getGroupsByIDs($groupIDs);
        $resultGroups = array();
        $lang = WCF::getLanguage();
        
        foreach ($groups as $id => $group) {
            array_push($resultGroups, array(
                'groupID' => $id,
                'groupName' => $lang->getDynamicVariable($group->groupName),
                'groupType' => $group->groupType
            ));
        }

        return array(
            'userID' => $user->userID,
            'username' => $user->username,
            'email' => $user->email,
            'groups' => $resultGroups
        );
    }

}
