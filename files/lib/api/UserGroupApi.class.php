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

        $userID = (isset($_REQUEST['userID'])) ? StringUtil::trim($_REQUEST['userID']) : null;
        $groupID = (isset($_REQUEST['groupID'])) ? StringUtil::trim($_REQUEST['groupID']) : null;
        
        if (empty($userID)) {
            throw new ApiException('userID is missing', 400);
        }

        if (!is_numeric($userID)) {
            throw new ApiException('userID is invalid', 412);
        }

        if (empty($groupID)) {
            throw new ApiException('groupID is missing', 400);
        }

        if (!is_numeric($groupID)) {
            throw new ApiException('groupID is invalid', 412);
        }

        $users = User::getUsers([$userID]);

        if (sizeof($users) !== 1) {
            if (!is_numeric($userID)) {
                throw new ApiException('UserID is invalid', 412);
            }
        }

        $groupIDs = $users[$userID]->getGroupIDs();
        $groupIDs = array_merge($groupIDs, array($groupID));
        
        $action = new UserAction([$users[$userID]], 'addToGroups', [
            'groups' => $groupIDs,
            'addDefaultGroups' => false
        ]);
        $action->executeAction();
        
        return (new UserApi())->get($userID);
    }


    public function remove() {
        $this->checkPermission('group.canGroupRemoveMember');
        $userID = (isset($_REQUEST['userID'])) ? StringUtil::trim($_REQUEST['userID']) : null;
        $groupID = (isset($_REQUEST['groupID'])) ? StringUtil::trim($_REQUEST['groupID']) : null;
        
        if (empty($userID)) {
            throw new ApiException('userID is missing', 400);
        }

        if (!is_numeric($userID)) {
            throw new ApiException('userID is invalid', 412);
        }

        if (empty($groupID)) {
            throw new ApiException('groupID is missing', 400);
        }

        if (!is_numeric($groupID)) {
            throw new ApiException('groupID is invalid', 412);
        }

        $users = User::getUsers([$userID]);

        if (sizeof($users) !== 1) {
            if (!is_numeric($userID)) {
                throw new ApiException('UserID is invalid', 412);
            }
        }

        $action = new UserAction([$users[$userID]], 'removeFromGroups', [
            'groups' => array($groupID),
            'addDefaultGroups' => false
        ]);
        $action->executeAction();
        
        return (new UserApi())->get($userID);
    }
}
