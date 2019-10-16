<?php
namespace wcf\api;

use wcf\util\StringUtil;
use wcf\system\exception\ApiException;
use wcf\data\trophy\TrophyList;
use wcf\data\user\trophy\UserTrophyList;
use wcf\data\user\trophy\UserTrophyAction;
use wcf\data\user\User;

/**
 * @author 	Robert Bitschnau
 * @package	at.megathorx.wsc-api
 */
class UserTrophyApi extends BaseApi {

    /**
     * @api
     * @permission('trophy.canFetchTrophyData')
     */
    public function get($userTrophyID = null, $trophyID = null, $userID = null) {
        if (empty($userTrophyID) || empty($trophyID) || empty($userID)) {
            throw new ApiException('userTrophyID and trophyID and userID are required', 400);
        }

        $trophyList = new UserTrophyList();

        if (!empty($userTrophyID)) {
            $trophyList->getConditionBuilder()->add('userTrophyID = ?', [$userTrophyID]);
        }

        if (!empty($trophyID)) {
            $trophyList->getConditionBuilder()->add('trophyID = ?', [$trophyID]);
        }

        if (!empty($userID)) {
            $trophyList->getConditionBuilder()->add('userID = ?', [$userID]);
        }

        $trophyList->readObjects();

        if (sizeof($trophyList) === 0) {
            throw new ApiException('userTrophyID or trophyID or userID is invalid', 412);
        }

        $data = [];

        foreach ($trophyList as $trophy) {
            $userProfile = $trophy->getUserProfile();
            array_push($data, [
                'userTrophyID' => $trophy->userTrophyID,
                'trophyID' => $trophy->trophyID,
                'userID' => $trophy->userID,
                'user' => [
                    'userID' => $userProfile->userID,
                    'username' => $userProfile->username
                ],
                'time' => $trophy->time,
                'description' => $trophy->description,
                'useCustomDescription' => $trophy->useCustomDescription
            ]);
        }

        return $data;
    }

    /**
     * @api
     * @permission('trophy.canTrophyAddUser')
     */
    public function add($trophyID, $userID, $description = null)
    {
        if (empty($trophyID)) {
            throw new ApiException('trophyID is required', 400);
        }

        if (empty($userID)) {
            throw new ApiException('userID is required', 400);
        }

        $trophyList = new TrophyList();
        $trophyList->getConditionBuilder()->add('trophyID = ?', [$trophyID]);

        $trophyList->readObjects();

        if (sizeof($trophyList) === 0) {
            throw new ApiException('trophyID is invalid', 412);
        }

        $users = User::getUsers([$userID]);

        if (sizeof($users) === 0) {
            throw new ApiException('userID is invalid', 412);
        }

        $data = [
            'trophyID' => $trophyID,
            'userID' => $userID,
            'time' => TIME_NOW
        ];

        if (!empty($description)) {
            $data['description'] = $description;
            $data['useCustomDescription'] = 1;
        }

        (new UserTrophyAction([], 'create', [
            'data' => $data
        ]))->executeAction();

        return $this->get(null, $trophyID);
    }

    /**
     * @api
     * @permission('trophy.canTrophyRemoveUser')
     */
    public function remove($userTrophyID = null, $trophyID = null, $userID = null)
    {
        if (empty($userTrophyID) || (empty($trophyID) || empty($userID))) {
            throw new ApiException('userTrophyID or trophyID and userID are required', 400);
        }

        $trophyList = new UserTrophyList();

        if (!empty($userTrophyID)) {
            $trophyList->getConditionBuilder()->add('userTrophyID = ?', [$userTrophyID]);
        } else {
            $trophyList->getConditionBuilder()->add('trophyID = ?', [$trophyID]);
            $trophyList->getConditionBuilder()->add('userID = ?', [$userID]);
        }

        $trophyList->readObjects();

        if (sizeof($trophyList) === 0) {
            throw new ApiException('trophyID and userID is invalid', 412);
        }

		$userTrophyAction = new UserTrophyAction($trophyList->getObjects(), 'delete');
		$userTrophyAction->executeAction();

        return $this->get(null, $trophyID);
    }
}
