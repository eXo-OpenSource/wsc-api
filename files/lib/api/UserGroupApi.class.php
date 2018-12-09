<?php
namespace wcf\api;
use wcf\data\user\UserAction;
use wcf\data\user\group\UserGroup;
use wcf\system\WCF;
use wcf\util\StringUtil;
use wcf\system\exception\ApiException;
use wcf\data\user\User;

/**
 * @author 	Robert Bitschnau
 * @package	at.megathorx.wsc-api
 */
class UserGroupApi extends BaseApi {

	/**
	 * Allowed methods
	 * @var string[]
	 */
    public $allowedMethods = ['add', 'remove', 'get'];

    public function get()
    {
        $this->checkPermission('group.canFetchGroupData');
        
        $groupID = (isset($_REQUEST['groupID'])) ? StringUtil::trim($_REQUEST['groupID']) : null;

        if (empty($groupID)) {
            throw new ApiException('groupID is missing', 400);
        }

        if (!is_numeric($groupID)) {
            throw new ApiException('groupID is invalid', 412);
        }

        $userGroup = UserGroup::getGroupByID($groupID);

		$data = [
			'groupID' => $userGroup->groupID,
			'groupName' => $userGroup->getTitle(),
            'members' => []
		];
		
		$sql = 'SELECT u.userID, u.username FROM wcf'.WCF_N.'_user_to_group g INNER JOIN wcf'.WCF_N.'_user u ON u.userID = g.userID WHERE g.groupID = ?';

		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([
			$groupID
		]);
		
		while ($row = $statement->fetchArray()) {
			array_push($data['members'], $row);
		}
		
        return $data;
    }


    public function add() {
        $this->checkPermission('group.canGroupAddMember');

        $groupID = (isset($_REQUEST['groupID'])) ? StringUtil::trim($_REQUEST['groupID']) : null;

        if (empty($groupID)) {
            throw new ApiException('groupID is missing', 400);
        }

        if (!is_numeric($groupID)) {
            throw new ApiException('groupID is invalid', 412);
        }

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
        }

        $users = User::getUsers($userIDs);

        if (sizeof($users) === 0) {
            throw new ApiException('userID is invalid', 412);
        }

        foreach ($users as $index => $user) {
            $groupIDs = $user->getGroupIDs();
            $groupIDs = array_merge($groupIDs, array($groupID));
            
            $action = new UserAction([$user], 'addToGroups', [
                'groups' => $groupIDs,
                'addDefaultGroups' => false
            ]);
            $action->executeAction();
        }
        
        if ($requestMultiple) {
            return (new UserApi())->get($userIDs);
        } else {
            return (new UserApi())->get($userIDs[0]);
        }
    }


    public function remove() {
        $this->checkPermission('group.canGroupRemoveMember');
        
        $groupID = (isset($_REQUEST['groupID'])) ? StringUtil::trim($_REQUEST['groupID']) : null;

        if (empty($groupID)) {
            throw new ApiException('groupID is missing', 400);
        }

        if (!is_numeric($groupID)) {
            throw new ApiException('groupID is invalid', 412);
        }

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
        }

        $users = User::getUsers($userIDs);

        if (sizeof($users) === 0) {
            throw new ApiException('userID is invalid', 412);
        }

        $action = new UserAction($users, 'removeFromGroups', [
            'groups' => array($groupID),
            'addDefaultGroups' => false
        ]);
        $action->executeAction();
        
        if ($requestMultiple) {
            return (new UserApi())->get($userIDs);
        } else {
            return (new UserApi())->get($userIDs[0]);
        }
    }
}
