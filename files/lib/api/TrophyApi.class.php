<?php
namespace wcf\api;

use wcf\util\StringUtil;
use wcf\system\exception\ApiException;
use wcf\data\trophy\TrophyList;

/**
 * @author 	Robert Bitschnau
 * @package	at.megathorx.wsc-api
 */
class TrophyApi extends BaseApi {

	/**
	 * Allowed methods
	 * @var string[]
	 */
    public $allowedMethods = ['index', 'get'];

    public function index() {
        $this->checkPermission('trophy.canFetchTrophyData');

		$trophyList = new TrophyList();
        $trophyList->readObjects();

        $data = [];
        
        foreach ($trophyList as $trophy) {
            $category = $trophy->getCategory();
            array_push($data, [
                'trophyID' => $trophy->trophyID,
                'title' => $trophy->title,
                'description' => $trophy->description,
                'categoryID' => $trophy->categoryID,
                'category' => [
                    'categoryID' => $category->categoryID,
                    'title' => $category->title,
                    'description' => $category->description,
                    'isDisabled' => $category->isDisabled
                ],
                'type' => $trophy->type,
                'iconFile' => $trophy->iconFile,
                'iconName' => $trophy->iconName,
                'iconColor' => $trophy->iconColor,
                'badgeColor' => $trophy->badgeColor,
                'isDisabled' => $trophy->isDisabled,
                'awardAutomatically' => $trophy->awardAutomatically
            ]);  
        }

        return $data;
    }
    
    public function get($trophyID = null) {
        $this->checkPermission('trophy.canFetchTrophyData');

        $trophyID = $trophyID ? $trophyID : ((isset($_REQUEST['trophyID'])) ? StringUtil::trim($_REQUEST['trophyID']) : null);

        if (empty($trophyID)) {
            throw new ApiException('trophyID is missing', 400);
        }

		$trophyList = new TrophyList();
		$trophyList->getConditionBuilder()->add('trophyID = ?', [$trophyID]);
        $trophyList->readObjects();

        if (sizeof($trophyList) !== 1) {
            throw new ApiException('trophyID is invalid', 412);
        }

        $data = [];

        foreach ($trophyList as $trophy) {
            $category = $trophy->getCategory();
            $data = [
                'trophyID' => $trophy->trophyID,
                'title' => $trophy->title,
                'description' => $trophy->description,
                'categoryID' => $trophy->categoryID,
                'category' => [
                    'categoryID' => $category->categoryID,
                    'title' => $category->title,
                    'description' => $category->description,
                    'isDisabled' => $category->isDisabled
                ],
                'type' => $trophy->type,
                'iconFile' => $trophy->iconFile,
                'iconName' => $trophy->iconName,
                'iconColor' => $trophy->iconColor,
                'badgeColor' => $trophy->badgeColor,
                'isDisabled' => $trophy->isDisabled,
                'awardAutomatically' => $trophy->awardAutomatically
            ];
        }

        return $data;
    }
}
